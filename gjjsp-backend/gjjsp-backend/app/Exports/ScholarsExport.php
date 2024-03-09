<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\YearlyScholarsSheet;
use App\Models\Scholar;

class ScholarsExport implements WithMultipleSheets
{
    protected $fromYear;
    protected $toYear;

    public function __construct($fromYear, $toYear)
    {
        $this->fromYear = $fromYear;
        $this->toYear = $toYear;
    }

    public function sheets(): array
{
    $sheets = [];

    if ($this->fromYear == 'all') {
        // The starting year of the school's record-keeping
        $startYear = 1990;
        $endYear = now()->year; // Current year

        // Add one to the end year if the current month is June or later
        $currentMonth = now()->month;
        if ($currentMonth >= 6) {
            $endYear++;
        }

        for ($year = $startYear; $year < $endYear; $year++) {
            $scholars = Scholar::activeDuringSchoolYear($year)->get(); // Use a scope to get the active scholars
            $sheets[] = new YearlyScholarsSheet($year, $scholars);
        }
    } else {
        // Adjust if specific "from" and "to" years are provided
        for ($year = $this->fromYear; $year <= $this->toYear; $year++) {
            $scholars = Scholar::activeDuringSchoolYear($year)->get(); // Use a scope to get the active scholars
            $sheets[] = new YearlyScholarsSheet($year, $scholars);
        }
    }

    return $sheets;
}


}
