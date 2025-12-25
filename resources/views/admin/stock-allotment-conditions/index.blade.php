@extends('layouts.app')

@section('title', 'Stock Allotment Conditions')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <h1 class="h3 mb-0 mb-2 mb-md-0">
                <i class="fas fa-cog me-2"></i>Stock Allotment Conditions
            </h1>
            <a href="{{ route('admin.stock-allotment-conditions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add Condition
            </a>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="stats-card mb-4">
    <form method="GET" action="{{ route('admin.stock-allotment-conditions.index') }}">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Filter by District</label>
                <select name="district_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                            {{ $district->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>
</div>

<!-- Conditions List -->
@if($conditions->count() > 0)
    <div class="stats-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>District</th>
                        <th>Land Extent From</th>
                        <th>Land Extent To</th>
                        <th>No. of Bags</th>
                        <th>At a Time</th>
                        <th>Interval (Days)</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conditions as $index => $condition)
                    <tr>
                        <td>{{ $conditions->firstItem() + $index }}</td>
                        <td><strong>{{ $condition->district->name }}</strong></td>
                        <td>{{ number_format($condition->land_extent_from, 2) }}</td>
                        <td>{{ number_format($condition->land_extent_to, 2) }}</td>
                        <td><span class="badge bg-primary">{{ $condition->number_of_bags }}</span></td>
                        <td>{{ $condition->at_a_time_how_many }}</td>
                        <td>{{ $condition->interval_time_days }}</td>
                        <td>
                            @if($condition->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.stock-allotment-conditions.edit', $condition) }}" 
                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" 
                                      action="{{ route('admin.stock-allotment-conditions.destroy', $condition) }}" 
                                      class="d-inline"
                                      onsubmit="return confirm('Are you sure you want to delete all conditions for {{ $condition->district->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $conditions->links() }}
        </div>
    </div>
@else
    <div class="stats-card">
        <div class="text-center py-5">
            <i class="fas fa-cog fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">No Conditions Found</h5>
            <p class="text-muted">Start by adding stock allotment conditions for districts.</p>
            <a href="{{ route('admin.stock-allotment-conditions.create') }}" class="btn btn-primary mt-3">
                <i class="fas fa-plus me-2"></i>Add Condition
            </a>
        </div>
    </div>
@endif
@endsection

