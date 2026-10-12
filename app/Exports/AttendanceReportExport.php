<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/* Attendance report workbook: "Summary" sheet (per staff) + "Details" sheet (per day) */
class AttendanceReportExport implements WithMultipleSheets
{
    protected $report;

    function __construct(array $report) {
        $this->report = $report;
    }

    public function sheets(): array
    {
        return [
            new AttendanceReportSheet($this->report, 'summary'),
            new AttendanceReportSheet($this->report, 'details'),
        ];
    }
}
