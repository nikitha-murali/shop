#!/usr/bin/env python3
"""Build normalized color-histogram descriptors for current listing photos."""

import json
import os
from pathlib import Path

import mysql.connector
from PIL import Image

ROOT = Path(__file__).resolve().parent.parent
UPLOADS = ROOT / "admin" / "upload"


def image_vector(path):
    with Image.open(path) as source:
        image = source.convert("RGB").resize((64, 64))
        values = []
        for channel in range(3):
            histogram = image.histogram()[channel * 256 : (channel + 1) * 256]
            buckets = [sum(histogram[index : index + 16]) for index in range(0, 256, 16)]
            total = sum(buckets) or 1
            values.extend(value / total for value in buckets)
        return values


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
        cursor.execute("SELECT id, image FROM hmd_listings WHERE status = 'active'")
        updated = 0
        for listing_id, image in cursor.fetchall():
            path = UPLOADS / Path(image).name
            if not path.is_file():
                continue
            if path.suffix.lower() == ".svg":
                print(f"Skipping vector generation for SVG listing image {path.name}.")
                continue
            vector = json.dumps(image_vector(path), separators=(",", ":"))
            cursor.execute(
                "INSERT INTO hmd_image_vectors (listing_id, `vector`) VALUES (%s, %s) "
                "ON DUPLICATE KEY UPDATE `vector` = VALUES(`vector`)",
                (listing_id, vector),
            )
            updated += 1
        connection.commit()
        print(f"Stored image descriptors for {updated} active listings.")
    finally:
        connection.close()


if __name__ == "__main__":
    main()
