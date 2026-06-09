<?php

namespace App\Exports;

use App\Models\Control;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ControlsExport extends StringValueBinder implements FromQuery, WithMapping, WithHeadings, WithStyles, WithColumnWidths
{
    public function headings(): array
    {
        return [
            trans('cruds.domain.fields.framework'),
            trans('cruds.domain.title'),
            trans('cruds.domain.title') . ' - ' . trans('cruds.domain.fields.description'),
            trans('cruds.control.fields.clause'),
            trans('cruds.control.fields.name'),
            trans('cruds.control.fields.objective'),
            trans('cruds.control.fields.attributes'),
            trans('cruds.control.fields.input'),
            trans('cruds.control.fields.model'),
            trans('cruds.control.fields.indicator'),
            trans('cruds.control.fields.action_plan'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
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

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // Framework
            'B' => 10,  // Domain name
            'C' => 30,  // Domain description
            'D' => 10,  // Clause
            'E' => 30,  // Name
            'F' => 50,  // Objectif
            'G' => 50,  // Attibuts
            'H' => 50,  // Input
            'I' => 50,  // Modele
            'J' => 50,  // Indicateur
            'K' => 50,  // Plan d'action
        ];
    }

    public function map($measure): array
    {
        return [
            [
                $measure->domain->framework,
                $measure->domain->title,
                $measure->domain->description,
                $measure->clause,
                $measure->name,
                $measure->objective,
                $measure->attributes,
                $measure->input,
                $measure->model,
                $measure->indicator,
                $measure->action_plan,
            ],
        ];
    }

    public function query(): Builder
    {
        return Control::with('domain')->orderBy('clause');
    }
}
