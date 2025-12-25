@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Reports</h1>
            <a href="{{ route('sub-admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h5 class="mb-0">Report Filters</h5>
            </div>
            
            <form method="POST" action="{{ route('sub-admin.reports.index') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">District Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" value="{{ $user->district->name ?? 'N/A' }}" disabled>
                        <div class="form-text">Example: {{ $user->district->name ?? 'Mahabubnagar' }}</div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Select Mandal <span class="text-danger">*</span></label>
                        @php
                            $selectedMandalIds = $selectedMandalIds ?? [];
                            $accessibleMandalIds = $accessibleMandals->pluck('id')->map(fn($id) => (int) $id)->toArray();
                            $allMandalsSelected = count($accessibleMandalIds) > 0
                                && count(array_intersect($selectedMandalIds, $accessibleMandalIds)) === count($accessibleMandalIds);
                        @endphp
                        <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                            @if(count($accessibleMandals) > 0)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="select_all_mandals"
                                           onchange="toggleAllMandals(this)" {{ $allMandalsSelected ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="select_all_mandals">
                                        Select All
                                    </label>
                                </div>
                                @foreach($accessibleMandals as $mandal)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="mandal_ids[]" 
                                           value="{{ $mandal->id }}" id="mandal_{{ $mandal->id }}"
                                           @if(in_array($mandal->id, $selectedMandalIds ?? [])) checked @endif
                                           onchange="handleMandalChange()">
                                    <label class="form-check-label" for="mandal_{{ $mandal->id }}">
                                        {{ $mandal->name }}
                                    </label>
                                </div>
                                @endforeach
                            @else
                                <div class="text-muted">No mandals available</div>
                            @endif
                        </div>
                        <div class="form-text">
                            @if(auth()->user()->hasRole('sub_admin_level_1'))
                                All mandals of the district
                            @else
                                Multiple mandals selected at the time of account creation will only show
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Select Stores</label>
                        <div id="stores-container" class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                            <div class="text-muted">Please select mandals first</div>
                        </div>
                        <div class="form-text">Stores of the above mandals only show</div>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">From Date</label>
                        <input type="date" name="date_from" value="{{ $dateFrom ?? request('date_from', request()->old('date_from')) }}" class="form-control">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label">To Date</label>
                        <input type="date" name="date_to" value="{{ $dateTo ?? request('date_to', request()->old('date_to')) }}" class="form-control">
                    </div>
                    
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Get Data
                        </button>
                    </div>
                </div>
            </form>
            
            <!-- Send Report to Mail -->
            @if($filtersApplied && $sales->count() > 0)
            <div class="mt-4 pt-4 border-top">
                <h6 class="mb-3">
                    <i class="fas fa-envelope me-2"></i>Send Report to Mail
                </h6>
                <form method="POST" action="{{ route('sub-admin.reports.send') }}" id="sendReportForm">
                    @csrf
                    <input type="hidden" name="format" value="excel">
                    <div class="row g-3">
                        <div class="col-md-9">
                            <label class="form-label">E-Mail ID <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Excel Report
                            </button>
                        </div>
                    </div>
                    @foreach($selectedMandalIds as $mandalId)
                        <input type="hidden" name="mandal_ids[]" value="{{ $mandalId }}">
                    @endforeach
                    @foreach($selectedStoreIds as $storeId)
                        <input type="hidden" name="store_ids[]" value="{{ $storeId }}">
                    @endforeach
                    <input type="hidden" name="date_from" value="{{ $dateFrom ?? request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ $dateTo ?? request('date_to') }}">
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Download Excel -->
@if($sales->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card" style="border: 2px solid #dc3545;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-1">
                        <i class="fas fa-download text-danger me-2"></i>Download Excel
                    </h6>
                    <small class="text-muted">
                        Columns: district, mandal, store name, document no, land(acres), qty, balance after, date & time
                    </small>
                </div>
                <form method="POST" action="{{ route('sub-admin.reports.export') }}" class="d-inline">
                    @csrf
                    @foreach($selectedMandalIds as $mandalId)
                        <input type="hidden" name="mandal_ids[]" value="{{ $mandalId }}">
                    @endforeach
                    @foreach($selectedStoreIds as $storeId)
                        <input type="hidden" name="store_ids[]" value="{{ $storeId }}">
                    @endforeach
                    <input type="hidden" name="date_from" value="{{ $dateFrom ?? request('date_from') }}">
                    <input type="hidden" name="date_to" value="{{ $dateTo ?? request('date_to') }}">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-file-excel me-2"></i>Download Excel
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Reports Table -->
@if($sales->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <h5 class="mb-3">
                <i class="fas fa-table me-2"></i>Reports Data
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date & Time</th>
                            <th>District</th>
                            <th>Mandal</th>
                            <th>Store Name</th>
                            <th>Document No</th>
                            <th>Land (Acres)</th>
                            <th>Qty</th>
                            <th>Balance After</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                        <tr>
                            <td>
                                <div>{{ $sale->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $sale->created_at->format('h:i A') }}</small>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $sale->customer->district->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge bg-secondary">{{ $sale->customer->mandal->name ?? 'N/A' }}</span>
                            </td>
                            <td>{{ $sale->store->name }}</td>
                            <td>
                                <span class="fw-bold">{{ $sale->customer->document_number }}</span>
                            </td>
                            <td>{{ $sale->customer->total_land }} acres</td>
                            <td>
                                <span class="badge bg-primary">{{ $sale->quantity }} bags</span>
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $sale->balance_after }} bags</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            @if($sales->hasPages())
            <div class="mt-4">
                {{ $sales->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
</div>
@elseif($filtersApplied)
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No data found</h5>
                <p class="text-muted">Please adjust your filters and try again</p>
            </div>
        </div>
    </div>
</div>
@endif

<script>
const availableStores = @json($stores);
const preselectedStoreIds = new Set((@json($selectedStoreIds ?? [])).map(id => id.toString()));
const preselectedMandalIds = new Set((@json($selectedMandalIds ?? [])).map(id => id.toString()));

// Restore selections from controller data on page load
function restoreSelections() {
    // Restore mandal checkboxes
    if (preselectedMandalIds.size > 0) {
        preselectedMandalIds.forEach(mandalId => {
            const checkbox = document.getElementById('mandal_' + mandalId);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        syncMandalSelectAll();
    }
    
    // Store selections will be restored when loadStores() is called
}

function loadStores() {
    const mandalCheckboxes = document.querySelectorAll('input[name="mandal_ids[]"]:checked');
    const selectedMandalIds = Array.from(mandalCheckboxes).map(cb => cb.value);
    const storesContainer = document.getElementById('stores-container');
    
    if (selectedMandalIds.length === 0) {
        preselectedStoreIds.clear();
        storesContainer.innerHTML = '<div class="text-muted">Please select mandals first</div>';
        syncStoreSelectAll();
        return;
    }
    
    storesContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading stores...</div>';
    
    const stores = availableStores.filter(store => selectedMandalIds.includes(store.mandal_id.toString()));
    
    if (stores.length > 0) {
        let html = '<div class="form-check mb-2">';
        html += '<input class="form-check-input" type="checkbox" id="select_all_stores" onchange="toggleAllStores(this)">';
        html += '<label class="form-check-label fw-bold" for="select_all_stores">Select All</label>';
        html += '</div>';
        
        stores.forEach(store => {
            const isChecked = preselectedStoreIds.has(store.id.toString());
            html += `
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="store_ids[]" 
                           value="${store.id}" id="store_${store.id}" ${isChecked ? 'checked' : ''} onchange="handleStoreChange()">
                    <label class="form-check-label" for="store_${store.id}">
                        ${store.name}
                    </label>
                </div>
            `;
        });
        storesContainer.innerHTML = html;
        syncStoreSelectAll();
        updatePreselectedStores();
    } else {
        preselectedStoreIds.clear();
        storesContainer.innerHTML = '<div class="text-muted">No stores found for selected mandals</div>';
        syncStoreSelectAll();
    }
}

function updatePreselectedStores() {
    preselectedStoreIds.clear();
    const selected = document.querySelectorAll('input[name="store_ids[]"]:checked');
    selected.forEach(cb => preselectedStoreIds.add(cb.value.toString()));
}

function handleMandalChange() {
    syncMandalSelectAll();
    loadStores();
}

function toggleAllMandals(checkbox) {
    const mandalCheckboxes = document.querySelectorAll('input[name="mandal_ids[]"]');
    mandalCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    checkbox.indeterminate = false;
    loadStores();
}

function syncMandalSelectAll() {
    const mandalCheckboxes = document.querySelectorAll('input[name="mandal_ids[]"]');
    const selectAll = document.getElementById('select_all_mandals');
    if (!selectAll) {
        return;
    }
    const total = mandalCheckboxes.length;
    const checked = Array.from(mandalCheckboxes).filter(cb => cb.checked).length;
    selectAll.checked = total > 0 && checked === total;
    selectAll.indeterminate = checked > 0 && checked < total;
}

function handleStoreChange() {
    updatePreselectedStores();
    syncStoreSelectAll();
}

function toggleAllStores(checkbox) {
    const storeCheckboxes = document.querySelectorAll('input[name="store_ids[]"]');
    storeCheckboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    checkbox.indeterminate = false;
    updatePreselectedStores();
    syncStoreSelectAll();
}

function syncStoreSelectAll() {
    const storeCheckboxes = document.querySelectorAll('input[name="store_ids[]"]');
    const selectAll = document.getElementById('select_all_stores');
    if (!selectAll) {
        return;
    }
    const total = storeCheckboxes.length;
    const checked = Array.from(storeCheckboxes).filter(cb => cb.checked).length;
    selectAll.checked = total > 0 && checked === total;
    selectAll.indeterminate = checked > 0 && checked < total;
}

window.addEventListener('DOMContentLoaded', function() {
    restoreSelections();
    syncMandalSelectAll();
    loadStores();
});
</script>
@endsection

