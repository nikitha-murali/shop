#!/usr/bin/env python3
"""Local Flask API for approximate color-histogram image search."""

import json
import os

import mysql.connector
from flask import Flask, jsonify, request
from PIL import Image

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = 5 * 1024 * 1024


def image_vector(file_obj):
    image = Image.open(file_obj).convert("RGB").resize((64, 64))
    values = []
    histogram = image.histogram()
    for channel in range(3):
        counts = histogram[channel * 256 : (channel + 1) * 256]
        buckets = [sum(counts[index : index + 16]) for index in range(0, 256, 16)]
        total = sum(buckets) or 1
        values.extend(value / total for value in buckets)
    return values


def distance(left, right):
    return sum((a - b) ** 2 for a, b in zip(left, right)) ** 0.5


@app.post("/search")
def search():
    uploaded = request.files.get("image")
    if uploaded is None:
        return jsonify(error="Provide an image file."), 400
    try:
        query = image_vector(uploaded.stream)
    except Exception:
        return jsonify(error="The uploaded file is not a supported image."), 400

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
            "SELECT v.listing_id, v.`vector` FROM hmd_image_vectors v "
            "JOIN hmd_listings l ON l.id = v.listing_id WHERE l.status = 'active'"
        )
        matches = []
        for listing_id, raw_vector in cursor.fetchall():
            candidate = json.loads(raw_vector)
            if len(candidate) == len(query):
                matches.append((listing_id, distance(query, candidate)))
        matches.sort(key=lambda result: result[1])
        return jsonify(listing_ids=[listing_id for listing_id, _ in matches[:20]])
    finally:
        connection.close()


if __name__ == "__main__":
    app.run(host=os.getenv("HMD_IMAGE_HOST", "127.0.0.1"), port=int(os.getenv("HMD_IMAGE_PORT", "8765")))
