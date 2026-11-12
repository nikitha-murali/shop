#!/usr/bin/env python3
"""Refresh simple same-course/category recommendations from active listings."""

import os
import mysql.connector


def main():
    connection = mysql.connector.connect(
        host=os.getenv("HMD_DB_HOST", "127.0.0.1"),
        port=int(os.getenv("HMD_DB_PORT", "3306")),
        user=os.getenv("HMD_DB_USER", "root"),
        password=os.getenv("HMD_DB_PASSWORD", ""),
        database=os.getenv("HMD_DB_NAME", "web"),
    )
    try:
        cursor = connection.cursor(dictionary=True)
        cursor.execute("SELECT id, category, course, asking_price FROM hmd_listings WHERE status = 'active'")
        listings = cursor.fetchall()
        recommendations = []
        for source in listings:
            candidates = [
                target for target in listings
                if target["id"] != source["id"]
                and (target["category"] == source["category"] or target["course"] == source["course"])
            ]
            candidates.sort(
                key=lambda target: (
                    target["category"] != source["category"],
                    abs(float(target["asking_price"]) - float(source["asking_price"])),
                    target["id"],
                )
            )
            for target in candidates[:4]:
                score = 1.0
                if target["category"] != source["category"]:
                    score -= 0.25
                gap = abs(float(target["asking_price"]) - float(source["asking_price"]))
                score -= min(0.5, gap / max(float(source["asking_price"]), 1) * 0.5)
                recommendations.append((source["id"], target["id"], max(0, score)))

        cursor.execute("DELETE FROM hmd_recommendations")
        if recommendations:
            cursor.executemany(
                "INSERT INTO hmd_recommendations (listing_id, similar_id, score) VALUES (%s, %s, %s)",
                recommendations,
            )
        connection.commit()
        print(f"Stored {len(recommendations)} recommendations.")
    finally:
        connection.close()


if __name__ == "__main__":
    main()
