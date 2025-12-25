@extends('layouts.app')

@section('title', 'Districts & Mandals')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <h1 class="h3 mb-0">Districts & Mandals</h1>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDistrictsModal">
                    <i class="fas fa-upload me-1 me-md-2"></i><span class="d-none d-sm-inline">Upload </span>Districts
                </button>
                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#uploadMandalsModal">
                    <i class="fas fa-upload me-1 me-md-2"></i><span class="d-none d-sm-inline">Upload </span>Mandals
                </button>
                <a href="{{ route('admin.districts.export') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-download me-1 me-md-2"></i><span class="d-none d-sm-inline">Export </span>Districts
                </a>
                <a href="{{ route('admin.mandals.export') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-download me-1 me-md-2"></i><span class="d-none d-sm-inline">Export </span>Mandals
                </a>
                <span class="badge bg-primary fs-6 py-2 px-3">{{ $districts->count() }} Districts</span>
                <span class="badge bg-success fs-6 py-2 px-3">{{ $districts->sum(function($d) { return $d->mandals->count(); }) }} Mandals</span>
            </div>
        </div>
    </div>
</div>

<!-- Add Forms -->
<div class="row mb-4">
    <!-- Add District -->
    <div class="col-lg-6 mb-4">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h5 class="mb-0">Add District</h5>
            </div>
            <form method="POST" action="{{ route('admin.districts.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">District Name</label>
                    <input type="text" name="district_name" placeholder="Enter district name" required class="form-control @error('district_name') is-invalid @enderror" value="{{ old('district_name') }}">
                    @error('district_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-plus me-2"></i>Add District
                </button>
            </form>
        </div>
    </div>

    <!-- Add Mandal -->
    <div class="col-lg-6 mb-4">
        <div class="stats-card h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-map-signs"></i>
                </div>
                <h5 class="mb-0">Add Mandal</h5>
            </div>
            <form method="POST" action="{{ route('admin.mandals.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Select District</label>
                    <select name="district_id" required class="form-select">
                        <option value="">Select District</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mandal Name</label>
                    <input type="text" name="mandal_name" placeholder="Enter mandal name" required class="form-control @error('mandal_name') is-invalid @enderror" value="{{ old('mandal_name') }}">
                    @error('mandal_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="fas fa-plus me-2"></i>Add Mandal
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Districts List -->
<div class="row">
    @foreach($districts as $district)
    <div class="col-12 mb-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="d-flex align-items-center">
                    <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 50px; height: 50px;">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h4 class="mb-1">{{ $district->name }} ({{ $district->id }})</h4>
                        <small class="text-muted">{{ $district->mandals->count() }} Mandals</small>
                    </div>
                </div>
                <form method="POST" action="{{ route('admin.districts.toggle', $district) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn {{ $district->is_active ? 'btn-success' : 'btn-danger' }} btn-sm">
                        <i class="fas {{ $district->is_active ? 'fa-check' : 'fa-times' }} me-1"></i>
                        {{ $district->is_active ? 'Active' : 'Inactive' }}
                    </button>
                </form>
            </div>
            
            <div class="row">
                @forelse($district->mandals as $mandal)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">{{ $mandal->name }} ({{ $mandal->id }})</h6>
                                    <small class="text-muted">Mandal</small>
                                </div>
                                <form method="POST" action="{{ route('admin.mandals.toggle', $mandal) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn {{ $mandal->is_active ? 'btn-outline-success' : 'btn-outline-danger' }} btn-sm">
                                        <i class="fas {{ $mandal->is_active ? 'fa-check' : 'fa-times' }}"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-4">
                        <i class="fas fa-map-signs fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No mandals added yet</p>
                        <small class="text-muted">Add mandals using the form above</small>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($districts->count() == 0)
<div class="row">
    <div class="col-12">
        <div class="stats-card text-center">
            <i class="fas fa-map fa-4x text-muted mb-4"></i>
            <h4 class="text-muted mb-3">No Districts Added Yet</h4>
            <p class="text-muted mb-4">Start by adding your first district using the form above.</p>
            <button class="btn btn-primary" onclick="document.querySelector('input[name=name]').focus()">
                <i class="fas fa-plus me-2"></i>Add First District
            </button>
        </div>
    </div>
</div>
@endif

<!-- Upload Districts Modal -->
<div class="modal fade" id="uploadDistrictsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Upload Districts from Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.districts.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Excel Format Required:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Column A: <strong>name</strong> (District Name)</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Choose Excel File</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-upload me-2"></i>Upload Districts
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Upload Mandals Modal -->
<div class="modal fade" id="uploadMandalsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-upload me-2"></i>Upload Mandals from Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.mandals.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Excel Format Required:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Column A: <strong>district_name</strong> (District Name)</li>
                            <li>Column B: <strong>name</strong> (Mandal Name)</li>
                        </ul>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Choose Excel File</label>
                        <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-upload me-2"></i>Upload Mandals
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
