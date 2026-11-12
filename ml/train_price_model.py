#!/usr/bin/env python3
"""Train a resale-price model from verified, paid cash-on-pickup orders."""

import csv
import json
import os
import sys
import mysql.connector
from pathlib import Path

ROOT = Path(__file__).resolve().parent
DATASET = ROOT / "paid_sales_dataset.csv"
MODEL = ROOT / "model.json"
MINIMUM_SALES = 30


def solve(matrix, vector):
    """Solve a small square system using Gaussian elimination with pivoting."""
    size = len(vector)
    augmented = [list(matrix[row]) + [vector[row]] for row in range(size)]
    for column in range(size):
        pivot = max(range(column, size), key=lambda row: abs(augmented[row][column]))
        if abs(augmented[pivot][column]) < 1e-12:
            raise ValueError("Paid sales do not support a stable price model yet.")
        augmented[column], augmented[pivot] = augmented[pivot], augmented[column]
        divisor = augmented[column][column]
        augmented[column] = [value / divisor for value in augmented[column]]
        for row in range(size):
            if row == column:
                continue
            factor = augmented[row][column]
            augmented[row] = [
                current - factor * pivot_value
                for current, pivot_value in zip(augmented[row], augmented[column])
            ]
    return [augmented[row][-1] for row in range(size)]


def main():
    connection = mysql.connector.connect(
        host=os.getenv("HMD_DB_HOST", "127.0.0.1"),
        port=int(os.getenv("HMD_DB_PORT", "3306")),
        user=os.getenv("HMD_DB_USER", "root"),
        password=os.getenv("HMD_DB_PASSWORD", ""),
        database=os.getenv("HMD_DB_NAME", "web"),
    )
    try:
        cursor = connection.cursor()
        cursor.execute(
            """SELECT l.original_price, l.condition_score, l.age_months, o.price
               FROM hmd_orders o
               JOIN hmd_listings l ON l.id = o.listing_id
               WHERE o.payment_status = 'paid'
                 AND o.payment_method = 'cash_on_pickup'
               ORDER BY o.created_at DESC
               LIMIT 10000"""
        )
        rows = [(float(original), float(condition), int(age), float(price))
                for original, condition, age, price in cursor.fetchall()]
        cursor.close()
    finally:
        connection.close()

    if len(rows) < MINIMUM_SALES:
        sys.exit(
            f"Need at least {MINIMUM_SALES} verified paid cash-on-pickup orders to train the price model; "
            f"found {len(rows)}. Listings can still use the rule-based estimate."
        )

    with DATASET.open("w", newline="", encoding="utf-8") as dataset_file:
        writer = csv.writer(dataset_file)
        writer.writerow(["original_price", "condition_score", "age_months", "paid_price"])
        writer.writerows([[round(value, 2) for value in row] for row in rows])

    features = [[1.0, original, condition, age] for original, condition, age, _ in rows]
    targets = [fair for _, _, _, fair in rows]
    columns = len(features[0])
    gram = [[sum(row[i] * row[j] for row in features) for j in range(columns)] for i in range(columns)]
    projected = [sum(row[i] * target for row, target in zip(features, targets)) for i in range(columns)]
    coefficients = solve(gram, projected)

    MODEL.write_text(
        json.dumps(
            {
                "model": "ordinary_least_squares",
                "training_data": "verified paid marketplace orders",
                "sample_count": len(rows),
                "features": ["intercept", "original_price", "condition_score", "age_months"],
                "coefficients": coefficients,
            },
            indent=2,
        )
        + "\n",
        encoding="utf-8",
    )
    print(f"Trained on {len(rows)} verified paid orders; wrote {DATASET.name} and {MODEL.name}.")


if __name__ == "__main__":
    main()
