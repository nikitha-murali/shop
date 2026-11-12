<?php
function predict_price(array $listing) {
    $original = (float) ($listing['original_price'] ?? 0);
    $condition = (float) ($listing['condition_score'] ?? 0);
    $age = (float) ($listing['age_months'] ?? 0);
    if ($original <= 0 || $condition < 1 || $condition > 5 || $age < 0) {
        throw new InvalidArgumentException('Cannot estimate price from invalid listing details.');
    }

    $modelPath = __DIR__ . '/../ml/model.json';
    $modelJson = is_file($modelPath) ? file_get_contents($modelPath) : false;
    $model = $modelJson === false ? null : json_decode($modelJson, true);
    if (is_file($modelPath) && (!$modelJson || !is_array($model))) {
        error_log('The price model file is unreadable or invalid; using the rule-based estimate.');
    }

    $hasSyntheticModel = is_array($model)
        && ($model['training_data'] ?? '') === 'synthetic sample data'
        && isset($model['intercept'], $model['age_months'], $model['condition'], $model['category'])
        && is_array($model['category'])
        && count(array_filter(
            [$model['intercept'], $model['age_months'], $model['condition']],
            static function ($coefficient) {
                return is_numeric($coefficient) && is_finite((float) $coefficient);
            }
        )) === 3;
    if ($hasSyntheticModel) {
        $category = trim((string) ($listing['category'] ?? ''));
        $categoryCoefficient = $model['category'][$category] ?? 0;
        if (!is_numeric($categoryCoefficient) || !is_finite((float) $categoryCoefficient)) {
            error_log('Ignoring an invalid category coefficient in the synthetic price model.');
            $hasSyntheticModel = false;
        } else {
            $logRatio = (float) $model['intercept']
                + (float) $model['age_months'] * $age
                + (float) $model['condition'] * $condition
                + (float) $categoryCoefficient;
            $fairPrice = $original * exp($logRatio);
            $modelSource = 'synthetic-data model (illustrative)';
        }
    }

    $hasPaidModel = is_array($model)
        && ($model['training_data'] ?? '') === 'verified paid marketplace orders'
        && (int) ($model['sample_count'] ?? 0) >= 30
        && isset($model['coefficients'])
        && count($model['coefficients']) === 4
        && count(array_filter(
            $model['coefficients'],
            static function ($coefficient) {
                return is_numeric($coefficient) && is_finite((float) $coefficient);
            }
        )) === 4;
    if (is_array($model) && !$hasSyntheticModel && !$hasPaidModel) {
        error_log('Ignoring an invalid or insufficient price model; using the rule-based estimate.');
    }

    if ($hasSyntheticModel) {
        $fairPrice = max(0, $fairPrice);
    } elseif ($hasPaidModel) {
        [$intercept, $priceCoefficient, $conditionCoefficient, $ageCoefficient] = $model['coefficients'];
        $fairPrice = max(0, $intercept + $original * $priceCoefficient
            + $condition * $conditionCoefficient + $age * $ageCoefficient);
        $modelSource = 'paid-sales model';
    } else {
        $category = strtolower(trim($listing['category'] ?? ''));
        $halfLifeMonths = [
            'books' => 48,
            'electronics' => 24,
            'furniture' => 60,
            'clothing' => 30,
            'sports' => 36,
        ][$category] ?? 36;
        $conditionFactor = 0.60 + ($condition * 0.08);
        $ageFactor = exp(-0.69314718056 * $age / $halfLifeMonths);
        $fairPrice = $original * $conditionFactor * $ageFactor;
        $modelSource = 'rule-based estimate';
    }

    $askingPrice = (float) ($listing['asking_price'] ?? $fairPrice);
    $badge = $askingPrice <= $fairPrice * 0.85
        ? 'Great deal'
        : ($askingPrice <= $fairPrice * 1.10 ? 'Fair' : 'Overpriced');

    return [
        'predicted_price' => round($fairPrice, 2),
        'deal_badge' => $badge,
        'model_source' => $modelSource,
    ];
}
