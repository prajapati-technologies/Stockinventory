@extends('layouts.app')

@section('title', 'Manage Supervisors')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h1 class="h3 mb-0 mb-2 mb-md-0">Supervisors</h1>
            <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                <button class="btn btn-success btn-sm flex-fill flex-md-fill-0" data-bs-toggle="modal" data-bs-target="#uploadModal">
                    <i class="fas fa-upload me-1 me-md-2"></i>
                    <span class="d-none d-md-inline">Upload Excel</span>
                    <span class="d-md-none">Upload</span>
                </button>
                <a href="{{ route('admin.supervisors.create') }}" class="btn btn-primary btn-sm flex-fill flex-md-fill-0">
                    <i class="fas fa-plus me-1 me-md-2"></i>
                    <span class="d-none d-md-inline">Add Supervisor</span>
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
    <form method="GET" action="{{ route('admin.supervisors.index') }}">
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
                <a href="{{ route('admin.supervisors.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="fas fa-times me-1"></i>
                    <span class="d-none d-sm-inline">Clear</span>
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Supervisors Table -->
<div class="stats-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Supervisor</th>
                    <th>Location</th>
                    <th>Phone</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($supervisors as $supervisor)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 40px; height: 40px;">
                                <i class="fas fa-user-tie"></i>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $supervisor->name }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div>{{ $supervisor->district->name }}</div>
                        <small class="text-muted">{{ $supervisor->mandal->name }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $supervisor->phone_number }}</span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.supervisors.toggle-status', $supervisor) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $supervisor->is_active ? 'btn-success' : 'btn-secondary' }} border-0" 
                                    onclick="return confirm('{{ $supervisor->is_active ? 'Deactivate' : 'Activate' }} this supervisor?')"
                                    title="{{ $supervisor->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                <i class="fas {{ $supervisor->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $supervisor->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.supervisors.edit', $supervisor) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('admin.supervisors.reset-password', $supervisor) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Reset password to guest?')">
                                    <i class="fas fa-key"></i>
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.supervisors.destroy', $supervisor) }}" class="d-inline">
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
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No supervisors found</h5>
                        <p class="text-muted">Start by adding your first supervisor</p>
                        <a href="{{ route('admin.supervisors.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Add Supervisor
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($supervisors->hasPages())
    <div class="mt-4">
        {{ $supervisors->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

<!-- Upload Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Upload Supervisors Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.supervisors.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Excel File</label>
                        <input type="file" name="excel_file" accept=".xlsx,.xls,.csv" required class="form-control">
                        <div class="form-text">
                            Columns: name, phone_number, district_id, mandal_id
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
</script>
@endsection