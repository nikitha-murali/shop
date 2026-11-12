#!/usr/bin/env python3
"""Train a clearly labelled sample price model from reproducible synthetic data."""

import json
from pathlib import Path

import numpy as np
import pandas as pd
from sklearn.linear_model import LinearRegression
from sklearn.model_selection import train_test_split


ROOT = Path(__file__).resolve().parent
DATASET = ROOT / "sample_dataset.csv"
MODEL = ROOT / "model.json"
RANDOM_SEED = 2026
CATEGORIES = ["Textbook", "Notes", "Electronics", "Hostel Essentials", "Cycle"]
CATEGORY_EFFECTS = {
    "Textbook": 0.00,
    "Notes": -0.04,
    "Electronics": -0.10,
    "Hostel Essentials": -0.07,
    "Cycle": -0.03,
}


def build_dataset():
    rng = np.random.default_rng(RANDOM_SEED)
    sample_count = 600
    categories = rng.choice(CATEGORIES, size=sample_count)
    original_prices = rng.integers(150, 30001, size=sample_count)
    conditions = rng.integers(1, 6, size=sample_count)
    ages = rng.integers(0, 61, size=sample_count)
    noise = rng.normal(0, 0.07, size=sample_count)

    log_price_ratios = (
        -0.19
        + 0.065 * conditions
        - 0.012 * ages
        + np.array([CATEGORY_EFFECTS[category] for category in categories])
        + noise
    )
    resale_prices = np.rint(original_prices * np.exp(log_price_ratios)).clip(1)
    return pd.DataFrame(
        {
            "category": categories,
            "original_price": original_prices,
            "condition_score": conditions,
            "age_months": ages,
            "resale_price": resale_prices,
            "data_source": "synthetic",
        }
    )


def model_features(dataset):
    features = dataset[["age_months", "condition_score"]].astype(float)
    category_features = pd.get_dummies(
        dataset["category"], prefix="category", drop_first=True, dtype=float
    )
    return pd.concat([features, category_features], axis=1)


def main():
    dataset = build_dataset()
    dataset.to_csv(DATASET, index=False)
    target = np.log(dataset["resale_price"] / dataset["original_price"])
    features = model_features(dataset)
    x_train, x_test, y_train, y_test = train_test_split(
        features, target, test_size=0.2, random_state=RANDOM_SEED
    )
    validation_model = LinearRegression().fit(x_train, y_train)
    validation_mae = float(
        np.mean(
            np.abs(
                dataset.loc[y_test.index, "original_price"].to_numpy()
                * np.exp(validation_model.predict(x_test))
                - dataset.loc[y_test.index, "resale_price"].to_numpy()
            )
        )
    )

    model = LinearRegression().fit(features, target)
    category_coefficients = {category: 0.0 for category in CATEGORIES}
    for feature_name, coefficient in zip(model.feature_names_in_, model.coef_):
        if feature_name.startswith("category_"):
            category_coefficients[feature_name.removeprefix("category_")] = float(
                coefficient
            )

    model_data = {
        "model": "linear_regression_log_price_ratio",
        "training_data": "synthetic sample data",
        "random_seed": RANDOM_SEED,
        "sample_count": int(len(dataset)),
        "features": ["age_months", "condition_score", "category"],
        "intercept": float(model.intercept_),
        "age_months": float(model.coef_[list(model.feature_names_in_).index("age_months")]),
        "condition": float(
            model.coef_[list(model.feature_names_in_).index("condition_score")]
        ),
        "category": category_coefficients,
        "validation": {
            "split": "80/20 random holdout",
            "mean_absolute_error_rupees": round(validation_mae, 2),
        },
    }
    MODEL.write_text(json.dumps(model_data, indent=2) + "\n", encoding="utf-8")
    print(
        f"Trained on {len(dataset)} synthetic rows; validation MAE "
        f"INR {validation_mae:,.2f}. Wrote {DATASET.name} and {MODEL.name}."
    )
    print("This is an illustrative model, not one trained on real marketplace sales.")


if __name__ == "__main__":
    main()
