@extends('layouts.app')

@section('title', 'Create Sub Admin Level-2')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Create Sub Admin Level-2</h1>
            <a href="{{ route('admin.sub-admins.level2.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Sub Admins
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #27ae60, #229954); width: 50px; height: 50px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h5 class="mb-0">Sub Admin Level-2 Information</h5>
            </div>
            
            <form method="POST" action="{{ route('admin.sub-admins.level2.store') }}">
                @csrf
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" required 
                               class="form-control @error('name') is-invalid @enderror" 
                               placeholder="Enter name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" value="{{ old('phone_number') }}" required 
                               class="form-control @error('phone_number') is-invalid @enderror" 
                               placeholder="Enter mobile number">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select name="district_id" id="district_id" required 
                                class="form-select @error('district_id') is-invalid @enderror" 
                                onchange="loadMandals(this.value)">
                            <option value="">Select District</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Example: Mahabubnagar</div>
                        @error('district_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Select Mandals <span class="text-danger">*</span></label>
                        <div id="mandals-container" class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="text-muted text-center">Please select a district first</div>
                        </div>
                        <div class="form-text">Allow multiple selection with checkboxes. Example: Bhoothpur, Addakal, Moosapet</div>
                        @error('mandal_ids')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @error('mandal_ids.*')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.sub-admins.level2.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadMandals(districtId) {
    const container = document.getElementById('mandals-container');
    container.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading mandals...</div>';
    
    if (districtId) {
        fetch(`/api/mandals/${districtId}`)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    let html = '<div class="row g-2">';
                    data.forEach(mandal => {
                        html += `
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="mandal_ids[]" 
                                           value="${mandal.id}" id="mandal_${mandal.id}">
                                    <label class="form-check-label" for="mandal_${mandal.id}">
                                        ${mandal.name}
                                    </label>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<div class="text-muted text-center">No mandals found for this district</div>';
                }
            })
            .catch(error => {
                container.innerHTML = '<div class="text-danger text-center">Error loading mandals</div>';
            });
    } else {
        container.innerHTML = '<div class="text-muted text-center">Please select a district first</div>';
    }
}

// Load mandals if district is pre-selected
@if(old('district_id'))
    loadMandals({{ old('district_id') }});
    // Restore selected mandals
    @if(old('mandal_ids'))
        setTimeout(() => {
            @foreach(old('mandal_ids') as $mandalId)
                const checkbox = document.getElementById('mandal_{{ $mandalId }}');
                if (checkbox) checkbox.checked = true;
            @endforeach
        }, 500);
    @endif
@endif
</script>
@endsection

