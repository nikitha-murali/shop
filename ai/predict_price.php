<?php
/**
 * predict_price.php  (Person 3 -> hand to Person 1)
 *
 * Usage in any PHP page:
 *   require_once __DIR__ . '/ai/predict_price.php';
 *   $model = load_price_model(__DIR__ . '/ai/model.json');
 *   $predicted = predict_price($model, 'Textbook', 12, 4, 800);   // Rs
 *   $badge = deal_badge($asking_price, $predicted);
 *
 * NOTE: the model was trained on SYNTHETIC data.
 */

/** Read model.json once and return it as a PHP array (or null if missing/broken). */
function load_price_model(string $path): ?array
{
    if (!is_readable($path)) {
        return null;
    }
    $model = json_decode(file_get_contents($path), true);
    return is_array($model) ? $model : null;
}

/**
 * Predicted resale price in rupees.
 * price = original * exp(category_coef + age_coef*age + condition_coef*condition + intercept)
 * Returns null if the category is unknown or the model could not be loaded.
 */
function predict_price($model, string $category, int $age, int $condition, float $original): ?int
{
    if (!is_array($model) || $original <= 0) {
        return null;
    }
    if (!isset($model['category_coefs'][$category])) {
        return null;                         // unknown category
    }

    // Keep inputs in the range the model was trained on
    $age       = max(0, min(60, $age));
    $condition = max(1, min(5, $condition));

    $log_ratio = $model['intercept']
               + $model['category_coefs'][$category]
               + $model['age_coef'] * $age
               + $model['condition_coef'] * $condition;

    $ratio = exp($log_ratio);
    $ratio = max(0.03, min(0.95, $ratio));   // same limits as the training data

    return (int) round($original * $ratio);
}

/**
 * Badge shown on a listing, based on asking price vs predicted price.
 *   asking <= 90% of predicted  -> Great deal
 *   asking <= 115% of predicted -> Fair
 *   otherwise                   -> Overpriced
 * Values match listings.deal_badge.
 */
function deal_badge(float $asking, ?int $predicted): string
{
    if ($predicted === null || $predicted <= 0) {
        return 'Fair';                       // no prediction -> neutral
    }
    $r = $asking / $predicted;
    if ($r <= 0.90) {
        return 'Great deal';
    }
    if ($r <= 1.15) {
        return 'Fair';
    }
    return 'Overpriced';
}