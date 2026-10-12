<?php

namespace App\Exports;

use App\Http\Controllers\ReportController;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AttendanceReportSheet implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize, WithStrictNullComparison
{
    protected $report;
    protected $kind;

    function __construct(array $report, string $kind) {
        $this->report = $report;
        $this->kind = $kind;
    }

    public function title(): string
    {
        return $this->kind === 'summary' ? 'Summary' : 'Details';
    }

    /* Report title + period on top, then the column headings */
    public function headings(): array
    {
        $columns = $this->kind === 'summary'
            ? ['User ID', 'Name', 'Mobile', 'Present Days', 'Leave Days', 'Permission Days', 'Permission Hours', 'Late Days', 'Late Hours', 'Worked Hours', 'Missing Punch Out']
            : ['Date', 'Day', 'User ID', 'Name', 'Shift', 'Punch In', 'Punch Out', 'Status', 'Worked Hours', 'Late By', 'Permission Hours', 'Reason'];

        return [
            [$this->report['title'].' - Vasantham Group Of Companies'],
            ['Period: '.$this->report['from']->format('d-m-Y').' to '.$this->report['to']->format('d-m-Y').' ('.$this->report['days'].' days)  |  Staff: '.$this->report['staff']],
            [],
            $columns,
        ];
    }

    public function array(): array
    {
        if ($this->kind === 'summary') {
            $rows = $this->report['summary']->map(fn ($s) => [
                $s['userId'], $s['name'], $s['mobile'], $s['present'], $s['leave'],
                $s['permission'], ReportController::hm($s['permissionMinutes']),
                $s['late'], ReportController::hm($s['lateMinutes']),
                ReportController::hm($s['workedMinutes']), $s['missingOut'],
            ])->all();

            $t = $this->report['totals'];
            $rows[] = ['', 'Total', '', $t['present'], $t['leave'], $t['permission'], '', $t['late'], '', ReportController::hm($t['workedMinutes']), ''];
            return $rows;
        }

        return $this->report['rows']->map(fn ($r) => [
            $r['date'], $r['day'], $r['userId'], $r['name'], $r['shift'], $r['punchIn'], $r['punchOut'], $r['status'],
            ReportController::hm($r['workedMinutes']), ReportController::hm($r['lateMinutes']),
            ReportController::hm($r['permissionMinutes']), $r['reason'],
        ])->all();
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9ECEF']]],
        ];
        if ($this->kind === 'summary') {
            $styles[$sheet->getHighestRow()] = ['font' => ['bold' => true]];
        }
        return $styles;
    }
}
