@extends('layouts.app')

@section('title', 'Upload Customers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Upload Customers</h1>
            <a href="{{ route('sub-admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
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

<div class="row">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-upload"></i>
                </div>
                <div>
                    <h5 class="mb-0">Upload Customer Excel</h5>
                    <small class="text-muted">Only Sub Admin Level-1 can perform bulk uploads</small>
                </div>
            </div>

            <form method="POST" action="{{ route('sub-admin.customers.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label">Excel File <span class="text-danger">*</span></label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-control @error('excel_file') is-invalid @enderror">
                        @error('excel_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text mt-2">
                            Columns: document_number, name, phone, district_id, mandal_id, total_land
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('sub-admin.customers.template.download') }}" class="btn btn-outline-danger">
                        <i class="fas fa-download me-2"></i>Download Template
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload Customers
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="stats-card h-100">
            <h6 class="mb-3">
                <i class="fas fa-info-circle me-2 text-primary"></i>Bulk Upload Guidelines
            </h6>
            <ul class="list-unstyled mb-0">
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    Ensure the Excel file follows the provided template.
                </li>
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    Document numbers must be unique for each customer.
                </li>
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    District and Mandal IDs should match existing records.
                </li>
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    Total land value must be a valid number (acres).
                </li>
                <li class="mb-0">
                    <i class="fas fa-phone text-info me-2"></i>
                    Contact the Admin team if you need help preparing the file.
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection

