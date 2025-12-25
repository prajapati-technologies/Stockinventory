@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Edit Profile</h1>
            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-user-edit"></i>
                </div>
                <h5 class="mb-0">Personal Information</h5>
            </div>
            
            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required 
                               class="form-control @error('name') is-invalid @enderror" 
                               placeholder="Enter your full name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required 
                               class="form-control @error('phone_number') is-invalid @enderror" 
                               placeholder="Enter your phone number">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">District</label>
                        <select name="district_id" id="district_id" 
                                class="form-select @error('district_id') is-invalid @enderror" 
                                onchange="loadMandals(this.value)">
                            <option value="">Select District</option>
                            @foreach(\App\Models\District::all() as $district)
                                <option value="{{ $district->id }}" {{ old('district_id', $user->district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('district_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Mandal</label>
                        <select name="mandal_id" id="mandal_id" 
                                class="form-select @error('mandal_id') is-invalid @enderror">
                            <option value="">Select Mandal</option>
                        </select>
                        @error('mandal_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadMandals(districtId) {
    const mandalSelect = document.getElementById('mandal_id');
    mandalSelect.innerHTML = '<option value="">Select Mandal</option>';
    
    if (districtId) {
        fetch(`/admin/mandals/${districtId}`)
            .then(response => response.json())
            .then(data => {
                data.forEach(mandal => {
                    const option = document.createElement('option');
                    option.value = mandal.id;
                    option.textContent = mandal.name;
                    option.selected = mandal.id == {{ old('mandal_id', $user->mandal_id) }};
                    mandalSelect.appendChild(option);
                });
            });
    }
}

// Load mandals on page load
document.addEventListener('DOMContentLoaded', function() {
    const districtId = document.getElementById('district_id').value;
    if (districtId) {
        loadMandals(districtId);
    }
});
</script>
@endsection
