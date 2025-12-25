@extends('layouts.app')

@section('title', 'Search Customer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Search Customer</h1>
            <a href="{{ route('store.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-search"></i>
                </div>
                <h5 class="mb-0">Customer Search</h5>
            </div>
            
            <form method="GET" action="{{ route('store.customer.search') }}">
                <div class="row g-3">
                    <div class="col-md-10">
                        <input 
                            type="text" 
                            name="document_number" 
                            value="{{ request('document_number') }}" 
                            placeholder="Enter Document Number" 
                            class="form-control form-control-lg @error('document_number') is-invalid @enderror"
                            autofocus
                        >
                        @error('document_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-search me-2"></i>Get Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if(request('document_number'))
    @if($customer)
        <!-- Customer Found -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="stats-card">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">Customer Details</h5>
                        <span class="badge bg-success fs-6">Data Found</span>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
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
                        
                        <div class="col-md-4">
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
                        
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 40px; height: 40px;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Location</h6>
                                    <p class="mb-0 fw-bold">{{ $customer->district->name }}, {{ $customer->mandal->name }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 40px; height: 40px;">
                                    <i class="fas fa-boxes"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Total Stock Allotted</h6>
                                    <p class="mb-0 fw-bold text-primary">{{ $customer->total_stock_allotted }} bags</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #f39c12, #e67e22); width: 40px; height: 40px;">
                                    <i class="fas fa-shopping-cart"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Stock Already Availed</h6>
                                    <p class="mb-0 fw-bold text-warning">{{ $customer->stock_availed }} bags</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 40px; height: 40px;">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Balance Stock</h6>
                                    <p class="mb-0 fw-bold text-success">{{ $customer->balance_stock }} bags</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Bags Section -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="mb-3">
                                <i class="fas fa-plus-circle me-2"></i>Additional Bags Allotted
                            </h6>
                            @if($customer->additionalBags && $customer->additionalBags->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Additional Bags</th>
                                                <th>Remarks</th>
                                                <th>Added By</th>
                                                <th>Date & Time</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($customer->additionalBags as $index => $additionalBag)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    <span class="badge bg-success">+{{ $additionalBag->additional_bags }} bags</span>
                                                </td>
                                                <td>{{ $additionalBag->remarks ?? 'N/A' }}</td>
                                                <td>{{ $additionalBag->addedBy->name ?? 'N/A' }}</td>
                                                <td>{{ $additionalBag->created_at->format('d-m-Y h:i A') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                            <tr class="table-info">
                                                <td colspan="2" class="text-end"><strong>Total Additional Bags:</strong></td>
                                                <td colspan="3"><strong>{{ $customer->additionalBags->sum('additional_bags') }} bags</strong></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info mb-0">
                                    <i class="fas fa-info-circle me-2"></i>No additional bags allotted to this customer.
                                </div>
                            @endif
                        </div>
                    </div>

                    @if($customer->balance_stock > 0)
                        <div class="d-grid">
                            <a href="{{ route('store.sale.create', ['customer_id' => $customer->id]) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-shopping-cart me-2"></i>Proceed to Sale
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle me-3"></i>
                            <div>No balance stock available for this customer.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Previous Sales -->
        @if($customer->sales->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="stats-card">
                    <h5 class="mb-4">
                        <i class="fas fa-history me-2"></i>Purchase History
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Store</th>
                                    <th>Quantity</th>
                                    <th>Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->sales as $sale)
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
                                        <span class="badge bg-success">{{ $sale->quantity }} bags</span>
                                    </td>
                                    <td>
                                        <div>{{ $sale->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @else
        <!-- Customer Not Found -->
        <div class="row">
            <div class="col-12">
                <div class="stats-card text-center">
                    <div class="stats-icon mx-auto mb-4" style="background: linear-gradient(45deg, #e74c3c, #c0392b); width: 80px; height: 80px;">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <h4 class="mb-3">No Data Found</h4>
                    <p class="text-muted mb-4">Customer with document number "{{ request('document_number') }}" not found in the system.</p>
                    <a href="{{ route('store.customer.create', ['document_number' => request('document_number')]) }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Add Customer Data
                    </a>
                </div>
            </div>
        </div>
    @endif
@else
    <!-- Instructions -->
    <div class="row">
        <div class="col-12">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);">
                <div class="stats-icon mx-auto mb-4" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 80px; height: 80px;">
                    <i class="fas fa-search"></i>
                </div>
                <h4 class="mb-3">Search for Customer</h4>
                <p class="text-muted mb-0">Enter the document number above to find customer data and proceed with sales.</p>
            </div>
        </div>
    </div>
@endif
@endsection