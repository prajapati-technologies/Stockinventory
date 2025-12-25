@extends('layouts.app')

@section('title', 'Create Sale')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Complete Sale</h1>
            <a href="{{ route('store.customer.search') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Search
            </a>
        </div>
    </div>
</div>

<!-- Customer Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white;">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 50px; height: 50px;">
                    <i class="fas fa-user"></i>
                </div>
                <h5 class="mb-0">Customer Information</h5>
            </div>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">Document Number</h6>
                            <p class="mb-0 fw-bold">{{ $customer->document_number }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">Total Land</h6>
                            <p class="mb-0 fw-bold">{{ $customer->total_land }} acres</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">Location</h6>
                            <p class="mb-0 fw-bold">{{ $customer->district->name }}, {{ $customer->mandal->name }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Information -->
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
                    <div class="text-muted">Already Availed</div>
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
                    <div class="stats-number text-success">{{ $balanceStock }}</div>
                    <div class="text-muted">Balance Available</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sales from Other Stores -->
@if($otherStoresSales->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <h5 class="mb-4">
                <i class="fas fa-history me-2"></i>Previous Purchases from Other Stores
            </h5>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Store Name</th>
                            <th>Quantity</th>
                            <th>Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($otherStoresSales as $sale)
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

<!-- Sale Form -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <h5 class="mb-0">Enter Sale Details</h5>
            </div>
            
            @if($balanceStock > 0)
                @if(isset($restrictionMessage) && !$canSell)
                    <div class="alert alert-warning d-flex align-items-center mb-4">
                        <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                        <div>
                            <strong>Sale Restriction:</strong> {{ $restrictionMessage }}
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('store.sale.store') }}">
                    @csrf
                    <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Quantity (bags) <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                name="quantity" 
                                min="1" 
                                max="{{ $maxQuantity ?? $balanceStock }}" 
                                value="{{ old('quantity') }}" 
                                required 
                                class="form-control form-control-lg @error('quantity') is-invalid @enderror"
                                onchange="updateBalance(this.value)"
                                @if(isset($canSell) && !$canSell) disabled @endif
                            >
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Available balance: {{ $balanceStock }} bags</div>
                            @if(isset($condition) && isset($condition['at_a_time']))
                                @if(isset($intervalInfo))
                                    <div class="form-text text-success fw-bold mt-2">
                                        <i class="fas fa-check-circle me-1"></i>
                                        {{ $intervalInfo }}
                                    </div>
                                @else
                                    <div class="form-text text-info">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Maximum {{ $condition['at_a_time'] }} bags can be sold at a time for this district.
                                    </div>
                                @endif
                            @endif
                            @if(isset($restrictionMessage) && !$canSell)
                                <div class="form-text text-danger fw-bold mt-2">
                                    <i class="fas fa-ban me-1"></i>
                                    {{ $restrictionMessage }}
                                </div>
                            @endif
                            <div id="newBalance" class="form-text text-primary fw-bold"></div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('store.customer.search') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back
                        </a>
                        <button type="submit" class="btn btn-success btn-lg" @if(isset($canSell) && !$canSell) disabled @endif>
                            <i class="fas fa-check me-2"></i>Complete Sale
                        </button>
                    </div>
                </form>
            @else
                <div class="alert alert-warning d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle me-3"></i>
                    <div>No balance stock available for this customer.</div>
                </div>
                <div class="d-flex justify-content-end">
                    <a href="{{ route('store.customer.search') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Search
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function updateBalance(quantity) {
    const balance = {{ $balanceStock }};
    const maxQuantity = {{ $maxQuantity ?? $balanceStock }};
    const quantityValue = parseInt(quantity) || 0;
    
    if (quantityValue > maxQuantity) {
        document.getElementById('newBalance').innerHTML = `<span class="text-danger">Maximum ${maxQuantity} bags allowed at a time.</span>`;
        return;
    }
    
    const newBalance = balance - quantityValue;
    document.getElementById('newBalance').textContent = `New balance will be: ${newBalance} bags`;
}
</script>
@endsection