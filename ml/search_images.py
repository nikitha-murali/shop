#!/usr/bin/env python3
"""Find active dress listings whose image colors resemble an uploaded photo."""

import json
import os
import sys
from pathlib import Path

import mysql.connector
from PIL import Image, UnidentifiedImageError

ROOT = Path(__file__).resolve().parent.parent
UPLOADS = ROOT / "admin" / "upload"
MAX_PIXELS = 40_000_000


def image_vector(path_or_file):
    with Image.open(path_or_file) as source:
        if source.width * source.height > MAX_PIXELS:
            raise ValueError("Image dimensions are too large.")
        image = source.convert("RGB").resize((64, 64))
        values = []
        histogram = image.histogram()
        for channel in range(3):
            counts = histogram[channel * 256 : (channel + 1) * 256]
            buckets = [sum(counts[index : index + 16]) for index in range(0, 256, 16)]
            total = sum(buckets) or 1
            values.extend(value / total for value in buckets)
        return values


def distance(left, right):
    if len(left) != len(right):
        return float("inf")
    return sum((a - b) ** 2 for a, b in zip(left, right)) ** 0.5


def main():
    if len(sys.argv) != 2:
        raise ValueError("Expected the temporary uploaded image path.")

    try:
        query = image_vector(sys.argv[1])
    except (OSError, ValueError, UnidentifiedImageError) as error:
        raise ValueError("The uploaded file is not a supported image.") from error

    connection = mysql.connector.connect(
        host=os.getenv("HMD_DB_HOST", "127.0.0.1"),
        port=int(os.getenv("HMD_DB_PORT", "3306")),
        user=os.getenv("HMD_DB_USER", "root"),
        password=os.getenv("HMD_DB_PASSWORD", ""),
        database=os.getenv("HMD_DB_NAME", "web"),
        connection_timeout=5,
    )
    try:
        cursor = connection.cursor()
        cursor.execute(
            "SELECT l.id, l.image, v.`vector` FROM hmd_listings l "
            "LEFT JOIN hmd_image_vectors v ON v.listing_id = l.id "
            "WHERE l.status = 'active' AND l.category = 'Dresses'"
        )
        matches = []
        for listing_id, image_name, raw_vector in cursor.fetchall():
            candidate = None
            image_path = UPLOADS / Path(image_name).name
            if raw_vector:
                try:
                    candidate = json.loads(raw_vector)
                except (TypeError, json.JSONDecodeError):
                    candidate = None
            if not isinstance(candidate, list) or len(candidate) != len(query):
                if image_path.is_file():
                    try:
                        candidate = image_vector(image_path)
                    except (OSError, ValueError, UnidentifiedImageError):
                        if image_path.suffix.lower() != ".svg":
                            continue
            if isinstance(candidate, list) and len(candidate) == len(query):
                matches.append((int(listing_id), distance(query, candidate)))
            elif image_path.is_file() and image_path.suffix.lower() == ".svg":
                matches.append((int(listing_id), float("inf")))
        cursor.close()
        matches.sort(key=lambda result: result[1])
        print(json.dumps({"listing_ids": [listing_id for listing_id, _ in matches[:20]]}))
    finally:
        connection.close()


if __name__ == "__main__":
    try:
        main()
    except (mysql.connector.Error, ValueError) as error:
        print(str(error), file=sys.stderr)
        sys.exit(1)
