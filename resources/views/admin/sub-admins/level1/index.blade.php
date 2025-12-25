@extends('layouts.app')

@section('title', 'Sub Admin Level-1')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h1 class="h3 mb-0 mb-2 mb-md-0">Sub Admin Level-1</h1>
            <div class="d-flex flex-wrap gap-2 w-100 w-md-auto">
                <a href="{{ route('admin.sub-admins.level1.create') }}" class="btn btn-primary btn-sm flex-fill flex-md-fill-0">
                    <i class="fas fa-plus me-1 me-md-2"></i>
                    <span class="d-none d-md-inline">Create Sub Admin Level-1</span>
                    <span class="d-md-none">Create</span>
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
    <form method="GET" action="{{ route('admin.sub-admins.level1.index') }}">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">District</label>
                <select name="district_id" class="form-select">
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label class="form-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or Phone" class="form-control">
            </div>

            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-search me-1"></i>Search
                </button>
                <a href="{{ route('admin.sub-admins.level1.index') }}" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="fas fa-times me-1"></i>Clear
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Sub-Admins Table -->
<div class="stats-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>District</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subAdmins as $subAdmin)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="stats-icon me-3" style="background: linear-gradient(45deg, #16a085, #138d75); width: 40px; height: 40px;">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <div class="fw-bold">{{ $subAdmin->name }}</div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">{{ $subAdmin->phone_number }}</span>
                    </td>
                    <td>
                        <span class="badge bg-info">{{ $subAdmin->district->name ?? 'N/A' }}</span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.sub-admins.level1.toggle-status', $subAdmin) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $subAdmin->is_active ? 'btn-success' : 'btn-secondary' }} border-0" 
                                    onclick="return confirm('{{ $subAdmin->is_active ? 'Deactivate' : 'Activate' }} this sub-admin?')"
                                    title="{{ $subAdmin->is_active ? 'Click to deactivate' : 'Click to activate' }}">
                                <i class="fas {{ $subAdmin->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                                {{ $subAdmin->is_active ? 'Active' : 'Inactive' }}
                            </button>
                        </form>
                    </td>
                    <td class="text-end">
                        <div class="btn-group" role="group">
                            <a href="{{ route('admin.sub-admins.level1.edit', $subAdmin) }}" class="btn btn-sm btn-outline-primary" title="Edit Sub Admin">
                                <i class="fas fa-edit me-1"></i>Edit
                            </a>
                            <form method="POST" action="{{ route('admin.sub-admins.level1.reset-password', $subAdmin) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-warning" onclick="return confirm('Reset password to guest?')" title="Reset Password">
                                    <i class="fas fa-key me-1"></i>Reset
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5">
                        <i class="fas fa-user-shield fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No sub-admins found</h5>
                        <p class="text-muted">Start by creating your first sub-admin level-1</p>
                        <a href="{{ route('admin.sub-admins.level1.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Create Sub Admin Level-1
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($subAdmins->hasPages())
    <div class="mt-4">
        {{ $subAdmins->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

