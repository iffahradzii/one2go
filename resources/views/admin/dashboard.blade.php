@extends('layout.layoutAdmin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container py-5">
    <h2 class="text-center mb-4">Admin Dashboard</h2>

    <!-- Metrics Cards -->
    <div class="row mb-4 text-center">
        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>Total Booking</h5>
                <h3>1000</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>Total Customer</h5>
                <h3>1000</h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 shadow">
                <h5>Total Earn</h5>
                <h3>RM 1000</h3>
            </div>
        </div>
    </div>

    <!-- Revenue Overview and Top Destination -->
    <div class="row mb-4">
        <!-- Revenue Overview -->
        <div class="col-md-8">
            <div class="card shadow p-3">
                <h5>Revenue Overview</h5>
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Top Destination -->
        <div class="col-md-4">
            <div class="card shadow p-3">
                <h5>Top Destination</h5>
                <canvas id="destinationChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Total Trips -->
    <div class="row text-center">
        <div class="col-md-12">
            <div class="card shadow p-3">
                <h5>Total Trips</h5>
                <h3>1000</h3>
                <div class="d-flex justify-content-between">
                    <div>Done: 620</div>
                    <div>Booked: 465</div>
                    <div>Canceled: 115</div>
                </div>
                <div class="progress mt-3">
                    <div class="progress-bar bg-success" style="width: 62%;">Done</div>
                    <div class="progress-bar bg-primary" style="width: 31%;">Booked</div>
                    <div class="progress-bar bg-danger" style="width: 7%;">Canceled</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Overview Chart
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: [1200, 1900, 3000, 5000, 2000, 3000, 4500, 6000, 4800, 5000, 7000, 8000],
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false,
                tension: 0.4,
            }]
        },
        options: {
            responsive: true,
        }
    });

    // Top Destination Chart
    const ctxDestination = document.getElementById('destinationChart').getContext('2d');
    new Chart(ctxDestination, {
        type: 'pie',
        data: {
            labels: ['Paris', 'London', 'New York', 'Tokyo', 'Sydney'],
            datasets: [{
                label: 'Top Destinations',
                data: [25, 20, 15, 30, 10],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)'
                ],
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
        }
    });
</script>
@endsection
