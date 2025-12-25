@extends('layouts.app')

@section('title', 'Customer Purchase Details')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Customer Purchase History</h1>
            <a href="{{ route('supervisor.reports.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>
</div>

<!-- Customer Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-user"></i>
                </div>
                <h5 class="mb-0">Customer Information</h5>
            </div>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 40px; height: 40px;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Document Number</h6>
                            <p class="mb-0 fw-bold">{{ $customer->document_number }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 40px; height: 40px;">
                            <i class="fas fa-map"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Total Land</h6>
                            <p class="mb-0 fw-bold">{{ $customer->total_land }} acres</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 40px; height: 40px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Location</h6>
                            <p class="mb-0 fw-bold">{{ $customer->district->name }}</p>
                            <small class="text-muted">{{ $customer->mandal->name }}</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #f39c12, #e67e22); width: 40px; height: 40px;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Created By</h6>
                            <p class="mb-0 fw-bold">{{ $customer->createdBy ? $customer->createdBy->name : 'System' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Summary -->
<div class="row mb-4">
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #3498db, #2980b9);">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-primary">{{ $customer->total_stock_allotted }}</div>
                    <div class="text-muted">Total Stock Allotted</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #f39c12, #e67e22);">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-warning">{{ $customer->stock_availed }}</div>
                    <div class="text-muted">Total Purchased</div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon" style="background: linear-gradient(45deg, #2ecc71, #27ae60);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="ms-3">
                    <div class="stats-number text-success">{{ $customer->balance_stock }}</div>
                    <div class="text-muted">Balance Remaining</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase History -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>All Purchases
                </h5>
                <span class="badge bg-primary">{{ $customer->sales->count() }} Total Purchases</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Store Name</th>
                            <th>Location</th>
                            <th>Quantity</th>
                            <th>Balance After</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->sales->sortByDesc('created_at') as $sale)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 30px; height: 30px;">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <span class="fw-bold">{{ $sale->store->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $sale->store->district->name }}</div>
                                    <small class="text-muted">{{ $sale->store->mandal->name }}</small>
                                </div>
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
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No purchases yet</h5>
                                <p class="text-muted">This customer hasn't made any purchases yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($customer->document_photo)
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #e74c3c, #c0392b); width: 50px; height: 50px;">
                    <i class="fas fa-image"></i>
                </div>
                <h5 class="mb-0">Document Photo</h5>
            </div>
            
            <div class="text-center">
                <img src="{{ $customer->document_photo_url }}" alt="Document" class="img-fluid rounded shadow" style="max-height: 500px;">
            </div>
        </div>
    </div>
</div>
@endif
@endsection