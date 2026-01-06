@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <div>
            <a href="{{ route('transaction.report') }}" class="btn btn-info btn-sm">
                <i class="fas fa-list-alt"></i> Laporan Transaksi Detail
            </a>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Daily Income Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Pendapatan Harian (Hari Ini)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($dailyIncome, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Income Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Pendapatan Bulanan (Bulan Ini)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($monthlyIncome, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yearly Income Card -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Pendapatan Tahunan (Tahun Ini)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($yearlyIncome, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Date Range Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Generate Laporan Berdasarkan Tanggal</h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('report') }}" id="reportForm">
                        @csrf
                        <div class="form-group row">
                            <label for="period" class="col-sm-2 col-form-label">Periode</label>
                            <div class="col-sm-4">
                                <select class="form-control" id="period" name="period">
                                    <option value="" {{ !request('period') ? 'selected' : '' }}>Pilih Periode</option>
                                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini</option>
                                    <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>Minggu Ini</option>
                                    <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                                    <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                                    <option value="custom" {{ request('period') == 'custom' ? 'selected' : '' }}>Custom Range</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="customDateRange" style="{{ request('period') == 'custom' ? '' : 'display: none;' }}">
                            <label for="tanggal_awal" class="col-sm-2 col-form-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="tanggal_awal" name="tanggal_awal" value="{{ $startDate }}">
                            </div>
                            <label for="tanggal_akhir" class="col-sm-2 col-form-label">Tanggal Akhir</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="tanggal_akhir" name="tanggal_akhir" value="{{ $endDate }}">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Jenis PlayStation</label>
                            <div class="col-sm-10">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAllPlaystations()">Pilih Semua</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="resetPlaystations()">Reset</button>
                                </div>
                                <div class="row">
                                    @foreach($playstationTypes as $playstation)
                                        <div class="col-sm-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="playstation_types[]" value="{{ $playstation->id }}" id="ps_{{ $playstation->id }}" {{ in_array($playstation->id, $selectedPlaystationIds) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="ps_{{ $playstation->id }}">
                                                    {{ $playstation->nama }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <small class="form-text text-muted">Pilih jenis PlayStation yang ingin ditampilkan dalam laporan.</small>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-info mr-2">Tampilkan Laporan</button>
                                <button type="submit" class="btn btn-primary mr-2" formaction="{{ url('/generate-pdf') }}">Generate PDF</button>
                                <button type="submit" class="btn btn-success" formaction="{{ url('/generate-excel') }}">Generate Excel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($startDate && $endDate)
    <!-- Period Income and Chart -->
    <div class="row">
        <div class="col-xl-12 col-md-12 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Pendapatan Periode ({{ $startDate }} - {{ $endDate }})
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Rp {{ number_format($periodIncome, 0, ',', '.') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Grafik Statistik Pendapatan</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="incomeChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Custom Date Range based on Period selection
        const periodSelect = document.getElementById('period');
        const customDateRange = document.getElementById('customDateRange');
        const startDateInput = document.getElementById('tanggal_awal');
        const endDateInput = document.getElementById('tanggal_akhir');

        if (periodSelect) {
            function toggleCustomDate() {
                if (periodSelect.value === 'custom') {
                    customDateRange.style.display = 'flex';
                    startDateInput.required = true;
                    endDateInput.required = true;
                } else {
                    customDateRange.style.display = 'none';
                    startDateInput.required = false;
                    endDateInput.required = false;
                }
            }

            periodSelect.addEventListener('change', toggleCustomDate);
            toggleCustomDate(); // Run on load
        }
    });

    function selectAllPlaystations() {
        const checkboxes = document.querySelectorAll('input[name="playstation_types[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }

    function resetPlaystations() {
        const checkboxes = document.querySelectorAll('input[name="playstation_types[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }
</script>

@if($startDate && $endDate)
<script>
    // Set new default font family and font color to mimic Bootstrap's default styling
    Chart.defaults.global.defaultFontFamily = 'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif';
    Chart.defaults.global.defaultFontColor = '#858796';

    function number_format(number, decimals, dec_point, thousands_sep) {
        // *     example: number_format(1234.56, 2, ',', ' ');
        // *     return: '1 234,56'
        number = (number + '').replace(',', '').replace(' ', '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    // Area Chart Example
    var ctx = document.getElementById("incomeChart");
    
    @if(!empty($chartDatasets))
        // Multiple datasets for different PlayStation types
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: @json($chartDatasets),
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return 'Rp ' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: true,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': Rp ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    @else
        // Single dataset for all types
        var myLineChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: "Pendapatan",
                    lineTension: 0.3,
                    backgroundColor: "rgba(78, 115, 223, 0.05)",
                    borderColor: "rgba(78, 115, 223, 1)",
                    pointRadius: 3,
                    pointBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointBorderColor: "rgba(78, 115, 223, 1)",
                    pointHoverRadius: 3,
                    pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                    pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                    pointHitRadius: 10,
                    pointBorderWidth: 2,
                    data: @json($chartData),
                }],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    xAxes: [{
                        time: {
                            unit: 'date'
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                            // Include a dollar sign in the ticks
                            callback: function(value, index, values) {
                                return 'Rp ' + number_format(value);
                            }
                        },
                        gridLines: {
                            color: "rgb(234, 236, 244)",
                            zeroLineColor: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }],
                },
                legend: {
                    display: false
                },
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    titleMarginBottom: 10,
                    titleFontColor: '#6e707e',
                    titleFontSize: 14,
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, chart) {
                            var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                            return datasetLabel + ': Rp ' + number_format(tooltipItem.yLabel);
                        }
                    }
                }
            }
        });
    @endif
</script>
@endif
@endsection
