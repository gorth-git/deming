<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Insère la configuration de scoring MONARC par défaut.
 *
 * Impact × Menace × Vulnérabilité — max 4 × 4 × 5 = 80.
 * La configuration est inactive par défaut ; l'administrateur l'active
 * manuellement via l'interface de scoring.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::table('risk_scoring_configs')->where('formula', 'monarc')->exists()) {
            return;
        }

        DB::table('risk_scoring_configs')->insert([
            'name'    => 'MONARC',
            'formula' => 'monarc',
            'is_active' => false,

            // Menace (vraisemblance) 0–4 — stockée dans probability_levels
            'probability_levels' => json_encode([
                ['value' => 0, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.0.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.0.description')],
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.1.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.1.description')],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.2.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.2.description')],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.3.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.3.description')],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.4.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.4.description')],
            ]),

            // Exposition : non utilisée pour MONARC
            'exposure_levels' => null,

            // Vulnérabilité 0–5
            'vulnerability_levels' => json_encode([
                ['value' => 0, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.0.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.0.description')],
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.1.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.1.description')],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.2.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.2.description')],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.3.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.3.description')],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.4.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.4.description')],
                ['value' => 5, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.5.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.5.description')],
            ]),

            // Impact 0–4
            'impact_levels' => json_encode([
                ['value' => 0, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.0.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.0.description')],
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.1.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.1.description')],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.2.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.2.description')],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.3.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.3.description')],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.4.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.4.description')],
            ]),

            // Seuils : 0–80, palette Deming
            'risk_thresholds' => json_encode([
                ['level' => 'low',      'label' => __('cruds.risk_scoring.defaults.risk_thresholds.low'),      'max' => 16,   'color' => '#3AB87A'],
                ['level' => 'medium',   'label' => __('cruds.risk_scoring.defaults.risk_thresholds.medium'),   'max' => 36,   'color' => '#E09B1A'],
                ['level' => 'high',     'label' => __('cruds.risk_scoring.defaults.risk_thresholds.high'),     'max' => 56,   'color' => '#E8731A'],
                ['level' => 'critical', 'label' => __('cruds.risk_scoring.defaults.risk_thresholds.critical'), 'max' => null, 'color' => '#D94F45'],
            ]),

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('risk_scoring_configs')->where('formula', 'monarc')->delete();
    }
};
