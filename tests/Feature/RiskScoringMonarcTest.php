<?php

use App\Models\Risk;
use App\Models\RiskScoringConfig;
use App\Services\RiskScoringService;

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function monarcConfig(): RiskScoringConfig
{
    return RiskScoringConfig::create([
        'name'    => 'MONARC Test',
        'formula' => 'monarc',
        'is_active' => true,
        'probability_levels' => [
            ['value' => 0, 'label' => 'Non applicable', 'description' => ''],
            ['value' => 1, 'label' => 'Improbable',     'description' => ''],
            ['value' => 2, 'label' => 'Possible',       'description' => ''],
            ['value' => 3, 'label' => 'Probable',       'description' => ''],
            ['value' => 4, 'label' => 'Très probable',  'description' => ''],
        ],
        'exposure_levels'      => null,
        'vulnerability_levels' => [
            ['value' => 0, 'label' => 'Inexistante',  'description' => ''],
            ['value' => 1, 'label' => 'Très faible',  'description' => ''],
            ['value' => 2, 'label' => 'Faible',       'description' => ''],
            ['value' => 3, 'label' => 'Moyenne',      'description' => ''],
            ['value' => 4, 'label' => 'Élevée',       'description' => ''],
            ['value' => 5, 'label' => 'Très élevée',  'description' => ''],
        ],
        'impact_levels' => [
            ['value' => 0, 'label' => 'Négligeable', 'description' => ''],
            ['value' => 1, 'label' => 'Faible',      'description' => ''],
            ['value' => 2, 'label' => 'Important',   'description' => ''],
            ['value' => 3, 'label' => 'Critique',    'description' => ''],
            ['value' => 4, 'label' => 'Vital',       'description' => ''],
        ],
        'risk_thresholds' => [
            ['level' => 'low',      'label' => 'Faible',   'max' => 16,   'color' => '#3AB87A'],
            ['level' => 'medium',   'label' => 'Moyen',    'max' => 36,   'color' => '#E09B1A'],
            ['level' => 'high',     'label' => 'Élevé',    'max' => 56,   'color' => '#E8731A'],
            ['level' => 'critical', 'label' => 'Critique', 'max' => null, 'color' => '#D94F45'],
        ],
    ]);
}

function monarcRisk(int $impact, int $threat, int $vuln): Risk
{
    return Risk::make([
        'impact'        => $impact,
        'probability'   => $threat,
        'vulnerability' => $vuln,
        'status'        => 'not_evaluated',
        'review_frequency' => 12,
    ]);
}

// ---------------------------------------------------------------------------
// Formule de calcul
// ---------------------------------------------------------------------------

test('score MONARC = Impact × Menace × Vulnérabilité', function () {
    $config = monarcConfig();
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $risk    = monarcRisk(impact: 2, threat: 3, vuln: 4);

    expect($service->score($risk)['score'])->toBe(24);
});

test('score MONARC max = 4 × 4 × 5 = 80', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $risk    = monarcRisk(impact: 4, threat: 4, vuln: 5);

    expect($service->score($risk)['score'])->toBe(80);
});

test('score MONARC min = 0 quand impact = 0', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $risk    = monarcRisk(impact: 0, threat: 4, vuln: 5);

    expect($service->score($risk)['score'])->toBe(0);
});

test('score MONARC min = 0 quand menace = 0', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $risk    = monarcRisk(impact: 4, threat: 0, vuln: 5);

    expect($service->score($risk)['score'])->toBe(0);
});

test('score MONARC min = 0 quand vulnérabilité = 0', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $risk    = monarcRisk(impact: 4, threat: 4, vuln: 0);

    expect($service->score($risk)['score'])->toBe(0);
});

// ---------------------------------------------------------------------------
// maxScore()
// ---------------------------------------------------------------------------

test('maxScore MONARC = 80', function () {
    $config = monarcConfig();
    expect($config->maxScore())->toBe(80);
});

// ---------------------------------------------------------------------------
// Seuils (thresholdFor)
// ---------------------------------------------------------------------------

test('seuil score 0 → low', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(0)['level'])->toBe('low');
});

test('seuil score 16 → low', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(16)['level'])->toBe('low');
});

test('seuil score 17 → medium', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(17)['level'])->toBe('medium');
});

test('seuil score 36 → medium', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(36)['level'])->toBe('medium');
});

test('seuil score 37 → high', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(37)['level'])->toBe('high');
});

test('seuil score 56 → high', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(56)['level'])->toBe('high');
});

test('seuil score 57 → critical', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(57)['level'])->toBe('critical');
});

test('seuil score 80 → critical', function () {
    $config = monarcConfig();
    expect($config->thresholdFor(80)['level'])->toBe('critical');
});

// ---------------------------------------------------------------------------
// Axe X de la matrice : produits distincts Menace × Vulnérabilité
// ---------------------------------------------------------------------------

test('matrixXAxis MONARC retourne 21 colonnes (0 à 20)', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $xAxis   = $service->matrixXAxis();

    expect(count($xAxis))->toBe(21);
});

test('matrixXAxis MONARC retourne toutes les valeurs de 0 à 20', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service  = app(RiskScoringService::class);
    $products = array_column($service->matrixXAxis(), 'value');

    expect($products)->toBe(range(0, 20));
});

test('matrixXAxis MONARC est trié en ordre croissant', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service  = app(RiskScoringService::class);
    $products = array_column($service->matrixXAxis(), 'value');
    $sorted   = $products;
    sort($sorted);

    expect($products)->toBe($sorted);
});

// ---------------------------------------------------------------------------
// Axe Y de la matrice : niveaux d'impact
// ---------------------------------------------------------------------------

test('matrixYAxis MONARC retourne les niveaux d\'impact', function () {
    monarcConfig();
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $values  = array_column($service->matrixYAxis(), 'value');

    expect($values)->toBe([0, 1, 2, 3, 4]);
});

// ---------------------------------------------------------------------------
// usesMonarc()
// ---------------------------------------------------------------------------

test('usesMonarc() retourne true uniquement pour formula=monarc', function () {
    $monarc = monarcConfig();
    expect($monarc->usesMonarc())->toBeTrue();
    expect($monarc->usesLikelihood())->toBeFalse();
});

// ---------------------------------------------------------------------------
// Vérification que les tests existants ne régressent pas (formule ISO 27005)
// ---------------------------------------------------------------------------

test('formule ISO 27005 non affectée par MONARC', function () {
    $iso = RiskScoringConfig::create([
        'name'    => 'ISO Test',
        'formula' => 'probability_x_impact',
        'is_active' => true,
        'probability_levels' => [
            ['value' => 1, 'label' => 'Rare',   'description' => ''],
            ['value' => 5, 'label' => 'Likely', 'description' => ''],
        ],
        'impact_levels' => [
            ['value' => 1, 'label' => 'Low',  'description' => ''],
            ['value' => 5, 'label' => 'High', 'description' => ''],
        ],
        'risk_thresholds' => [
            ['level' => 'low',  'label' => 'Low',  'max' => 10,   'color' => '#27ae60'],
            ['level' => 'high', 'label' => 'High', 'max' => null, 'color' => '#e74c3c'],
        ],
    ]);
    RiskScoringConfig::clearCache();

    $service = app(RiskScoringService::class);
    $risk    = Risk::make(['probability' => 3, 'impact' => 3, 'status' => 'not_evaluated', 'review_frequency' => 12]);

    expect($service->score($risk)['score'])->toBe(9);
    expect($iso->usesMonarc())->toBeFalse();
    expect($iso->maxScore())->toBe(25);
});
