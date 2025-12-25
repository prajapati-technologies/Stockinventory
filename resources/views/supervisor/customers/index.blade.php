@extends('layouts.app')

@section('title', 'Customer Edit')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Customer Edit</h1>
            <div class="d-flex gap-2">
                {{-- <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-2"></i>Upload Excel
                </button> --}}
                <a href="{{ route('supervisor.customers.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Add Customer
                </a>
            </div>
        </div>
        <div class="alert alert-info d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <span>Bulk customer uploads are now handled by the Admin team. Please contact the admin if you need assistance.</span>
        </div>
    </div>
</div>

<!-- Search -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 40px; height: 40px;">
                    <i class="fas fa-search"></i>
                </div>
                <h6 class="mb-0">Search Customers</h6>
            </div>
            
            <form method="GET" action="{{ route('supervisor.customers.index') }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}" 
                            placeholder="Search by Document Number" 
                            class="form-control"
                        >
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>Search
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('supervisor.customers.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Excel Upload Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 40px; height: 40px;">
                    <i class="fas fa-file-excel"></i>
                </div>
                <h6 class="mb-0">Excel Upload to Add Additional Bags</h6>
            </div>
            
            <div class="alert alert-info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Excel Format:</strong> Document number | Add bags | Remarks
                <div class="mt-2">
                    <a href="{{ route('supervisor.customers.additional-bags-template.download') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download me-2"></i>Download Template
                    </a>
                </div>
            </div>
            
            <form method="POST" action="{{ route('supervisor.customers.upload-additional-bags') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-9">
                        <label class="form-label">Upload Excel File <span class="text-danger">*</span></label>
                        <input 
                            type="file" 
                            name="excel_file" 
                            accept=".xlsx,.xls,.csv" 
                            required 
                            class="form-control @error('excel_file') is-invalid @enderror"
                        >
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Columns required: <strong>document_number</strong>, <strong>add_bags</strong>, <strong>remarks</strong>
                        </small>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-upload me-2"></i>Upload
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Customers Table -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-users me-2"></i>Customer Records
                    @if(request('search'))
                        <small class="text-muted ms-2">
                            <i class="fas fa-filter me-1"></i>Filtered by: {{ request('search') }}
                        </small>
                    @endif
                </h5>
                @if(request('search'))
                    <span class="badge bg-primary">{{ $customers->total() }} Result</span>
                @endif
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Document Number</th>
                            <th>Total Land</th>
                            <th>Stock Allotted</th>
                            <th>Stock Availed</th>
                            <th>Balance</th>
                            <th>Additional Bags</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 30px; height: 30px;">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <span class="fw-bold">{{ $customer->document_number }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $customer->total_land }} acres</span>
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $customer->total_stock_allotted }} bags</span>
                            </td>
                            <td>
                                <span class="badge bg-warning">{{ $customer->stock_availed }} bags</span>
                            </td>
                            <td>
                                <span class="badge {{ $customer->balance_stock > 0 ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $customer->balance_stock }} bags
                                </span>
                            </td>
                            <td>
                                @if($customer->additionalBags->count() > 0)
                                    <div class="small">
                                        @foreach($customer->additionalBags as $additionalBag)
                                            <div class="mb-1">
                                                <span class="badge bg-success">+{{ $additionalBag->additional_bags }} bags</span>
                                                @if($additionalBag->remarks)
                                                    <br><small class="text-muted" title="{{ $additionalBag->remarks }}">
                                                        <i class="fas fa-comment me-1"></i>{{ Str::limit($additionalBag->remarks, 30) }}
                                                    </small>
                                                @endif
                                                <br><small class="text-muted">{{ $additionalBag->created_at->format('d M Y') }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('supervisor.customers.edit', $customer) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-edit me-1"></i>Edit Stock
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                @if(request('search'))
                                    <h5 class="text-muted">No customer found</h5>
                                    <p class="text-muted">No customer found with document number: <strong>{{ request('search') }}</strong></p>
                                    <a href="{{ route('supervisor.customers.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-2"></i>Clear Search
                                    </a>
                                @else
                                    <h5 class="text-muted">Search for a customer</h5>
                                    <p class="text-muted">Enter a document number above to view customer details</p>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($customers->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- <!-- Upload Modal (disabled for supervisors)
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">
                    <i class="fas fa-upload me-2"></i>Upload Customers Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('supervisor.customers.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Excel File <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-control">
                        <div class="form-text">Columns: document_number, name, phone, district_id, mandal_id, total_land</div>
                        <div class="mt-2">
                            <a href="{{ route('supervisor.customers.template.download') }}" class="text-danger text-decoration-none fw-bold" style="border: 2px solid #dc3545; padding: 8px 12px; border-radius: 4px; display: inline-block; background-color: #fff;">
                                <i class="fas fa-download me-2"></i>Download the template
                            </a>
                        </div>
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
--}} 

@endsection