@extends('layouts.app')

@section('title', 'Customer Search')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Customer Search</h1>
            <div class="d-flex gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#uploadCustomersModal">
                    <i class="fas fa-upload me-2"></i>Upload Customers
                </button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="row">
    <div class="col-12">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="row">
    <div class="col-12">
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

<!-- Customer Search -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-search"></i>
                </div>
                <h5 class="mb-0">Customer Search</h5>
            </div>
            
            <form method="GET" action="{{ route('admin.customers.search') }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Document Number</label>
                        <input 
                            type="text" 
                            name="document_number" 
                            value="{{ $documentNumber }}" 
                            class="form-control @error('document_number') is-invalid @enderror"
                            placeholder="Enter document number"
                            required
                        >
                        @error('document_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary flex-fill">
                            <i class="fas fa-search me-2"></i>Get Data
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@if($customer)
    <!-- Customer Details -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                            <i class="fas fa-user"></i>
                        </div>
                        <h5 class="mb-0">Customer Details</h5>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-success">Data Found</span>
                        <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this customer? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-2"></i>Delete
                            </button>
                        </form>
                    </div>
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
                        <div class="text-muted">Stock Already Availed</div>
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
                        <div class="text-muted">Balance Stock</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Bags Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Additional Bags Allotted
                    </h5>
                    @if($customer->additionalBags && $customer->additionalBags->count() > 0)
                        <span class="badge bg-success">{{ $customer->additionalBags->sum('additional_bags') }} Total Additional Bags</span>
                    @endif
                </div>
                
                @if($customer->additionalBags && $customer->additionalBags->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                    <td>
                                        @if($additionalBag->remarks)
                                            <span title="{{ $additionalBag->remarks }}">{{ Str::limit($additionalBag->remarks, 50) }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="stats-icon me-2" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 30px; height: 30px;">
                                                <i class="fas fa-user"></i>
                                            </div>
                                            <span class="fw-bold">{{ $additionalBag->addedBy->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $additionalBag->created_at->format('d M Y') }}</div>
                                        <small class="text-muted">{{ $additionalBag->created_at->format('h:i A') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-info">
                                    <td colspan="2" class="text-end"><strong>Total:</strong></td>
                                    <td colspan="3"><strong>{{ $customer->additionalBags->sum('additional_bags') }} additional bags</strong></td>
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
    </div>

    <!-- Purchase History -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Purchase History
                    </h5>
                    <span class="badge bg-primary">{{ $customer->sales->count() }} Total Purchases</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Store</th>
                                <th>District</th>
                                <th>Mandal</th>
                                <th>Quantity</th>
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
                                        <span class="fw-bold">{{ $sale->store->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $sale->store->district->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $sale->store->mandal->name ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $sale->quantity }} bags</span>
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

@elseif($documentNumber)
    <!-- No Customer Found -->
    <div class="row">
        <div class="col-12">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
                <div class="stats-icon mx-auto mb-4" style="background: linear-gradient(45deg, #dc3545, #c82333); width: 80px; height: 80px;">
                    <i class="fas fa-user-times"></i>
                </div>
                <h4 class="mb-3 text-danger">Customer Not Found</h4>
                <p class="text-muted mb-4">No customer found with document number: <strong>{{ $documentNumber }}</strong></p>
                <a href="{{ route('admin.customers.search') }}" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Search Again
                </a>
            </div>
        </div>
    </div>
@else
    <!-- Search Instructions -->
    <div class="row">
        <div class="col-12">
            <div class="stats-card text-center" style="background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%);">
                <div class="stats-icon mx-auto mb-4" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 80px; height: 80px;">
                    <i class="fas fa-search"></i>
                </div>
                <h4 class="mb-3">Search Customer Data</h4>
                <p class="text-muted mb-0">Enter a document number above to search for customer information and purchase history</p>
            </div>
        </div>
    </div>
@endif

<div class="modal fade" id="uploadCustomersModal" tabindex="-1" aria-labelledby="uploadCustomersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadCustomersModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Customers Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('admin.customers.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Excel File <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-control">
                        <div class="form-text">Columns: document_number, name, phone, district_id, mandal_id, total_land</div>
                    </div>
                    <div class="mb-2">
                        <a href="{{ route('admin.customers.template.download') }}" class="btn btn-outline-danger w-100">
                            <i class="fas fa-download me-2"></i>Download Template
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

