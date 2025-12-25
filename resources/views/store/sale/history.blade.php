@extends('layouts.app')

@section('title', 'Sales History')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Sales History</h1>
            <a href="{{ route('store.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Sales Records
                </h5>
                <div class="d-flex gap-2 align-items-center">
                    <span class="badge bg-primary">{{ $sales->total() }} Total Sales</span>
                    <a href="{{ route('store.sale.export') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Export to Excel
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Document Number</th>
                            <th>Customer Location</th>
                            <th>Quantity</th>
                            <th>Balance After</th>
                            <th>Date & Time</th>
                            <th>Sold By</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 30px; height: 30px;">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <span class="fw-bold">{{ $sale->customer->document_number }}</span>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="fw-bold">{{ $sale->customer->district->name }}</div>
                                    <small class="text-muted">{{ $sale->customer->mandal->name }}</small>
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
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 30px; height: 30px;">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <span class="fw-bold">{{ $sale->user->name }}</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No sales recorded yet</h5>
                                <p class="text-muted">Start by adding your first customer</p>
                                <a href="{{ route('store.customer.search') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Add Customer
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($sales->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $sales->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection