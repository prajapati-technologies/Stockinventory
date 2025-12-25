@extends('layouts.app')

@section('title', 'Store Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Store Dashboard</h1>
            <div class="d-flex">
                <span class="badge {{ $store->isValid() ? 'bg-success' : 'bg-danger' }} me-2">
                    {{ $store->isValid() ? 'Active' : 'Expired' }}
                </span>
                <small class="text-muted">Last updated: {{ now()->format('d M Y, h:i A') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Store Information Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 60px; height: 60px;">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h2 class="mb-1">{{ $store->name }}</h2>
                    <p class="mb-0 opacity-75">Store Manager Dashboard</p>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">District</h6>
                            <p class="mb-0 fw-bold">{{ $store->district->name }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map-signs"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">Mandal</h6>
                            <p class="mb-0 fw-bold">{{ $store->mandal->name }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">Valid Till</h6>
                            <p class="mb-0 fw-bold">{{ $store->valid_till->format('d M Y') }}</p>
                            @if($store->valid_till->diffInDays(now()) <= 30)
                                <small class="text-warning">Expires in {{ $store->valid_till->diffForHumans() }}</small>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Customer Search -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <h5 class="mb-3">
                <i class="fas fa-search me-2"></i>Search Customer
            </h5>
            <form method="GET" action="{{ route('store.customer.search') }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Document Number</label>
                        <input 
                            type="text" 
                            name="document_number" 
                            class="form-control" 
                            placeholder="Enter document number"
                            required
                        >
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-users"></i>
                </div>
                <h5 class="mb-0">Customer Management</h5>
            </div>
            <p class="text-muted mb-3">Search for existing customers or add new customer data</p>
            <a href="{{ route('store.customer.search') }}" class="btn btn-primary w-100">
                <i class="fas fa-user-plus me-2"></i>Manage Customers
            </a>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h5 class="mb-0">Sales Management</h5>
            </div>
            <p class="text-muted mb-3">View sales history and manage transactions</p>
            <a href="{{ route('store.sale.history') }}" class="btn btn-success w-100">
                <i class="fas fa-chart-line me-2"></i>View Sales History
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #e74c3c, #c0392b);">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-primary">{{ $stats['total_sales_today'] ?? 0 }}</div>
                    <div class="text-muted">Today's Sales</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #2ecc71, #27ae60);">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-success">{{ $stats['total_sales_month'] ?? 0 }}</div>
                    <div class="text-muted">This Month</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #9b59b6, #8e44ad);">
                    <i class="fas fa-users"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-purple">{{ $stats['total_customers'] ?? 0 }}</div>
                    <div class="text-muted">Total Customers</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #f39c12, #e67e22);">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-warning">{{ $stats['total_sales'] ?? 0 }}</div>
                    <div class="text-muted">Total Sales</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Sales -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Recent Sales</h5>
                <a href="{{ route('store.sale.history') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Document Number</th>
                            <th>Customer</th>
                            <th>Quantity</th>
                            <th>Balance</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales ?? [] as $sale)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $sale->customer->document_number }}</span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 30px; height: 30px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $sale->customer->document_number }}</div>
                                        <small class="text-muted">{{ $sale->customer->total_land }} acres</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $sale->quantity ?? 0 }} bags</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $sale->balance_after ?? 0 }} bags</span>
                            </td>
                            <td>
                                <div>{{ $sale->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No sales yet</h5>
                                <p class="text-muted">Start by adding your first customer</p>
                                <a href="{{ route('store.customer.search') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Customer
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection