@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Dashboard</h1>
            <div class="d-flex">
                <span class="badge bg-success me-2">System Active</span>
                <small class="text-muted">Last updated: {{ now()->format('d M Y, h:i A') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #e74c3c, #c0392b);">
                    <i class="fas fa-store"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-primary">{{ $stats['total_stores'] }}</div>
                    <div class="text-muted">Total Stores</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #2ecc71, #27ae60);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-success">{{ $stats['active_stores'] }}</div>
                    <div class="text-muted">Active Stores</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #9b59b6, #8e44ad);">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-purple">{{ $stats['total_supervisors'] }}</div>
                    <div class="text-muted">Supervisors</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #f39c12, #e67e22);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-warning">{{ $stats['total_customers'] }}</div>
                    <div class="text-muted">Total Customers</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Social Media Style Cards -->
{{-- <div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="social-card" style="background: linear-gradient(45deg, #3b5998, #2c3e50);">
            <div class="d-flex align-items-center">
                <div class="social-icon" style="background: rgba(255,255,255,0.2);">
                    <i class="fab fa-facebook-f"></i>
                </div>
                <div class="ms-3">
                    <div class="h4 mb-1">{{ $stats['total_stores'] }}k</div>
                    <div class="small">Active Stores</div>
                    <div class="small">{{ $stats['active_stores'] }} Online</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="social-card" style="background: linear-gradient(45deg, #1da1f2, #0d8bd9);">
            <div class="d-flex align-items-center">
                <div class="social-icon" style="background: rgba(255,255,255,0.2);">
                    <i class="fab fa-twitter"></i>
                </div>
                <div class="ms-3">
                    <div class="h4 mb-1">{{ $stats['total_supervisors'] }}k</div>
                    <div class="small">Supervisors</div>
                    <div class="small">{{ $stats['total_supervisors'] }} Active</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="social-card" style="background: linear-gradient(45deg, #0077b5, #005885);">
            <div class="d-flex align-items-center">
                <div class="social-icon" style="background: rgba(255,255,255,0.2);">
                    <i class="fab fa-linkedin-in"></i>
                </div>
                <div class="ms-3">
                    <div class="h4 mb-1">{{ $stats['total_customers'] }}+</div>
                    <div class="small">Customers</div>
                    <div class="small">{{ $stats['total_customers'] }} Registered</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="social-card" style="background: linear-gradient(45deg, #dd4b39, #c23321);">
            <div class="d-flex align-items-center">
                <div class="social-icon" style="background: rgba(255,255,255,0.2);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="ms-3">
                    <div class="h4 mb-1">{{ $stats['total_sales'] }}k</div>
                    <div class="small">Total Sales</div>
                    <div class="small">{{ $stats['total_sales'] }} Bags</div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<div class="row">
    <!-- Recent Stores -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Recent Stores</h5>
                <a href="{{ route('admin.stores.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            
            @if($recentStores->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($recentStores as $store)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-store text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $store->name }}</h6>
                                <small class="text-muted">{{ $store->district->name }}, {{ $store->mandal->name }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge {{ $store->isValid() ? 'bg-success' : 'bg-danger' }}">
                                {{ $store->isValid() ? 'Active' : 'Expired' }}
                            </span>
                            <div class="small text-muted">{{ $store->valid_till->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No stores yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Expiring Soon -->
    <div class="col-lg-6 mb-4">
        <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Stores Expiring Soon</h5>
                <span class="badge bg-warning">{{ $expiringSoon->count() }}</span>
            </div>
            
            @if($expiringSoon->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach($expiringSoon as $store)
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-exclamation-triangle text-white"></i>
                                </div>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ $store->name }}</h6>
                                <small class="text-muted">{{ $store->district->name }}, {{ $store->mandal->name }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-warning">{{ $store->valid_till->diffForHumans() }}</span>
                            <div class="small text-muted">{{ $store->valid_till->format('d M Y') }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <p class="text-muted">No stores expiring soon</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Chart Section -->
{{-- <div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Sales Overview</h5>
                        <small class="text-muted">Monthly statistics for the year</small>
                    </div>
                </div>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary active">Year</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Month</button>
                    <button type="button" class="btn btn-sm btn-outline-primary">Week</button>
                </div>
            </div>
            
            <div style="position: relative; height: 400px;">
                <canvas id="salesChart"></canvas>
            </div>
            
            <!-- Chart Legend -->
            <div class="row mt-4 text-center">
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <div style="width: 20px; height: 20px; background: #e74c3c; border-radius: 3px; margin-right: 10px;"></div>
                        <div>
                            <div class="fw-bold">Stores</div>
                            <small class="text-muted">Total: 45</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <div style="width: 20px; height: 20px; background: #2ecc71; border-radius: 3px; margin-right: 10px;"></div>
                        <div>
                            <div class="fw-bold">Sales</div>
                            <small class="text-muted">Total: 100</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-center">
                        <div style="width: 20px; height: 20px; background: #3498db; border-radius: 3px; margin-right: 10px;"></div>
                        <div>
                            <div class="fw-bold">Customers</div>
                            <small class="text-muted">Total: 95</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}

<script>
// document.addEventListener('DOMContentLoaded', function() {
//     // Sales Chart
//     const ctx = document.getElementById('salesChart');
//     if (ctx) {
//         const salesChart = new Chart(ctx.getContext('2d'), {
//             type: 'line',
//             data: {
//                 labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
//                 datasets: [{
//                     label: 'Stores',
//                     data: [12, 19, 15, 25, 22, 30, 28, 35, 32, 40, 38, 45],
//                     borderColor: '#e74c3c',
//                     backgroundColor: 'rgba(231, 76, 60, 0.1)',
//                     tension: 0.4,
//                     fill: true,
//                     borderWidth: 3,
//                     pointRadius: 4,
//                     pointHoverRadius: 6,
//                     pointBackgroundColor: '#e74c3c',
//                     pointBorderColor: '#fff',
//                     pointBorderWidth: 2
//                 }, {
//                     label: 'Sales',
//                     data: [65, 59, 80, 81, 56, 55, 40, 70, 85, 95, 88, 100],
//                     borderColor: '#2ecc71',
//                     backgroundColor: 'rgba(46, 204, 113, 0.1)',
//                     tension: 0.4,
//                     fill: true,
//                     borderWidth: 3,
//                     pointRadius: 4,
//                     pointHoverRadius: 6,
//                     pointBackgroundColor: '#2ecc71',
//                     pointBorderColor: '#fff',
//                     pointBorderWidth: 2
//                 }, {
//                     label: 'Customers',
//                     data: [28, 48, 40, 19, 86, 27, 90, 55, 75, 65, 80, 95],
//                     borderColor: '#3498db',
//                     backgroundColor: 'rgba(52, 152, 219, 0.1)',
//                     tension: 0.4,
//                     fill: true,
//                     borderWidth: 3,
//                     pointRadius: 4,
//                     pointHoverRadius: 6,
//                     pointBackgroundColor: '#3498db',
//                     pointBorderColor: '#fff',
//                     pointBorderWidth: 2
//                 }]
//             },
//             options: {
//                 responsive: true,
//                 maintainAspectRatio: false,
//                 interaction: {
//                     mode: 'index',
//                     intersect: false,
//                 },
//                 plugins: {
//                     legend: {
//                         display: true,
//                         position: 'top',
//                         labels: {
//                             usePointStyle: true,
//                             padding: 20,
//                             font: {
//                                 size: 13,
//                                 weight: 'bold'
//                             }
//                         }
//                     },
//                     tooltip: {
//                         backgroundColor: 'rgba(0, 0, 0, 0.8)',
//                         padding: 12,
//                         titleFont: {
//                             size: 14,
//                             weight: 'bold'
//                         },
//                         bodyFont: {
//                             size: 13
//                         },
//                         borderColor: 'rgba(255, 255, 255, 0.2)',
//                         borderWidth: 1,
//                         displayColors: true,
//                         callbacks: {
//                             label: function(context) {
//                                 let label = context.dataset.label || '';
//                                 if (label) {
//                                     label += ': ';
//                                 }
//                                 label += context.parsed.y;
//                                 return label;
//                             }
//                         }
//                     }
//                 },
//                 scales: {
//                     y: {
//                         beginAtZero: true,
//                         grid: {
//                             color: 'rgba(0, 0, 0, 0.05)',
//                             drawBorder: false
//                         },
//                         ticks: {
//                             font: {
//                                 size: 12
//                             },
//                             padding: 10
//                         }
//                     },
//                     x: {
//                         grid: {
//                             color: 'rgba(0, 0, 0, 0.05)',
//                             drawBorder: false
//                         },
//                         ticks: {
//                             font: {
//                                 size: 12
//                             },
//                             padding: 10
//                         }
//                     }
//                 }
//             }
//         });
//     }
// });
</script>
@endsection