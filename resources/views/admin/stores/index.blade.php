@extends('layouts.app')

@section('title', 'Manage Stores')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h1 class="h3 mb-0 mb-2 mb-md-0">Stores</h1>
            <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                <button class="btn btn-success btn-sm flex-fill flex-md-fill-0" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-1 me-md-2"></i>
                    <span class="d-none d-md-inline">Upload Excel</span>
                    <span class="d-md-none">Upload</span>
                </button>
                <a href="{{ route('admin.stores.create') }}" class="btn btn-primary btn-sm flex-fill flex-md-fill-0">
                    <i class="fas fa-plus me-1 me-md-2"></i>
                    <span class="d-none d-md-inline">Add Store</span>
                    <span class="d-md-none">Add</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="stats-card mb-4">
    <h5 class="mb-3">
        <i class="fas fa-filter me-2"></i>Filters
    </h5>
    <form method="GET" action="{{ route('admin.stores.index') }}">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">District</label>
                <select name="district_id" id="district_id" class="form-select" onchange="loadMandals(this.value)">
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Mandal</label>
                <select name="mandal_id" id="mandal_id" class="form-select">
                    <option value="">All Mandals</option>
                    @foreach($mandals as $mandal)
                        <option value="{{ $mandal->id }}" {{ request('mandal_id') == $mandal->id ? 'selected' : '' }}>
                            {{ $mandal->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Phone" class="form-control">
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-search me-1"></i>
                    <span class="d-none d-sm-inline">Search</span>
                </button>
                <a href="{{ route('admin.stores.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="fas fa-times me-1"></i>
                    <span class="d-none d-sm-inline">Clear</span>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Stores Table -->
<div class="stats-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Store</th>
                    <th>Location</th>
                    <th>Phone</th>
                    <th>Valid Till</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stores as $store)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 40px; height: 40px;">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $store->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $store->district->name }}</div>
                        <small class="text-muted">{{ $store->mandal->name }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $store->phone_number }}</span>
                    </td>
                    <td>
                        <div>{{ $store->valid_till->format('d M Y') }}</div>
                        <small class="text-muted">{{ $store->valid_till->diffForHumans() }}</small>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.stores.toggle-status', $store) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $store->is_active ? 'btn-success' : 'btn-secondary' }} border-0" 
                                    onclick="return confirm('{{ $store->is_active ? 'Deactivate' : 'Activate' }} this store?')"
                                    title="{{ $store->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                <i class="fas {{ $store->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $store->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.stores.edit', $store) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-success" onclick="showExtendModal({{ $store->id }})">
                                <i class="fas fa-calendar-plus"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.stores.reset-password', $store) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Reset password to guest?')">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.stores.destroy', $store) }}" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <i class="fas fa-store fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No stores found</h5>
                        <p class="text-muted">Start by adding your first store</p>
                        <a href="{{ route('admin.stores.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Add Store
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($stores->hasPages())
    <div class="mt-4">
        {{ $stores->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Upload Stores Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.stores.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Excel File</label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-control">
                        <div class="form-text">
                            Columns: name, phone_number, district_id, mandal_id, validity_months
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Extend Validity Modal -->
<div class="modal fade" id="extendModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-plus me-2"></i>Extend Validity
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="extendForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Extend By</label>
                        <select name="validity_period" class="form-select">
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-calendar-plus me-2"></i>Extend
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadMandals(districtId) {
    const mandalSelect = document.getElementById('mandal_id');
    mandalSelect.innerHTML = '<option value="">All Mandals</option>';
    
    if (districtId) {
        fetch(`/admin/mandals/${districtId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(mandal => {
                    const option = document.createElement('option');
                    option.value = mandal.id;
                    option.textContent = mandal.name;
                    mandalSelect.appendChild(option);
                });
            });
    }
}

function showExtendModal(storeId) {
    document.getElementById('extendForm').action = `/admin/stores/${storeId}/extend-validity`;
    const modal = new bootstrap.Modal(document.getElementById('extendModal'));
    modal.show();
}
</script>
@endsection