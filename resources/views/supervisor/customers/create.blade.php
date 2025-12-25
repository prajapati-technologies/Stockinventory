@extends('layouts.app')

@section('title', 'Add Customer')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Add New Customer</h1>
            <a href="{{ route('supervisor.customers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Customers
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h5 class="mb-0">Customer Information</h5>
            </div>
            
            <form method="POST" action="{{ route('supervisor.customers.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Document Number <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input 
                                type="text" 
                                name="document_number" 
                                id="document_number"
                                value="{{ old('document_number') }}" 
                                required 
                                class="form-control @error('document_number') is-invalid @enderror"
                                placeholder="Enter document number"
                                onblur="checkDocumentNumber(this.value)"
                            >
                            <button type="button" class="btn btn-outline-primary" onclick="checkDocumentNumber(document.getElementById('document_number').value)">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        @error('document_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="documentStatus" class="form-text"></div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Customer Name</label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name') }}" 
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter customer name (optional)"
                        >
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Phone Number</label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone') }}" 
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="Enter phone number (optional)"
                        >
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Total Land (in acres) <span class="text-danger">*</span></label>
                        <input 
                            type="number" 
                            step="0.01" 
                            name="total_land" 
                            value="{{ old('total_land') }}" 
                            required 
                            class="form-control @error('total_land') is-invalid @enderror"
                            placeholder="Enter total land in acres"
                            onchange="calculateStock(this.value)"
                        >
                        @error('total_land')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="stockAllotment" class="form-text text-primary fw-bold"></div>
                    </div>

                    <input type="hidden" name="district_id" value="{{ $user->district_id }}">
                    <input type="hidden" name="mandal_id" value="{{ $user->mandal_id }}">

                    <div class="col-12">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-map-marker-alt me-3"></i>
                            <div>
                                <strong>Location:</strong> {{ $user->district->name }}, {{ $user->mandal->name }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('supervisor.customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-2"></i>Add Customer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function calculateStock(land) {
    land = parseFloat(land);
    let stock = 0;
    
    if (land <= 1.00) {
        stock = 2;
    } else if (land <= 1.20) {
        stock = 3;
    } else if (land <= 2.00) {
        stock = 4;
    } else if (land <= 2.20) {
        stock = 5;
    } else if (land <= 3.00) {
        stock = 6;
    } else if (land <= 3.20) {
        stock = 7;
    } else if (land <= 4.00) {
        stock = 8;
    } else if (land <= 4.20) {
        stock = 9;
    } else if (land <= 5.00) {
        stock = 10;
    } else if (land <= 5.20) {
        stock = 11;
    } else if (land <= 6.00) {
        stock = 12;
    } else if (land <= 6.20) {
        stock = 13;
    } else if (land <= 7.00) {
        stock = 14;
    } else {
        // For above 7.01, round to nearest number and multiply by 2
        stock = Math.round(land) * 2;
    }
    
    document.getElementById('stockAllotment').textContent = `Stock to be allotted: ${stock} bags`;
}

function checkDocumentNumber(documentNumber) {
    const statusDiv = document.getElementById('documentStatus');
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (!documentNumber) {
        statusDiv.innerHTML = '';
        submitBtn.disabled = false;
        return;
    }
    
    statusDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Checking document number...';
    
    fetch(`/api/check-document/${documentNumber}`)
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                statusDiv.innerHTML = `
                    <div class="alert alert-warning d-flex align-items-center mb-2">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <div>
                            <strong>Document number already exists!</strong><br>
                            <small>Customer: ${data.customer.name || 'N/A'} | Location: ${data.customer.district.name}, ${data.customer.mandal.name}</small>
                        </div>
                    </div>
                    <a href="/supervisor/customer/search?document_number=${documentNumber}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye me-2"></i>View Customer Details
                    </a>
                `;
                submitBtn.disabled = true;
            } else {
                statusDiv.innerHTML = '<i class="fas fa-check-circle text-success me-2"></i>Document number is available';
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            statusDiv.innerHTML = '<i class="fas fa-exclamation-circle text-danger me-2"></i>Error checking document number';
            submitBtn.disabled = false;
        });
}
</script>
@endsection