@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Change Password</h1>
            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #e74c3c, #c0392b); width: 50px; height: 50px;">
                    <i class="fas fa-key"></i>
                </div>
                <h5 class="mb-0">Security Settings</h5>
            </div>
            
            <form method="POST" action="{{ route('profile.update-password') }}">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label class="form-label">Current Password <span class="text-danger">*</span></label>
                    <input type="password" name="current_password" required 
                           class="form-control @error('current_password') is-invalid @enderror" 
                           placeholder="Enter your current password">
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password" required 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Enter your new password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        Password must be at least 8 characters long.
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Confirm New Password <span class="text-danger">*</span></label>
                    <input type="password" name="password_confirmation" required 
                           class="form-control" 
                           placeholder="Confirm your new password">
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Password
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Security Tips -->
        <div class="stats-card">
            <h6 class="mb-3">
                <i class="fas fa-shield-alt me-2"></i>Password Security Tips
            </h6>
            
            <ul class="list-unstyled mb-0">
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    Use at least 8 characters
                </li>
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    Include uppercase and lowercase letters
                </li>
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    Add numbers and special characters
                </li>
                <li class="mb-2">
                    <i class="fas fa-check text-success me-2"></i>
                    Avoid common words or personal information
                </li>
                <li class="mb-0">
                    <i class="fas fa-check text-success me-2"></i>
                    Don't reuse passwords from other accounts
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
