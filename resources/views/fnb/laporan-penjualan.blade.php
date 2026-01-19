@extends('layouts.app')

@section('content')
    <!-- Content Row -->
    @if (session()->has('success'))
        <div class="alert alert-success col-lg-8" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('gagal'))
        <div class="alert alert-danger col-lg-8" role="alert">
            {{ session('gagal') }}
        </div>
    @endif



    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('fnb.laporan') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="period">Periode</label>
                        <select name="period" id="period" class="form-control">
                            <option value="today" {{ $period == 'today' ? 'selected' : '' }}>Hari Ini</option>
                            <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                            <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>Tahun Ini</option>
                            <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                        </select>
                    </div>
                    <div class="col-md-3" id="start-date-group" style="display: {{ $period == 'custom' ? 'block' : 'none' }}">
                        <label for="start_date">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3" id="end-date-group" style="display: {{ $period == 'custom' ? 'block' : 'none' }}">
                        <label for="end_date">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary form-control">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Penjualan Per Hari</h6>
                </div>
                <div class="card-body">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Distribusi Penjualan Produk</h6>
                </div>
                <div class="card-body">
                    <canvas id="productChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    
@endsection

@section('scripts')
<script>
    // Period toggle
    $('#period').change(function() {
        if ($(this).val() === 'custom') {
            $('#start-date-group, #end-date-group').show();
        } else {
            $('#start-date-group, #end-date-group').hide();
        }
    });

    // Charts
    var salesData = @json($chartData['sales_over_time']);
    var productData = @json($chartData['product_distribution']);
    var profitData = @json($chartData['profit_loss']);

    // Sales over time (bar)
    var ctxSales = document.getElementById('salesChart').getContext('2d');
    new Chart(ctxSales, {
        type: 'bar',
        data: {
            labels: Object.keys(salesData),
            datasets: [{
                label: 'Jumlah Terjual',
                data: Object.values(salesData),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Product distribution (pie)
    var ctxProduct = document.getElementById('productChart').getContext('2d');
    new Chart(ctxProduct, {
        type: 'pie',
        data: {
            labels: Object.keys(productData),
            datasets: [{
                data: Object.values(productData),
                backgroundColor: [
                    'rgba(255, 99, 132, 0.5)',
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(255, 205, 86, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(153, 102, 255, 0.5)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 205, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1
            }]
        }
    });

    // Profit/Loss (bar)
    var ctxProfit = document.getElementById('profitChart').getContext('2d');
    new Chart(ctxProfit, {
        type: 'bar',
        data: {
            labels: Object.keys(profitData),
            datasets: [{
                label: 'Laba/Rugi',
                data: Object.values(profitData),
                backgroundColor: function(context) {
                    var value = context.parsed.y;
                    return value >= 0 ? 'rgba(75, 192, 192, 0.5)' : 'rgba(255, 99, 132, 0.5)';
                },
                borderColor: function(context) {
                    var value = context.parsed.y;
                    return value >= 0 ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)';
                },
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
@endsection
