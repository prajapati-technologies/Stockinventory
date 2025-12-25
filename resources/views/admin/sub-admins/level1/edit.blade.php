@extends('layouts.app')

@section('title', 'Edit Sub Admin Level-1')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Edit Sub Admin Level-1</h1>
            <a href="{{ route('admin.sub-admins.level1.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Sub Admins
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #16a085, #138d75); width: 50px; height: 50px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h5 class="mb-0">Sub Admin Level-1 Information</h5>
            </div>
            
            <form method="POST" action="{{ route('admin.sub-admins.level1.update', $subAdmin) }}">
                @csrf
                @method('PUT')
                
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $subAdmin->name) }}" required 
                               class="form-control @error('name') is-invalid @enderror" 
                               placeholder="Enter name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Mobile Number <span class="text-danger">*</span></label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $subAdmin->phone_number) }}" required 
                               class="form-control @error('phone_number') is-invalid @enderror" 
                               placeholder="Enter mobile number">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select name="district_id" required 
                                class="form-select @error('district_id') is-invalid @enderror">
                            <option value="">Select District</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ old('district_id', $subAdmin->district_id) == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Example: Mahabubnagar</div>
                        @error('district_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.sub-admins.level1.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


