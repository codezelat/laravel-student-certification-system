<?php

namespace App\Exports;

use App\Models\Form;
use App\Models\Submission;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubmissionsExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function query()
    {
        return Submission::query()
            ->where('form_id', $this->form->id)
            ->latest();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Full Name',
            'Email',
            'Mobile',
            'Score',
            'Total Questions',
            'Percentage',
            'Submitted At',
        ];
    }

    public function map($submission): array
    {
        return [
            $submission->id,
            $submission->full_name,
            $submission->email,
            $submission->mobile,
            $submission->score,
            $submission->total_questions,
            $submission->score_percentage . '%',
            $submission->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }
}
