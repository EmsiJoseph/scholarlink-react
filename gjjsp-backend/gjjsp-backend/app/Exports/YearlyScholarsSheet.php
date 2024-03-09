<?php

namespace App\Exports;

use App\Models\Scholar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class YearlyScholarsSheet implements FromCollection, WithTitle, WithHeadings, WithStyles, WithMapping
{
    protected $year;
    protected $scholars;


    public function title(): string
    {
        return 'S.Y ' . $this->year . '-' . ($this->year + 1);
    }

    public function __construct($year, $scholars)
    {
        $this->year = $year;
        $this->scholars = $scholars;
    }

    public function collection()
    {
        // Return the filtered collection of scholars for the given year
        return $this->scholars->load('user');
    }

    public function map($scholar): array
{
    // Format the name as LASTNAME, FIRSTNAME MIDDLENAME
    $fullName = ucfirst(strtolower($scholar->user->last_name)) . ', ' .
                ucfirst(strtolower($scholar->user->first_name)) . ' ' .
                ucfirst(strtolower($scholar->user->middle_name));

// Calculate the academic year range
$academicYearStarted = 'S.Y ' . $scholar->school_yr_started . '-' . ($scholar->school_yr_started + 1);
$academicYearGraduated = 'S.Y ' . $scholar->school_yr_graduated . '-' . ($scholar->school_yr_graduated + 1);


$mobileNumber = $scholar->user->user_mobile_num;
if (substr($mobileNumber, 0, 2) === '63') {
    $mobileNumber = '0' . substr($mobileNumber, 2);
}

    return [
        // Add the full name as the first column
        $scholar->id,
        $fullName,
        // Add the email as the second column
        $scholar->user->email_address ?? '',
        // Add the mobile number as the third column, casted to string
        $mobileNumber,
        $scholar->scholarshipCateg->scholarship_categ_name ?? 'N/A', // Replace with the actual name
        $scholar->projectPartner->project_partner_name ?? 'N/A', // Replace with the actual name
        $scholar->scholar_status_name ?? 'N/A',
        $scholar->school->school_name ?? 'N/A', // Replace with the actual name
        $scholar->scholar_photo_filepath,
        $scholar->gender,
        $scholar->religion,
            $scholar->birthdate,
            $scholar->birthplace,
            $scholar->civil_status,
            $scholar->num_fam_mem,
            $academicYearStarted, // Modified school_yr_started
            $academicYearGraduated, // Modified school_yr_graduated
            $scholar->program,
            $scholar->home_visit_sched,
            $scholar->fb_account,
            $scholar->street,
            $scholar->zip_code,
            $scholar->region_name,
            $scholar->province_name,
            $scholar->cities_municipalities_name,
            $scholar->barangay_name,


        ];
    }

    public function headings(): array
    {
        return [
            'Scholar ID',
            'Scholar Name',
            'Email Address',
            'Contact #',
            'Scholarship Category',
            'Project Partner',
            'Scholar Status',
            'School',
            'Scholar Photo Filepath',
            'Gender',
            'Religion',
            'Birthdate',
            'Birthplace',
            'Civil Status',
            'Number of Family Members',
            'School Year Started',
            'School Year Graduated',
            'Program',
            'Home Visit Schedule',
            'Facebook Account',
            'Street',
            'Zip Code',
            'Region Name',
            'Province Name',
            'Cities/Municipalities Name',
            'Barangay Name',

        ];
    }

    // Styles in the exported excel
    public function styles(Worksheet $sheet)
{
    // Style array for header row - bold text, centered, with a solid background color
    $headerStyleArray = [
        'font' => [
            'bold' => true,
            'color' => ['argb' => 'FFFFFFFF'], // White text
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['argb' => 'FF4F81BD'], // Dark blue background
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FF000000'], // Black border
            ],
        ],
    ];

    // Apply style to header row
    $sheet->getStyle('A1:Z1')->applyFromArray($headerStyleArray);

    // Style array for cells - set font size and add border
    $cellStyleArray = [
        'font' => [
            'size' => 12,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => 'FFD0CECE'], // Grey border
            ],
        ],
    ];

    // Get the highest data row and column
    $highestRow = $sheet->getHighestDataRow();
    $highestColumn = $sheet->getHighestDataColumn();

    // Apply cell style to all data cells
    $sheet->getStyle('A2:' . $highestColumn . $highestRow)->applyFromArray($cellStyleArray);

    // Set auto size for all columns
    foreach (range('A', $highestColumn) as $column) {
        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    // Optionally set the height of the header row
    $sheet->getRowDimension(1)->setRowHeight(20);

    // Set the sheet title
    $sheet->setTitle($this->title());


    // Freeze the header row so it's not scrolled
    $sheet->freezePane('A2');

    // Optionally you can set the print area for the sheet
    $sheet->getPageSetup()->setPrintArea('A1:' . $highestColumn . $highestRow);

    // Set the page to landscape mode
    $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
}

}
