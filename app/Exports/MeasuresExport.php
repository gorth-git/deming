<?php

namespace App\Exports;

use App\Models\Measure;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeasuresExport implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            trans('cruds.measure.fields.clause'),
            trans('cruds.measure.fields.name'),
            trans('cruds.measure.fields.scope'),
            trans('cruds.measure.fields.objective'),
            trans('cruds.measure.fields.attributes'),
            trans('cruds.measure.fields.input'),
            trans('cruds.measure.fields.model'),
            trans('cruds.measure.fields.indicator'),
            trans('cruds.measure.fields.plan_date'),
            trans('cruds.measure.fields.realisation_date'),
            trans('cruds.measure.fields.observations'),
            trans('cruds.measure.fields.score'),
            trans('cruds.measure.fields.note'),
            trans('cruds.measure.fields.owners'),
            trans('cruds.measure.fields.status'),
            trans('cruds.measure.fields.action_plan'),
        ];
    }

    public function styles(Worksheet $_sheet)
    {
        // return
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true],
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => 'top',
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Clause
            'B' => 30,  // Nom
            'C' => 20,  // Scope
            'D' => 50,  // Objectif
            'E' => 50,  // Attibuts
            'F' => 50,  // Input
            'G' => 50,  // Modele
            'H' => 50,  // Indicateur
            'I' => 15,  // Plan date
            'J' => 15,  // Realisation date
            'K' => 50,  // Observation
            'L' => 15,  // Score
            'M' => 15,  // Note
            'N' => 50,  // Responsibles
            'O' => 15,  // Status
            'P' => 50,  // Plan d'action
        ];
    }

    public function map($control): array
    {
        return [
            [
                $control->controls()->implode('clause', ', '),
                $control->name,
                $control->scope,
                $control->objective,
                $control->attributes,
                $control->input,
                $control->model,
                $control->indicator,
                $control->plan_date,
                $control->realisation_date,
                $control->observations,
                $control->score,
                $control->note,
                implode(
                    ', ',
                    array_filter(
                        [
                            $control->users()->implode('name', ', '),
                            $control->groups()->implode('name', ', '),
                        ]
                    )
                ),
                $control->status,
                $control->action_plan,
            ],
        ];
    }

    public function query(): Builder
    {
        return Measure::query()->orderBy('realisation_date');
    }
}
