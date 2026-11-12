"""
train_price_model.py
Trains a resale-price model on SYNTHETIC data and exports it to model.json.

Run:  python train_price_model.py
Needs: numpy, pandas, scikit-learn
"""
import json
import numpy as np
import pandas as pd
from sklearn.linear_model import LinearRegression
from sklearn.model_selection import train_test_split
from sklearn.metrics import r2_score

# ---------------------------------------------------------------
# 1. SYNTHETIC DATASET
# ---------------------------------------------------------------
rng = np.random.default_rng(42)   # fixed seed -> same data every run
N = 2000

# Share of value each category keeps when new (our assumption, not real data)
CATEGORY_BASE = {
    "Textbook":          0.50,
    "Notes":             0.30,
    "Electronics":       0.65,
    "Hostel Essentials": 0.45,
    "Cycle":             0.60,
}
# Typical new-price range (Rs) per category
PRICE_RANGE = {
    "Textbook":          (300, 2500),
    "Notes":             (50, 500),
    "Electronics":       (1500, 60000),
    "Hostel Essentials": (200, 5000),
    "Cycle":             (3000, 25000),
}

categories = list(CATEGORY_BASE.keys())
cat = rng.choice(categories, size=N)
age_months = rng.integers(1, 61, size=N)           # 1 to 60 months
condition = rng.integers(1, 6, size=N)             # 1 (poor) to 5 (like new)
original_price = np.array(
    [rng.uniform(*PRICE_RANGE[c]) for c in cat]
).round(0)

# The "true" rule we invent: ratio = base * exp(-a*age) * exp(b*(cond-3)) * noise
DECAY_PER_MONTH = 0.015      # value lost per month (exponential decay)
COND_EFFECT = 0.12           # each condition step changes log-value by 0.12
base = np.array([CATEGORY_BASE[c] for c in cat])
noise = rng.normal(0, 0.05, size=N)                # small random noise
ratio = base * np.exp(-DECAY_PER_MONTH * age_months
                      + COND_EFFECT * (condition - 3)
                      + noise)
ratio = np.clip(ratio, 0.03, 0.95)                 # resale never > 95% or < 3%
resale_price = (original_price * ratio).round(0)

df = pd.DataFrame({
    "category": cat,
    "original_price": original_price,
    "age_months": age_months,
    "condition": condition,
    "resale_price": resale_price,
})
df.to_csv("sample_dataset.csv", index=False)
print(f"Saved sample_dataset.csv with {len(df)} rows")

# ---------------------------------------------------------------
# 2. FEATURES AND TARGET
# ---------------------------------------------------------------
df["ratio"] = df["resale_price"] / df["original_price"]
y = np.log(df["ratio"])                            # model log(ratio)

# One-hot: one 0/1 column per category. We keep ALL categories (no drop)
# so each category gets its own coefficient in model.json.
X = pd.get_dummies(df[["category"]], prefix="cat", dtype=float)
X["age_months"] = df["age_months"]
X["condition"] = df["condition"]

# ---------------------------------------------------------------
# 3. TRAIN / TEST SPLIT AND FIT
# ---------------------------------------------------------------
X_train, X_test, y_train, y_test = train_test_split(
    X, y, test_size=0.2, random_state=42
)

# fit_intercept=False because the category columns already act as
# per-category intercepts (they sum to 1 on every row).
model = LinearRegression(fit_intercept=False)
model.fit(X_train, y_train)

r2 = r2_score(y_test, model.predict(X_test))
print(f"R2 score on test data (log-ratio): {r2:.4f}")

# ---------------------------------------------------------------
# 4. EXPORT TO model.json
# ---------------------------------------------------------------
coef = dict(zip(X.columns, model.coef_))
export = {
    "intercept": 0.0,   # absorbed into category coefficients (see notes)
    "age_coef": float(coef["age_months"]),
    "condition_coef": float(coef["condition"]),
    "category_coefs": {
        c.replace("cat_", ""): float(v)
        for c, v in coef.items() if c.startswith("cat_")
    },
    "r2": round(float(r2), 4),
    "note": "Trained on SYNTHETIC data. Prediction = original * exp(category + age_coef*age + condition_coef*condition + intercept).",
}
with open("model.json", "w") as f:
    json.dump(export, f, indent=2)
print("Saved model.json")
print(json.dumps(export, indent=2))