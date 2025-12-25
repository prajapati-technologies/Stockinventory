@extends('layouts.app')

@section('title', 'Edit Customer Stock')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Modify Stock Allocation</h1>
            <a href="{{ route('supervisor.customers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Customers
            </a>
        </div>
    </div>
</div>

<!-- Customer Details -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-user"></i>
                </div>
                <h5 class="mb-0">Customer Details</h5>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
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
                
                <div class="col-md-6">
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
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #f39c12, #e67e22); width: 40px; height: 40px;">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Current Allotted Stock</h6>
                            <p class="mb-0 fw-bold text-warning">{{ $customer->total_stock_allotted }} bags</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 40px; height: 40px;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Already Availed</h6>
                            <p class="mb-0 fw-bold text-purple">{{ $customer->stock_availed }} bags</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Modification Form -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #e74c3c, #c0392b); width: 50px; height: 50px;">
                    <i class="fas fa-edit"></i>
                </div>
                <h5 class="mb-0">Modify Stock Allocation</h5>
            </div>
            
            <form method="POST" action="{{ route('supervisor.customers.update', $customer) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">New Total Stock Allotted <span class="text-danger">*</span></label>
                        <input 
                            type="number" 
                            name="total_stock_allotted" 
                            value="{{ old('total_stock_allotted', $customer->total_stock_allotted) }}" 
                            min="0"
                            required 
                            class="form-control form-control-lg @error('total_stock_allotted') is-invalid @enderror"
                            placeholder="Enter new total stock allocation"
                        >
                        @error('total_stock_allotted')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="alert alert-warning d-flex align-items-center mt-4">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div>
                        <strong>Note:</strong> Changing this will affect the customer's available balance for future purchases.
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('supervisor.customers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Stock Allocation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection