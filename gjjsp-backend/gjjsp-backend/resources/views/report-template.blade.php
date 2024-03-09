<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accomplishment Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 50px;
        }
    </style>


</head>
<body>
    <div class="header">
        <h2>ASSISI DEVELOPMENT FOUNDATION INC.</h2>
        <h3>SPECIAL PROJECT- GADO & JESS JALANDONI SCHOLARSHIP PROJECT</h3>
        <h4>{{ $reportType }}</h4>
        <p>From {{ $monthRange }} {{ $year }}</p>
    </div>

    <h2>Areas of Coverage</h2>
    <p>Regions – {{ $totalRegions }}</p>
    <p>Provinces – {{ $totalProvinces }}</p>
    <p>Cities/Municipalities – {{ $totalCitiesMunicipalities }}</p>

    @if (isset($regionTableHtml))
    {!! $regionTableHtml !!}
    @endif

    @if (isset($provinceTableHtml))
        {!! $provinceTableHtml !!}
    @endif

    @if (isset($cityMunicipalityTableHtml))
        {!! $cityMunicipalityTableHtml !!}
    @endif

    <br><br>

    <h2>
        Project Partners Report
    </h2>

    @if(isset($luzonTableHtml))
        <section>

            {!! $luzonTableHtml !!}
        </section>
    @endif

    @if(isset($visayasTableHtml))
        <section>

            {!! $visayasTableHtml !!}
        </section>
    @endif

    @if(isset($mindanaoTableHtml))
        <section>
            {!! $mindanaoTableHtml !!}
        </section>
    @endif


    <br><br>

    @if(isset($scholarStatusTableHtml))
        <section>
        {!! $scholarStatusTableHtml !!}
        </section>
    @endif

    <br><br>

    @if(isset($programGrowthTableHtml))
    <section>
        {!! $programGrowthTableHtml !!}
    </section>
    @endif


</body>
</html>



