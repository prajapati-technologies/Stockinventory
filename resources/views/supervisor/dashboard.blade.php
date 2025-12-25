@extends('layouts.app')

@section('title', 'Supervisor Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Supervisor Dashboard</h1>
            <div class="d-flex">
                <span class="badge bg-primary me-2">Supervisor</span>
                <small class="text-muted">Last updated: {{ now()->format('d M Y, h:i A') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Supervisor Information Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%); color: white;">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 60px; height: 60px;">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h2 class="mb-1">{{ $user->name }}</h2>
                    <p class="mb-0 opacity-75">Supervisor Dashboard</p>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">District</h6>
                            <p class="mb-0 fw-bold">{{ $user->district->name }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map-signs"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">Mandal</h6>
                            <p class="mb-0 fw-bold">{{ $user->mandal->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h5 class="mb-0">Reports & Analytics</h5>
            </div>
            <p class="text-muted mb-3">Generate reports and view analytics for your area</p>
            <a href="{{ route('supervisor.reports.index') }}" class="btn btn-success w-100">
                <i class="fas fa-chart-line me-2"></i>View Reports
            </a>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="mb-0">Customer Management</h5>
            </div>
            <p class="text-muted mb-3">Search customers and manage customer data</p>
            <div class="d-grid gap-2">
                <a href="{{ route('supervisor.customer.search') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Search Customer
                </a>
                <a href="{{ route('supervisor.customers.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-users me-2"></i>Manage Customers
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #3498db, #2980b9);">
                    <i class="fas fa-store"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-primary">{{ $stats['total_stores'] ?? 0 }}</div>
                    <div class="text-muted">Total Stores</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #2ecc71, #27ae60);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-success">{{ $stats['total_customers'] ?? 0 }}</div>
                    <div class="text-muted">Total Customers</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #9b59b6, #8e44ad);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-purple">{{ $stats['total_sales'] ?? 0 }}</div>
                    <div class="text-muted">Total Sales</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #f39c12, #e67e22);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-warning">{{ $stats['active_stores'] ?? 0 }}</div>
                    <div class="text-muted">Active Stores</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stores in Mandal -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Stores in Your Mandal</h5>
                <span class="badge bg-primary">{{ $stores->count() ?? 0 }} Stores</span>
            </div>
            
            <div class="row">
                @forelse($stores ?? [] as $store)
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 40px; height: 40px;">
                                    <i class="fas fa-store"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $store->name }}</h6>
                                    <small class="text-muted">{{ $store->phone_number }}</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Valid Till:</span>
                                    <span class="fw-bold">{{ $store->valid_till->format('d M Y') }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span class="text-muted">Status:</span>
                                    <span class="badge {{ $store->isValid() ? 'bg-success' : 'bg-danger' }}">
                                        {{ $store->isValid() ? 'Active' : 'Expired' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <a href="{{ route('supervisor.reports.index') }}?store_id={{ $store->id }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-chart-line me-1"></i>View Reports
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-store fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No stores in this mandal</h5>
                        <p class="text-muted">Stores will appear here when they are assigned to your mandal</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection