"""
recommend.py
Finds the 5 most similar listings for every listing and stores them in the
`recommendations` table (listing_id, similar_id, score).

Run (XAMPP MySQL must be running):
    python recommend.py
Needs: pandas, scikit-learn, numpy, mysql-connector-python
"""
import numpy as np
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity

# ---------------- CHANGE THESE IF NEEDED ----------------
DB_HOST = "localhost"
DB_USER = "root"
DB_PASSWORD = ""            # XAMPP default is an empty password
DB_NAME = "web"             # your database name
TOP_N = 5                   # how many similar items to store per listing
# --------------------------------------------------------


def build_recommendations(df, top_n=TOP_N):
    """df needs columns: id, title, category, course, description, status.
    Returns a list of (listing_id, similar_id, score) tuples."""
    df = df.reset_index(drop=True)

    # 1. Combine the text fields of each listing into ONE string.
    #    fillna("") stops empty (NULL) fields from turning into the word "None".
    text = (
        df["title"].fillna("") + " "
        + df["category"].fillna("") + " "
        + df["course"].fillna("") + " "
        + df["description"].fillna("")
    )

    # 2. TF-IDF turns each text into a vector of numbers. Words that are
    #    rare and specific (e.g. "thermodynamics") count more than common
    #    words. stop_words removes "the", "and", "is"...
    vectorizer = TfidfVectorizer(stop_words="english", lowercase=True)
    matrix = vectorizer.fit_transform(text)

    # 3. Cosine similarity compares every listing with every other listing.
    #    Result: a square table; sim[i][j] is a score from 0 (nothing in
    #    common) to 1 (identical text).
    sim = cosine_similarity(matrix)

    available = (df["status"] == "available").to_numpy()
    ids = df["id"].to_numpy()
    rows = []

    for i in range(len(df)):
        scores = sim[i].copy()
        scores[i] = -1                  # an item is not similar to itself
        scores[~available] = -1         # only suggest items still for sale
        best = np.argsort(scores)[::-1][:top_n]   # indexes of highest scores
        for j in best:
            if scores[j] > 0:           # skip items with nothing in common
                rows.append((int(ids[i]), int(ids[j]), round(float(scores[j]), 4)))
    return rows


def main():
    import mysql.connector              # imported here so the logic above can be tested alone

    conn = mysql.connector.connect(
        host=DB_HOST, user=DB_USER, password=DB_PASSWORD, database=DB_NAME
    )
    cur = conn.cursor()

    # Read all listings
    cur.execute(
        "SELECT id, title, category, course, description, status FROM listings"
    )
    cols = ["id", "title", "category", "course", "description", "status"]
    df = pd.DataFrame(cur.fetchall(), columns=cols)
    print(f"Read {len(df)} listings from MySQL")

    if len(df) < 2:
        print("Need at least 2 listings to compare. Nothing written.")
        conn.close()
        return

    rows = build_recommendations(df)

    # Replace old recommendations with the fresh ones (one transaction).
    cur.execute("DELETE FROM recommendations")
    cur.executemany(
        "INSERT INTO recommendations (listing_id, similar_id, score) VALUES (%s, %s, %s)",
        rows,
    )
    conn.commit()
    print(f"Saved {len(rows)} recommendation rows to the recommendations table")
    conn.close()


if __name__ == "__main__":
    main()
    