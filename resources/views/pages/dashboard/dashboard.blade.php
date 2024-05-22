@extends('layouts.app')
@section('title', '| Dashboard  ')
@section('content')
<style>
    #sekolah, #kelurahan, #puskesmas {
        width: auto;
        height: auto;
    }
    </style>
<div class="page has-sidebar-left height-full">
    <header class="blue accent-3 relative nav-sticky">
        <div class="container-fluid text-white">
            <div class="row p-t-b-10 ">
                <div class="col">
                    <h4>
                        <i class="icon icon-dashboard2"></i> 
                        Dashboard | Menampilkan Data Tahun {{ $tahun->tahun }}
                    </h4>
                </div>
            </div>
        </div>
    </header>
    <div class="container-fluid relative animatedParent animateOnce">
        <div class="tab-content pb-3" id="v-pills-tabContent">
            <div class="tab-pane animated fadeInUpShort show active" id="v-pills-1">
                <div class="card no-b mt-3">
                    <form  action="{{ route('dashboard', 2021) }}" method="GET">
                        <div class="card-body p-0">
                            <div class="form-group row mb-2 mt-3">
                                <label for="tahun_id" class="col-form-label s-12 col-md-1 text-right font-weight-bold">Tahun : </label>
                                <div class="col-sm-2">
                                    <select name="tahun_id" id="tahun_id" class="select2 form-control r-0 light s-12">
                                        @foreach ($times as $i)
                                            <option value="{{ $i->id }}">{{ $i->tahun }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> 
                            <div class="form-group row">
                                <label for="tahun_id" class="col-form-label s-12 col-md-1 text-right font-weight-bold"></label>
                                <div class="col-sm-4">
                                    <button class="btn btn-sm btn-primary"><i class="icon-refresh mr-2"></i>Segarkan</button>
                                </div>
                            </div> 
                        </div>
                    </form>
                </div>
                @include('pages.dashboard.card3')
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mt-1">
                            <h6 class="card-header">Sekolah</h6>
                            <div class="card-body">
                                <div id="sekolah"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mt-1">
                            <h6 class="card-header">Kelurahan</h6>
                            <div class="card-body">
                                <div id="kelurahan"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mt-1">
                            <h6 class="card-header">Puskesmas</h6>  
                            <div class="card-body">
                                <div id="puskesmas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script type="text/javascript">
    $('#tahun_id').val("{{$tahun_id}}");
    $('#tahun_id').trigger('change.select2');

    am4core.ready(function() {
        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("sekolah", am4charts.PieChart);

        // Add data
        chart.data = [ {
            "data": "Belum Mengisi",
            "jumlah": {{ $sekolah - $sekolahKirim }}
        }, {
            "data": "Sudah Mengisi",
            "jumlah": {{ $sekolahKirim }}
        }];

        // Set inner radius
        chart.innerRadius = am4core.percent(50);

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "jumlah";
        pieSeries.dataFields.category = "data";
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;

        // This creates initial animation
        pieSeries.hiddenState.properties.opacity = 1;
        pieSeries.hiddenState.properties.endAngle = -90;
        pieSeries.hiddenState.properties.startAngle = -90;

    }); // end am4core.ready()

    am4core.ready(function() {
        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("kelurahan", am4charts.PieChart);

        // Add data
        chart.data = [ {
            "data": "Belum Mengisi",
            "jumlah": {{ $kelurahan - $kelurahanKirim }}
        }, {
            "data": "Sudah Mengisi",
            "jumlah": {{ $kelurahanKirim }}
        }];

        // Set inner radius
        chart.innerRadius = am4core.percent(50);

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "jumlah";
        pieSeries.dataFields.category = "data";
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;

        // This creates initial animation
        pieSeries.hiddenState.properties.opacity = 1;
        pieSeries.hiddenState.properties.endAngle = -90;
        pieSeries.hiddenState.properties.startAngle = -90;

    }); // end am4core.ready()

    am4core.ready(function() {
        // Themes begin
        am4core.useTheme(am4themes_animated);
        // Themes end

        // Create chart instance
        var chart = am4core.create("puskesmas", am4charts.PieChart);

        // Add data
        chart.data = [ {
            "data": "Belum Mengisi",
            "jumlah": {{ $puskesmas - $puskesmasKirim }}
        }, {
            "data": "Sudah Mengisi",
            "jumlah": {{ $puskesmasKirim }}
        }];

        // Set inner radius
        chart.innerRadius = am4core.percent(50);

        // Add and configure Series
        var pieSeries = chart.series.push(new am4charts.PieSeries());
        pieSeries.dataFields.value = "jumlah";
        pieSeries.dataFields.category = "data";
        pieSeries.slices.template.stroke = am4core.color("#fff");
        pieSeries.slices.template.strokeWidth = 2;
        pieSeries.slices.template.strokeOpacity = 1;

        // This creates initial animation
        pieSeries.hiddenState.properties.opacity = 1;
        pieSeries.hiddenState.properties.endAngle = -90;
        pieSeries.hiddenState.properties.startAngle = -90;

    }); // end am4core.ready()

</script>
@endsection
