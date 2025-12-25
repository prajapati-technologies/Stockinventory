@extends('layouts.app')

@section('title', 'Sales Reports')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Sales Reports</h1>
            <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h5 class="mb-0">Report Filters</h5>
            </div>
            
            <form method="GET" action="{{ route('supervisor.reports.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Select Store <span class="text-danger">*</span></label>
                        <select name="store_id" required class="form-select">
                            <option value="">Select Store</option>
                            <option value="all" {{ request('store_id') == 'all' ? 'selected' : '' }}>All Stores</option>
                            @foreach($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control">
                    </div>

                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-2"></i>Get Data
                        </button>
                        <a href="{{ route('supervisor.reports.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if($selectedStore || $showAllStores)
    <!-- Sales Summary -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon" style="background: linear-gradient(45deg, #e74c3c, #c0392b);">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stats-number text-danger">{{ $sales->sum('quantity') ?? 0 }}</div>
                        <div class="text-muted">Total Stock Sold</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <div class="stats-icon" style="background: linear-gradient(45deg, #2ecc71, #27ae60);">
                        <i class="fas fa-list"></i>
                    </div>
                    <div class="ms-3">
                        <div class="stats-number text-success">{{ $sales->total() ?? count($sales) }}</div>
                        <div class="text-muted">Total Sales Records</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        <i class="fas fa-store me-2"></i>
                        @if($showAllStores)
                            Sales from All Stores
                        @else
                            Sales from {{ $selectedStore->name }}
                        @endif
                    </h5>
                    <div class="d-flex gap-2">
                        @if($sales->count() > 0)
                            <a href="{{ route('supervisor.reports.export', request()->query()) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-file-excel me-2"></i>Download Excel
                            </a>
                        @endif
                        <span class="badge bg-primary">{{ $sales->total() ?? count($sales) }} Total Sales</span>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Document Number</th>
                                @if($showAllStores)
                                    <th>Store Name</th>
                                @endif
                                <th>Land (Acres)</th>
                                <th>Quantity</th>
                                <th>Balance After</th>
                                <th>Date & Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr class="cursor-pointer" onclick="window.location='{{ route('supervisor.reports.customer', $sale->customer) }}'">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="stats-icon me-2" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 30px; height: 30px;">
                                            <i class="fas fa-file-alt"></i>
                                        </div>
                                        <span class="fw-bold text-primary">{{ $sale->customer->document_number }}</span>
                                    </div>
                                </td>
                                @if($showAllStores)
                                    <td>
                                        <span class="badge bg-secondary">{{ $sale->store->name }}</span>
                                    </td>
                                @endif
                                <td>
                                    <span class="badge bg-warning">{{ $sale->customer->total_land }} acres</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $sale->quantity }} bags</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $sale->balance_after }} bags</span>
                                </td>
                                <td>
                                    <div>{{ $sale->created_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ $showAllStores ? '6' : '5' }}" class="text-center py-5">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No sales found</h5>
                                    <p class="text-muted">No sales found for the selected criteria</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($sales instanceof \Illuminate\Pagination\LengthAwarePaginator && $sales->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $sales->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);">
                <div class="stats-icon mx-auto mb-4" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 80px; height: 80px;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h4 class="mb-3">Generate Sales Report</h4>
                <p class="text-muted mb-0">Select a store and date range above to view sales reports</p>
            </div>
        </div>
    </div>
@endif
@endsection