<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholar;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ReportDataController extends Controller
{
    public function fetchReportData(Request $request)
    {
        $startDate = $request->input('startDate');
        $endDate = $request->input('endDate');
        $reportType = $request->input('reportType');

        $formattedStartDate = Carbon::parse($startDate)->format('m/d/Y H:i');
        $monthRange = $this->getMonthRangeBasedOnReportType($reportType);
        $year = (new Carbon($formattedStartDate))->format('Y');
        $data = $this->fetchData($startDate, $endDate);




        return response()->json([
            'reportType' => $this->getReportTitle($reportType),
            'monthRange' => $monthRange,
            'year' => $year,
            'totalRegions' => $data['totalRegions'],
            'totalProvinces' => $data['totalProvinces'],
            'totalCitiesMunicipalities' => $data['totalCitiesMunicipalities'],
            'regionData' => $data['regionData'],
            'provinceData' => $data['provinceData'],
            'cityMunicipalityData' => $data['cityMunicipalityData'],
            'projectPartnersData' => [
                'luzon' => $this->fetchProjectPartnersDataByIslandGroup('luzon'),
                'visayas' => $this->fetchProjectPartnersDataByIslandGroup('visayas'),
                'mindanao' => $this->fetchProjectPartnersDataByIslandGroup('mindanao'),
            ],

        ]);
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
            'filteredScholars' => $query,
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

}
