<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RiskScoringConfigSeeder extends Seeder
{
    public function run(): void
    {
        // Ne pas réinsérer ISO 27005 s'il existe déjà (la migration MONARC peut
        // avoir inséré une ligne sans qu'ISO 27005 soit présent).
        if (DB::table('risk_scoring_configs')->where('formula', 'probability_x_impact')->exists()) {
            return;
        }

        DB::table('risk_scoring_configs')->insert([
            'name'    => 'ISO 27005',
            'formula' => 'probability_x_impact',
            'is_active' => true,
            'probability_levels' => json_encode([
                ['value' => 1, 'label' => 'Rare',        'description' => ''],
                ['value' => 2, 'label' => 'Unlikely',    'description' => ''],
                ['value' => 3, 'label' => 'Possible',    'description' => ''],
                ['value' => 4, 'label' => 'Likely',      'description' => ''],
                ['value' => 5, 'label' => 'Very Likely', 'description' => ''],
            ]),
            'impact_levels' => json_encode([
                ['value' => 1, 'label' => 'Negligible', 'description' => ''],
                ['value' => 2, 'label' => 'Low',        'description' => ''],
                ['value' => 3, 'label' => 'Moderate',   'description' => ''],
                ['value' => 4, 'label' => 'High',       'description' => ''],
                ['value' => 5, 'label' => 'Critical',   'description' => ''],
            ]),
            'exposure_levels' => json_encode([
                ['value' => 0, 'label' => 'Offline',   'description' => ''],
                ['value' => 1, 'label' => 'Internal',  'description' => ''],
                ['value' => 2, 'label' => 'Internet',  'description' => ''],
            ]),
            'vulnerability_levels' => json_encode([
                ['value' => 1, 'label' => 'None',              'description' => ''],
                ['value' => 2, 'label' => 'Known',             'description' => ''],
                ['value' => 3, 'label' => 'Exploitable (int)', 'description' => ''],
                ['value' => 4, 'label' => 'Exploitable (ext)', 'description' => ''],
            ]),
            'risk_thresholds' => json_encode([
                ['level' => 'low',      'label' => 'Low',      'max' => 4,    'color' => '#27ae60'],
                ['level' => 'medium',   'label' => 'Medium',   'max' => 9,    'color' => '#f39c12'],
                ['level' => 'high',     'label' => 'High',     'max' => 16,   'color' => '#e74c3c'],
                ['level' => 'critical', 'label' => 'Critical', 'max' => null, 'color' => '#c0392b'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // MONARC — guard indépendant : la migration de rattrapage peut l'avoir déjà inséré
        if (DB::table('risk_scoring_configs')->where('formula', 'monarc')->exists()) {
            return;
        }

        DB::table('risk_scoring_configs')->insert([
            'name'       => 'MONARC',
            'formula'    => 'monarc',
            'is_active'  => false,
            'probability_levels' => json_encode([
                ['value' => 0, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.0.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.0.description')],
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.1.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.1.description')],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.2.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.2.description')],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.3.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.3.description')],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.monarc_threat_levels.4.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_threat_levels.4.description')],
            ]),
            'exposure_levels'      => null,
            'vulnerability_levels' => json_encode([
                ['value' => 0, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.0.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.0.description')],
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.1.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.1.description')],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.2.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.2.description')],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.3.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.3.description')],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.4.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.4.description')],
                ['value' => 5, 'label' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.5.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_vulnerability_levels.5.description')],
            ]),
            'impact_levels' => json_encode([
                ['value' => 0, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.0.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.0.description')],
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.1.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.1.description')],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.2.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.2.description')],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.3.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.3.description')],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.monarc_impact_levels.4.label'), 'description' => __('cruds.risk_scoring.defaults.monarc_impact_levels.4.description')],
            ]),
            'risk_thresholds' => json_encode([
                ['level' => 'low',      'label' => __('cruds.risk_scoring.defaults.risk_thresholds.low'),      'max' => 9,    'color' => '#3AB87A'],
                ['level' => 'medium',   'label' => __('cruds.risk_scoring.defaults.risk_thresholds.medium'),   'max' => 30,   'color' => '#E09B1A'],
                ['level' => 'critical', 'label' => __('cruds.risk_scoring.defaults.risk_thresholds.critical'), 'max' => null, 'color' => '#D94F45'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
