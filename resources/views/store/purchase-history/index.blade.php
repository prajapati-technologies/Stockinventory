@extends('layouts.app')

@section('title', 'Purchase History')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h1 class="h3 mb-0 mb-2 mb-md-0">
                <i class="fas fa-history me-2"></i>Purchase History
            </h1>
        </div>
    </div>
</div>

<!-- Search Form -->
<div class="stats-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-search me-2"></i>Search by Document Number
    </h5>
    <form method="GET" action="{{ route('store.purchase-history.index') }}">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label">Document Number</label>
                <input 
                    type="text" 
                    name="document_number" 
                    class="form-control" 
                    placeholder="Enter document number"
                    value="{{ $documentNumber }}"
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

<!-- Purchase History Table -->
@if($documentNumber)
    @if($sales->isEmpty())
        <div class="stats-card">
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No purchase history found</h5>
                <p class="text-muted">No sales records found for document number: <strong>{{ $documentNumber }}</strong> in your store</p>
            </div>
        </div>
    @else
        @php
            $customer = $sales->first()->customer;
        @endphp
        
        <!-- Customer Information -->
        <div class="stats-card mb-4">
            <h5 class="mb-3">
                <i class="fas fa-user me-2"></i>Customer Information
            </h5>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Document Number:</strong> {{ $customer->document_number }}
                </div>
                @if($customer->name)
                <div class="col-md-6 mb-3">
                    <strong>Name:</strong> {{ $customer->name }}
                </div>
                @endif
                @if($customer->phone)
                <div class="col-md-6 mb-3">
                    <strong>Phone:</strong> {{ $customer->phone }}
                </div>
                @endif
                <div class="col-md-6 mb-3">
                    <strong>District:</strong> {{ $customer->district->name ?? 'N/A' }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Mandal:</strong> {{ $customer->mandal->name ?? 'N/A' }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Total Land:</strong> {{ number_format($customer->total_land, 2) }} acres
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Total Stock Allotted:</strong> {{ $customer->total_stock_allotted }} bags
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Stock Availed:</strong> {{ $customer->stock_availed }} bags
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Balance Stock:</strong> {{ $customer->balance_stock }} bags
                </div>
            </div>
            
            <!-- Additional Bags Section -->
            @if($customer->additionalBags && $customer->additionalBags->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <h6 class="mb-3">
                        <i class="fas fa-plus-circle me-2"></i>Additional Bags Allotted
                    </h6>
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
                </div>
            </div>
            @else
            <div class="row mt-4">
                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>No additional bags allotted to this customer.
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Purchase History Table -->
        <div class="stats-card">
            <h5 class="mb-3">
                <i class="fas fa-list me-2"></i>Purchase History ({{ $sales->count() }} records)
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Date & Time</th>
                            <th>Quantity (Bags)</th>
                            <th>Balance Before</th>
                            <th>Balance After</th>
                            <th>Sold By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $index => $sale)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $sale->created_at->format('d-m-Y h:i A') }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $sale->quantity }}</span>
                            </td>
                            <td>{{ $sale->balance_before ?? 'N/A' }}</td>
                            <td>{{ $sale->balance_after ?? 'N/A' }}</td>
                            <td>{{ $sale->user->name ?? 'N/A' }}</td>
                            <td>
                                <button type="button" class="btn btn-info btn-sm" onclick="viewSaleDetails({{ $sale->id }})" title="View Details">
                                    <i class="fas fa-eye me-1"></i>View
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <td colspan="2" class="text-end"><strong>Total:</strong></td>
                            <td><strong>{{ $sales->sum('quantity') }} bags</strong></td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endif
@else
    <div class="stats-card">
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Search Purchase History</h5>
            <p class="text-muted">Enter a document number above to view purchase history from your store</p>
        </div>
    </div>
@endif

<!-- View Sale Details Modal -->
<div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSaleModalLabel">
                    <i class="fas fa-eye me-2"></i>View Sale Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="saleDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewSaleDetails(saleId) {
    // Get sale data from the table row
    const row = event.target.closest('tr');
    const saleData = {
        date: row.cells[1].textContent,
        quantity: row.cells[2].querySelector('.badge').textContent,
        balanceBefore: row.cells[3].textContent,
        balanceAfter: row.cells[4].textContent,
        soldBy: row.cells[5].textContent
    };
    
    const content = `
        <div class="row">
            <div class="col-md-6 mb-3">
                <strong>Date & Time:</strong> ${saleData.date}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Store Name:</strong> {{ $store->name }}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Quantity:</strong> ${saleData.quantity}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Balance Before:</strong> ${saleData.balanceBefore}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Balance After:</strong> ${saleData.balanceAfter}
            </div>
            <div class="col-md-6 mb-3">
                <strong>Sold By:</strong> ${saleData.soldBy}
            </div>
        </div>
    `;
    
    document.getElementById('saleDetailsContent').innerHTML = content;
    const modal = new bootstrap.Modal(document.getElementById('viewSaleModal'));
    modal.show();
}
</script>
@endsection

