@extends('layouts.app')

@section('title', 'Change Password')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="stats-card">
            <div class="text-center mb-4">
                <div class="stats-icon mx-auto mb-3" style="background: linear-gradient(45deg, #e74c3c, #c0392b); width: 60px; height: 60px;">
                    <i class="fas fa-key"></i>
                </div>
                <h2 class="h4 mb-0">Change Password</h2>
                <p class="text-muted">You must change your password before continuing</p>
            </div>
            
            <div class="alert alert-warning d-flex align-items-center mb-4">
                <i class="fas fa-exclamation-triangle me-3"></i>
                <div>
                    <strong>Required:</strong> You must change your password before continuing.
                </div>
            </div>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="current_password" class="form-label">
                        <i class="fas fa-lock me-2"></i>Current Password
                    </label>
                    <input 
                        type="password" 
                        name="current_password" 
                        id="current_password" 
                        class="form-control form-control-lg @error('current_password') is-invalid @enderror"
                        placeholder="Enter current password"
                        required
                    >
                    @error('current_password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">
                        <i class="fas fa-key me-2"></i>New Password
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                        placeholder="Enter new password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>Minimum 6 characters required
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        <i class="fas fa-check-circle me-2"></i>Confirm New Password
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        class="form-control form-control-lg"
                        placeholder="Confirm new password"
                        required
                    >
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


