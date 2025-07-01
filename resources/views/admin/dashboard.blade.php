@extends('layouts.admin')
@section('content')

<!-- Ringkasan Data -->
<div class="container-fluid mt-4">
    <h1 class="mb-4">Dashboard Admin</h1>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-bg-primary mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Buku</h5>
                    <p class="card-text fs-4">{{ $bookCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-success mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Pengguna</h5>
                    <p class="card-text fs-4">{{ $userCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-warning mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Penjual</h5>
                    <p class="card-text fs-4">{{ $sellerCount }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-bg-danger mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Total Toko</h5>
                    <p class="card-text fs-4">{{ $storeCount }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Server Stats Chart -->
<div class="container-fluid mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Statistik Server (Real-time)</h5>
            <canvas id="serverStatsChart" height="100"></canvas>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('serverStatsChart').getContext('2d');
    
    const serverStatsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [], // Timestamps
            datasets: [
                {
                    label: 'CPU Usage (%)',
                    data: [],
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'GPU Usage (%)',
                    data: [],
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 10
                    }
                }
            },
            animation: {
                duration: 200
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            responsive: true,
            maintainAspectRatio: true
        }
    });

    function addData(chart, label, cpuData, gpuData) {
        chart.data.labels.push(label);
        chart.data.datasets[0].data.push(cpuData);
        chart.data.datasets[1].data.push(gpuData);

        // Limit data points to 20
        if (chart.data.labels.length > 20) {
            chart.data.labels.shift();
            chart.data.datasets.forEach((dataset) => {
                dataset.data.shift();
            });
        }
        
        chart.update();
    }

    function fetchServerStats() {
        fetch('{{ route("admin.server-stats") }}')
            .then(response => response.json())
            .then(data => {
                const now = new Date();
                const time = now.getHours() + ':' + now.getMinutes() + ':' + now.getSeconds();
                addData(serverStatsChart, time, data.cpu, data.gpu);
            })
            .catch(error => console.error('Error fetching server stats:', error));
    }

    // Fetch data every 2 seconds
    setInterval(fetchServerStats, 2000);
});
</script>
@endpush
