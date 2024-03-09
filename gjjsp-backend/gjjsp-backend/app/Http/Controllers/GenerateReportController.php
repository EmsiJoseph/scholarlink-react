<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholar;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GenerateReportController extends Controller
{
    public function generateReport(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $reportType = $request->input('reportType');

        $formattedStartDate = Carbon::parse($startDate)->format('m/d/Y H:i');


        // Convert report type to month range based on logic
        $monthRange = $this->getMonthRangeBasedOnReportType($reportType);

        // Extract year from startDate for report
        $year = (new Carbon($formattedStartDate))->format('Y');

        // Fetch data based on user input and calculated month range
        $data = $this->fetchData($startDate, $endDate);

        $tableHtmls = [
            'regionTableHtml' => $this->getRegionTableHtml($data['regionData']),
            'provinceTableHtml' => $this->getProvinceTableHtml($data['provinceData']),
            'cityMunicipalityTableHtml' => $this->getCityMunicipalityTableHtml($data['cityMunicipalityData']),
        ];

        // dd($data);
        // For each island group
        foreach (['luzon', 'visayas', 'mindanao'] as $islandGroup) {
            $projectPartnersData = $this->fetchProjectPartnersDataByIslandGroup($islandGroup);
            $tablesHtml[$islandGroup] = $this->getProjectPartnersTableHtml($projectPartnersData, ucfirst($islandGroup));
        }

        $scholarStatusData = $this->fetchScholarStatusData($startDate, $endDate);
        $scholarStatusTableHtml = $this->getScholarStatusTableHtml($scholarStatusData);

         // Generate an array of years for the report
        $years = $this->generateYearsArray(1990, $endDate);

        // Get the active scholars by year data
        $scholarsByYear = $this->getActiveScholarsByYear($years, $startDate, $endDate);
        // Generate the HTML for the Program Growth and Development table
         $programGrowthTableHtml = $this->getProgramGrowthTableHtml($scholarsByYear);




        // Generate the PDF report
        $pdf = PDF::loadView('report-template', [
            'reportType' => $this->getReportTitle($reportType),
            'monthRange' => $monthRange,
            'year' => $year, // Assuming you want the current year
            'totalRegions' => $data['totalRegions'],
            'totalProvinces' => $data['totalProvinces'],
            'totalCitiesMunicipalities' => $data['totalCitiesMunicipalities'],
            // Include the generated HTML strings for tables
            'regionTableHtml' => $tableHtmls['regionTableHtml'],
            'provinceTableHtml' => $tableHtmls['provinceTableHtml'],
            'cityMunicipalityTableHtml' => $tableHtmls['cityMunicipalityTableHtml'],
            'luzonTableHtml' => $tablesHtml['luzon'],
            'visayasTableHtml' => $tablesHtml['visayas'],
            'mindanaoTableHtml' => $tablesHtml['mindanao'],
            'scholarStatusTableHtml' => $scholarStatusTableHtml,
            'programGrowthTableHtml' => $programGrowthTableHtml,
        ]);

        // Optionally, customize the PDF output:
        // - $pdf->setPaper('a4'); // Set paper size
        // - $pdf->setTitle('My Report Title'); // Set PDF title

        // $fileName = $this->getReportTitle($reportType) . ' '$year. '.pdf';
        // Storage::put('reports/' . $fileName, $pdf->output());

        // // Return a JSON response with a URL to the stored PDF
        // return response()->json([
        //     'message' => 'Report generated successfully',
        //     'pdfUrl' => Storage::url('reports/' . $fileName),
        // ]);

        return $pdf->stream();
    }

    protected function fetchData($startDate, $endDate)
    {
        $query = Scholar::whereBetween('school_yr_started', [$startDate, $endDate])
            ->orWhereBetween('school_yr_graduated', [$startDate, $endDate]);

        // Calculate areas of coverage
        $totalRegions = $query->distinct('region_name')->count('region_name');
        $totalProvinces = $query->distinct('province_name')->count('province_name');
        $totalCitiesMunicipalities = $query->distinct('cities_municipalities_name')->count('cities_municipalities_name');

        // Prepare data for tables
        $regionData = $query->select('region_name', \DB::raw('count(*) as scholar_count'))
            ->groupBy('region_name')
            ->get()
            ->toArray();

        $provinceData = $query->select('province_name', \DB::raw('count(*) as scholar_count'))
            ->groupBy('province_name')
            ->get()
            ->toArray();

        $cityMunicipalityData = $query->select('cities_municipalities_name', \DB::raw('count(*) as scholar_count'))
            ->groupBy('cities_municipalities_name')
            ->get()
            ->toArray();

        return [
            'totalRegions' => $totalRegions,
            'totalProvinces' => $totalProvinces,
            'totalCitiesMunicipalities' => $totalCitiesMunicipalities,
            'regionData' => $regionData,
            'provinceData' => $provinceData,
            'cityMunicipalityData' => $cityMunicipalityData,
        ];
    }


    private function getMonthRangeBasedOnReportType($reportType)
    {
        switch ($reportType) {
            case '1':
                return 'January to June';
            case '2':
                return 'July to December';
            default:
                return 'Custom Report'; // Or handle invalid report type differently
        }
    }

    private function getReportTitle($reportType)
    {
        switch ($reportType) {
            case '1':
                return 'Mid-year Accomplishment Report';
            case '2':
                return 'Year-end Accomplishment Report';
            default:
                return 'Custom Accomplishment Report'; // Or handle invalid report type differently
        }
    }

    public function getRegionTableHtml($data)
    {
        $html = "<h3>Regions</h3><table border='1' style='width: 100%; border-collapse: collapse;'>";
        $html .= "<thead><tr><th style='border: 1px solid black; text-align: center;'>Region</th><th style='border: 1px solid black; text-align: center;'># of Scholars</th></tr></thead>";
        $html .= "<tbody>";
        $totalScholars = 0;
        foreach ($data as $row) {
            $totalScholars += $row['scholar_count'];
            $html .= "<tr><td style='border: 1px solid black; text-align: center;'>{$row['region_name']}</td><td style='border: 1px solid black; text-align: center;'>{$row['scholar_count']}</td></tr>";
        }
        // Add total row
        $html .= "<tr><td style='border: 1px solid black; text-align: center;'><strong>Total</strong></td><td style='border: 1px solid black; text-align: center;'><strong>{$totalScholars}</strong></td></tr>";
        $html .= "</tbody></table>";
        return $html;
    }


    public function getProvinceTableHtml($data)
    {
        $html = "<h3>Provinces</h3><table border='1' style='width: 100%; border-collapse: collapse;'>";
        $html .= "<thead><tr><th style='border: 1px solid black; text-align: center;'>Province</th><th style='border: 1px solid black; text-align: center;'># of Scholars</th></tr></thead>";
        $html .= "<tbody>";
        $totalScholars = 0;
        foreach ($data as $row) {
            $totalScholars += $row['scholar_count'];
            $html .= "<tr><td style='border: 1px solid black; text-align: center;'>{$row['province_name']}</td><td style='border: 1px solid black; text-align: center;'>{$row['scholar_count']}</td></tr>";
        }
        // Add total row
        $html .= "<tr><td style='border: 1px solid black; text-align: center;'><strong>Total</strong></td><td style='border: 1px solid black; text-align: center;'><strong>{$totalScholars}</strong></td></tr>";
        $html .= "</tbody></table>";
        return $html;
    }

    public function getCityMunicipalityTableHtml($data)
    {
        $html = "<h3>Cities/Municipalities</h3><table border='1' style='width: 100%; border-collapse: collapse;'>";
        $html .= "<thead><tr><th style='border: 1px solid black; text-align: center;'>City/Municipality</th><th style='border: 1px solid black; text-align: center;'># of Scholars</th></tr></thead>";
        $html .= "<tbody>";
        $totalScholars = 0;
        foreach ($data as $row) {
            $totalScholars += $row['scholar_count'];
            $html .= "<tr><td style='border: 1px solid black; text-align: center;'>{$row['cities_municipalities_name']}</td><td style='border: 1px solid black; text-align: center;'>{$row['scholar_count']}</td></tr>";
        }
        // Add total row
        $html .= "<tr><td style='border: 1px solid black; text-align: center;'><strong>Total</strong></td><td style='border: 1px solid black; text-align: center;'><strong>{$totalScholars}</strong></td></tr>";
        $html .= "</tbody></table>";
        return $html;
    }

    protected function cacheRegionGroupings()
    {
        $regions = Cache::remember('region_groupings', now()->addDay(), function () {
            $response = Http::get('https://psgc.gitlab.io/api/regions');
            return $response->successful() ? $response->json() : [];
        });

        // Transform the regions into a more accessible format
        $regionGroupings = [];
        foreach ($regions as $region) {
            $regionGroupings[$region['name']] = $region['islandGroupCode'];
        }

        return $regionGroupings;
    }

    protected function fetchProjectPartnersDataByIslandGroup($islandGroupCode)
    {
        $regionGroupings = $this->cacheRegionGroupings();
        $regions = array_keys(array_filter($regionGroupings, function ($code) use ($islandGroupCode) {
            return $code === $islandGroupCode;
        }));

        return Scholar::select('project_partners.project_partner_name', \DB::raw('COUNT(scholars.id) as scholar_count'))
            ->selectRaw('SUM(scholars.num_fam_mem) as num_fam_mem_total')
            ->join('project_partners', 'project_partners.id', '=', 'scholars.project_partner_id')
            ->whereIn('scholars.region_name', $regions)
            ->groupBy('project_partners.project_partner_name')
            ->get()
            ->toArray();
    }

    public function getProjectPartnersTableHtml($data, $islandGroup)
    {
        $totalScholars = 0;
        $totalFamilyMembers = 0;
        $html = "<h3>$islandGroup based Scholars</h3><table border='1' style='width: 100%; border-collapse: collapse;'>";
        $html .= "<thead><tr>
                    <th style='border: 1px solid black; text-align: center;'>Project Partner Name</th>
                    <th style='border: 1px solid black; text-align: center;'># of Scholars</th>
                    <th style='border: 1px solid black; text-align: center;'># of Family Members</th>
                </tr></thead>";
        $html .= "<tbody>";
        foreach ($data as $row) {
            $html .= "<tr>
                        <td style='border: 1px solid black; text-align: center;'>{$row['project_partner_name']}</td>
                        <td style='border: 1px solid black; text-align: center;'>{$row['scholar_count']}</td>
                        <td style='border: 1px solid black; text-align: center;'>{$row['num_fam_mem_total']}</td>
                    </tr>";
                    $totalScholars += $row['scholar_count'];
                    $totalFamilyMembers += $row['num_fam_mem_total'];
        }
        $html .= "<tr>
        <th style='border: 1px solid black; text-align: center;'>Total</th>
        <th style='border: 1px solid black; text-align: center;'>{$totalScholars}</th>
        <th style='border: 1px solid black; text-align: center;'>{$totalFamilyMembers}</th>
        </tr>";
        $html .= "</tbody></table>";
        return $html;
    }


    protected function fetchScholarStatusData($startDate, $endDate) {


        $scholarStatusData = Scholar::select('scholar_status_id', \DB::raw('count(*) as total'))
            ->where('school_yr_started', '<=', $endDate)
            ->where('school_yr_graduated', '>=', $startDate)
            ->groupBy('scholar_status_id')
            ->with('scholarStatus') // Assuming you have a relation defined in Scholar model
            ->get();

        return $scholarStatusData;
    }


    public function getScholarStatusTableHtml($scholarStatusData) {
        $html = "<h2>Scholar Status Distribution</h2><table border='1' style='width: 100%; border-collapse: collapse;'>";
        $html .= "<thead><tr><th style='border: 1px solid black; text-align: center;'>Scholar Status</th><th style='border: 1px solid black; text-align: center;'># of Scholars</th><th style='border: 1px solid black; text-align: center;'>Percentage</th></tr></thead>";
        $html .= "<tbody>";

        $totalScholars = 0; // Initialize total scholars counter

        // First, calculate the total number of scholars to use for percentage calculations
        foreach ($scholarStatusData as $status) {
            $totalScholars += $status->total;
        }

        // Now, iterate again to build the table rows with percentages
        foreach ($scholarStatusData as $status) {
            $percentage = ($status->total / $totalScholars) * 100; // Calculate percentage
            $html .= "<tr><td style='border: 1px solid black; text-align: center;'>{$status->scholarStatus->scholar_status_name}</td><td style='border: 1px solid black; text-align: center;'>{$status->total}</td><td style='border: 1px solid black; text-align: center;'>".number_format($percentage, 2)."%</td></tr>";
        }

        // Add total row at the end
        $html .= "<tr><td style='border: 1px solid black; text-align: center;'><strong>Total</strong></td><td style='border: 1px solid black; text-align: center;'><strong>{$totalScholars}</strong></td><td style='border: 1px solid black; text-align: center;'><strong>100%</strong></td></tr>";

        $html .= "</tbody></table>";
        return $html;
    }

    protected function generateYearsArray($startYear, $endYear)
    {
        return range($startYear, $endYear);
    }

    protected function getActiveScholarsByYear($years, $startDate, $endDate)
    {
        $scholarsByYear = collect($years)->map(function($year) use ($startDate, $endDate) {
            // Count the number of active scholars for the year
            $count = Scholar::where('school_yr_started', '<=', $year)
                            ->where('school_yr_graduated', '>=', $year)
                            ->count();

            return (object)[
                'year' => $year,
                'total' => $count,
            ];
        });

        return $scholarsByYear;
    }



    public function getProgramGrowthTableHtml($scholarsByYear) {


        $html = "<h2>Program Growth and Development</h2>";
        $html .= "<table border='1' style='width: 100%; border-collapse: collapse;'>";
        $html .= "<thead><tr><th style='border: 1px solid black; text-align: center;'>Year</th><th style='border: 1px solid black; text-align: center;'>Number of Scholars</th><th style='border: 1px solid black; text-align: center;'>Percentage</th></tr></thead>";
        $html .= "<tbody>";

        $totalScholars = $scholarsByYear->sum('total'); // Total scholars for percentage calculation

        foreach ($scholarsByYear as $data) {
            $yearlyPercentage = ($data->total / $totalScholars) * 100;
            $html .= "<tr><td style='border: 1px solid black; text-align: center;'>{$data->year}</td><td style='border: 1px solid black; text-align: center;'>{$data->total}</td><td style='border: 1px solid black; text-align: center;'>".number_format($yearlyPercentage, 2)."%</td></tr>";
        }

        // Add total row at the end
        $html .= "<tr><td style='border: 1px solid black; text-align: center;'><strong>Total</strong></td><td style='border: 1px solid black; text-align: center;'><strong>{$totalScholars}</strong></td><td style='border: 1px solid black; text-align: center;'><strong>100%</strong></td></tr>";

        $html .= "</tbody></table>";

        return $html;
    }






}
