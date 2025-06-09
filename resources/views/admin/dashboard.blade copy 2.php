@extends('layout.layoutAdmin')

@section('title', 'Admin Dashboard')

@section('styles')
<style>
    /* Badge and legend styling */
    .bg-pink {
        background-color: #e83e8c;
        color: white;
    }
    
    .value-badge {
        font-size: 0.75rem;
        min-width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50% !important;
        padding: 0 0.4rem;
    }
    
    .legend-item {
        display: inline-flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    
    /* Layout consistency */
    .chart-container-row .card {
        height: 100%;
    }
    
    .chart-pie, .chart-bar, .chart-area {
        height: 250px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Dashboard Header -->
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
        <span class="text-gray-600">Welcome back, Admin!</span>
    </div>

    <!-- Metrics Cards -->
    <div class="row g-4 mb-4">
        @php
        $metricCards = [
            [
                'title' => 'Total Bookings',
                'value' => $totalBookings ?? 0,
                'growth' => $bookingGrowth ?? 0,
                'icon' => 'fa-calendar',
                'color' => 'primary'
            ],
            [
                'title' => 'Total Revenue',
                'value' => 'RM ' . number_format($totalRevenue ?? 0, 2),
                'growth' => $revenueGrowth ?? 0,
                'icon' => 'fa-dollar-sign',
                'color' => 'success'
            ],
            [
                'title' => 'Total Customers',
                'value' => $totalCustomers ?? 0,
                'growth' => $customerGrowth ?? 0,
                'icon' => 'fa-users',
                'color' => 'info'
            ],
            [
                'title' => 'Pending Bookings',
                'value' => $pendingBookings ?? 0,
                'growth' => $pendingGrowth ?? 0,
                'icon' => 'fa-clock',
                'color' => 'warning',
                'period' => 'week'
            ]
        ];
        @endphp

        @foreach($metricCards as $card)
        <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6">
            <div class="card border-left-{{ $card['color'] }} shadow h-100">
                <div class="card-body py-3">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $card['color'] }} text-uppercase mb-2">{{ $card['title'] }}</div>
                            <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $card['value'] }}</div>
                            <div class="mt-2 text-{{ $card['growth'] >= 0 ? 'success' : 'danger' }} small" data-bs-toggle="tooltip" title="Change since last {{ $card['period'] ?? 'month' }}">
                                <i class="fas fa-arrow-{{ $card['growth'] >= 0 ? 'up' : 'down' }} me-1"></i>
                                {{ abs($card['growth']) }}% since last {{ $card['period'] ?? 'month' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $card['icon'] }} fa-2x text-gray-300"></i>
                        </div>
                    </div>      
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Content Row -->
    <div class="row g-4 chart-container-row">
        <!-- Revenue Overview Chart (Full Width) -->
        <div class="col-12">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Revenue Overview</h6>
                    <div class="dropdown no-arrow"></div>
                    <button class="btn btn-sm btn-outline-primary export-pdf-btn" data-chart="revenue">
                        <i class="fas fa-download me-1"></i> Export PDF
                    </button>
                </div>
                <div class="card-body">
                    <div class="chart-area" style="height: 300px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Destinations and Top Countries side by side -->
        @php
        $chartConfigs = [
            [
                'id' => 'destination',
                'title' => 'Top Destinations for May',
                'type' => 'pie',
                'labels' => $destinationLabels ?? [],
                'colors' => ['primary', 'success', 'info', 'warning', 'danger']
            ],
            [
                'id' => 'country',
                'title' => 'Top Countries for May',
                'type' => 'bar',
                'labels' => $countryLabels ?? [],
                'colors' => ['primary', 'success', 'info', 'warning']
            ]
        ];
        @endphp

        @foreach($chartConfigs as $config)
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary" id="{{ $config['id'] }}ChartTitle">{{ $config['title'] }}</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="{{ $config['id'] == 'country' ? 'country' : '' }}DropdownMenuLink" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="{{ $config['id'] == 'country' ? 'country' : '' }}DropdownMenuLink">
                            <div class="dropdown-header">Month:</div>
                            <div class="month-selector" data-chart="{{ $config['id'] }}">
                                <a class="dropdown-item month-item" href="#" data-month="overall">Overall</a>
                                @for ($i = 1; $i <= 12; $i++)
                                    <a class="dropdown-item month-item {{ $i == ($currentMonth ?? Carbon\Carbon::now()->month) ? 'active' : '' }}" 
                                       href="#" data-month="{{ $i }}">{{ date('F', mktime(0, 0, 0, $i, 1)) }}</a>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-{{ $config['type'] }} pt-4 pb-2" style="height: 250px;">
                        <canvas id="{{ $config['id'] }}Chart"></canvas>
                    </div>
                    <div class="mt-4 text-center small {{ $config['id'] }}-legend">
                        @forelse($config['labels'] as $index => $label)
                            <span class="me-2 legend-item">
                                <i class="fas fa-circle text-{{ $config['colors'][$index % count($config['colors'])] }}"></i> {{ $label }}
                                <span class="badge rounded-pill bg-pink ms-1 value-badge">0</span>
                            </span>
                        @empty
                            <span class="text-muted">No data available</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <!-- Recent Bookings (8) and Trips Status (4) side by side -->
        <div class="col-xl-8 col-lg-8">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Bookings</h6>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.booking.index') }}" class="btn btn-primary px-4">View General</a>
                        <a href="{{ route('admin.private-booking.index') }}" class="btn btn-info px-4 text-white">View Private</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th class="px-4 py-3">Customer</th>
                                    <th class="px-4 py-3">Package</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBookings as $booking)
                                <tr>
                                    <td class="px-4 py-3">{{ $booking->user_name }}</td>
                                    <td class="px-4 py-3">{{ $booking->package_name }}</td>
                                    <td class="px-4 py-3">{{ $booking->available_date }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                        $statusColors = [
                                            'paid' => 'bg-success',
                                            'pending' => 'bg-warning',
                                            'default' => 'bg-danger'
                                        ];
                                        $statusColor = $statusColors[$booking->payment_status] ?? $statusColors['default'];
                                        @endphp
                                        <span class="badge {{ $statusColor }}">
                                            {{ ucfirst($booking->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="btn px-2 py-1 rounded-pill text-white {{ $booking->booking_type === 'Private' ? 'btn-info' : 'btn-primary' }}" style="font-size: 0.85rem;">
                                            {{ $booking->booking_type }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Trips Status -->
        <div class="col-xl-4 col-lg-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Trips Status</h6>
                </div>
                <div class="card-body d-flex flex-column justify-content-between">
                    <div class="progress-section">
                        @php
                        $tripStatuses = [
                            ['name' => 'Pending', 'percentage' => $pendingPercentage ?? 0, 'count' => $pendingTrips ?? 0, 'color' => 'warning'],
                            ['name' => 'Paid', 'percentage' => $paidPercentage ?? 0, 'count' => $paidTrips ?? 0, 'color' => 'info'],
                            ['name' => 'Completed', 'percentage' => $completedPercentage ?? 0, 'count' => $completedTrips ?? 0, 'color' => 'success'],
                            ['name' => 'Canceled', 'percentage' => $canceledPercentage ?? 0, 'count' => $canceledTrips ?? 0, 'color' => 'danger']
                        ];
                        @endphp

                        @foreach($tripStatuses as $status)
                        <h4 class="small font-weight-bold">{{ $status['name'] }} <span class="float-end">{{ $status['percentage'] }}%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-{{ $status['color'] }}" role="progressbar" style="width: {{ $status['percentage'] }}%" 
                                 aria-valuenow="{{ $status['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-between mt-auto pt-4">
                        @foreach($tripStatuses as $status)
                        <div class="text-center flex-grow-1">
                            <div class="font-weight-bold mb-2">{{ $status['name'] }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $status['count'] }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
<script>
    // Utility function for number formatting
    function number_format(number, decimals = 2, dec_point = '.', thousands_sep = ',') {
        number = (number + '').replace(',', '').replace(' ', '');
        let n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                let k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    // Common chart configuration
    const chartConfig = {
        tooltipStyle: {
            backgroundColor: "rgb(68, 68, 68)",
            bodyColor: "#ffffff",
            titleColor: "#ffffff",
            titleFont: {
                size: 14,
                weight: 'bold',
                family: "'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,sans-serif"
            },
            bodyFont: {
                size: 14,
                family: "'Nunito', '-apple-system,system-ui,BlinkMacSystemFont,\"Segoe UI\",Roboto,\"Helvetica Neue\",Arial,sans-serif"
            },
            borderColor: '#dddfeb',
            borderWidth: 1,
            padding: { x: 15, y: 15 },
            displayColors: false
        }
    };

    // Global chart instances
    let revenueChart, destinationChart, countryChart;
    
    // Revenue Overview Chart
    const ctx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Revenue',
                data: @json($revenueData ?? array_fill(0, 12, 0)),
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                borderColor: 'rgba(78, 115, 223, 1)',
                pointRadius: 3,
                pointBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointBorderColor: 'rgba(78, 115, 223, 1)',
                pointHoverRadius: 3,
                pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
                pointHoverBorderColor: 'rgba(78, 115, 223, 1)',
                pointHitRadius: 10,
                pointBorderWidth: 2,
                tension: 0.3
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: { left: 10, right: 25, top: 25, bottom: 0 }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: { maxTicksLimit: 7 }
                },
                y: {
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) { return 'RM' + number_format(value); }
                    },
                    grid: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                },
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...chartConfig.tooltipStyle,
                    intersect: false,
                    mode: 'index',
                    caretPadding: 10,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: RM' + number_format(context.raw);
                        }
                    }
                }
            }
        }
    });

    // Top Destination Chart
    const ctxDestination = document.getElementById('destinationChart').getContext('2d');
    destinationChart = new Chart(ctxDestination, {
        type: 'doughnut',
        data: {
            labels: @json($destinationLabels ?? []),
            datasets: [{
                data: @json($destinationDataValues ?? []),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderWidth: 1
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...chartConfig.tooltipStyle,
                    callbacks: {
                        title: function(context) {
                            return context[0].label;
                        },
                        label: function(context) {
                            return 'Bookings: ' + context.raw;
                        }
                    }
                }
            }
        }
    });

    // Top Countries Chart
    const ctxCountry = document.getElementById('countryChart').getContext('2d');
    countryChart = new Chart(ctxCountry, {
        type: 'bar',
        data: {
            labels: @json($countryLabels ?? []),
            datasets: [{
                label: 'Bookings',
                data: @json($countryDataValues ?? []),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    ...chartConfig.tooltipStyle,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ": " + context.raw;
                        }
                    }
                }
            }
        }
    });

    // Chart update functions
    function updateDestinationChart(labels, data) {
        destinationChart.data.labels = labels;
        destinationChart.data.datasets[0].data = data;
        destinationChart.update();
        
        // Update the legend
        updateChartLegend('destination', labels, data);
    }
    
    function updateCountryChart(labels, data) {
        countryChart.data.labels = labels;
        countryChart.data.datasets[0].data = data;
        countryChart.update();
        
        // Update the legend
        updateChartLegend('country', labels, data);
    }

    function updateChartLegend(chartType, labels, data) {
        const legendContainer = document.querySelector(`.${chartType}-legend`);
        legendContainer.innerHTML = '';
        
        if (labels.length > 0 && labels[0] !== 'No Data Available') {
            const colorClasses = chartType === 'destination' ? 
                ['primary', 'success', 'info', 'warning', 'danger'] : 
                ['primary', 'success', 'info', 'warning'];
                
            labels.forEach((label, index) => {
                const colorClass = colorClasses[index % colorClasses.length];
                legendContainer.innerHTML += `
                    <span class="me-2 legend-item">
                        <i class="fas fa-circle text-${colorClass}"></i> ${label}
                        <span class="badge rounded-pill bg-pink ms-1 value-badge">${data[index]}</span>
                    </span>
                `;
            });
        } else {
            legendContainer.innerHTML = '<span class="text-muted">No data available</span>';
        }
    }
    
    // Function to update value badges on page load
    function updateValueBadges() {
        // Update destination chart badges
        if (destinationChart.data.labels.length > 0) {
            const destData = destinationChart.data.datasets[0].data;
            document.querySelectorAll('.destination-legend .value-badge').forEach((badge, index) => {
                if (index < destData.length) {
                    badge.textContent = destData[index];
                }
            });
        }
        
        // Update country chart badges
        if (countryChart.data.labels.length > 0) {
            const countryData = countryChart.data.datasets[0].data;
            document.querySelectorAll('.country-legend .value-badge').forEach((badge, index) => {
                if (index < countryData.length) {
                    badge.textContent = countryData[index];
                }
            });
        }
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Set initial chart titles with current month
        const currentMonth = new Date().toLocaleString('default', { month: 'long' });
        document.getElementById('destinationChartTitle').textContent = `Top Destinations for ${currentMonth}`;
        document.getElementById('countryChartTitle').textContent = `Top Countries for ${currentMonth}`;
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Update value badges on page load
        updateValueBadges();
    });

    // Month selector functionality
    document.querySelectorAll('.month-selector .month-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const month = this.getAttribute('data-month');
            const chartType = this.closest('.month-selector').getAttribute('data-chart');
            const monthName = this.textContent.trim();
            
            // Update active class
            this.closest('.month-selector').querySelectorAll('.month-item').forEach(el => {
                el.classList.remove('active');
            });
            this.classList.add('active');
            
            // Update chart title
            if (chartType === 'destination') {
                document.getElementById('destinationChartTitle').textContent = `Top Destinations ${month === 'overall' ? 'Overall' : `for ${monthName}`}`;
            } else if (chartType === 'country') {
                document.getElementById('countryChartTitle').textContent = `Top Countries ${month === 'overall' ? 'Overall' : `for ${monthName}`}`;
            }
            
            // Fetch data for the selected month
            fetch(`{{ route('admin.dashboard.charts-data') }}?month=${month}`)
                .then(response => response.json())
                .then(data => {
                    if (chartType === 'destination') {
                        updateDestinationChart(data.destinationLabels, data.destinationData);
                    } else if (chartType === 'country') {
                        updateCountryChart(data.countryLabels, data.countryData);
                    }
                    // Update value badges after chart update
                    updateValueBadges();
                })
                .catch(error => console.error('Error fetching chart data:', error));
        });
    });

    // Revenue month selector
    document.getElementById('revenueMonth')?.addEventListener('change', function() {
        const selectedMonth = this.value;
        
        // Make an AJAX request to get the revenue data
        fetch(`/admin/dashboard/revenue-data?month=${selectedMonth}`)
            .then(response => response.json())
            .then(data => {
                // Update the revenue chart data
                revenueChart.data.datasets[0].data = data.revenueData;
                revenueChart.update();
            })
            .catch(error => console.error('Error fetching revenue data:', error));
    });
</script>
@endsection