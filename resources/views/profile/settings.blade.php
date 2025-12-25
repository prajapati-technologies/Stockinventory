@extends('layouts.app')

@section('title', 'Account Settings')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Account Settings</h1>
            <a href="{{ route('profile.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Profile
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 50px; height: 50px;">
                    <i class="fas fa-cog"></i>
                </div>
                <h5 class="mb-0">Preferences</h5>
            </div>
            
            <form method="POST" action="{{ route('admin.profile.update-settings') }}">
                @csrf
                @method('PUT')
                
                @php
                    $preferences = $user->preferences ? json_decode($user->preferences, true) : [];
                @endphp
                
                <div class="row g-3">
                    <div class="col-12">
                        <h6 class="mb-3">Notification Settings</h6>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="email_notifications" id="email_notifications" 
                                   {{ ($preferences['email_notifications'] ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="email_notifications">
                                <strong>Email Notifications</strong>
                                <div class="form-text">Receive notifications via email</div>
                            </label>
                        </div>
                        
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="sms_notifications" id="sms_notifications" 
                                   {{ ($preferences['sms_notifications'] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label" for="sms_notifications">
                                <strong>SMS Notifications</strong>
                                <div class="form-text">Receive notifications via SMS</div>
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Language</label>
                        <select name="language" class="form-select">
                            <option value="en" {{ ($preferences['language'] ?? 'en') == 'en' ? 'selected' : '' }}>English</option>
                            <option value="hi" {{ ($preferences['language'] ?? 'en') == 'hi' ? 'selected' : '' }}>Hindi</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label">Timezone</label>
                        <select name="timezone" class="form-select">
                            <option value="Asia/Kolkata" {{ ($preferences['timezone'] ?? 'Asia/Kolkata') == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                            <option value="UTC" {{ ($preferences['timezone'] ?? 'Asia/Kolkata') == 'UTC' ? 'selected' : '' }}>UTC</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('profile.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Account Information -->
        <div class="stats-card mb-4">
            <h6 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>Account Information
            </h6>
            
            <div class="mb-3">
                <small class="text-muted">User ID</small>
                <div class="fw-bold">{{ $user->id }}</div>
            </div>
            
            <div class="mb-3">
                <small class="text-muted">Role</small>
                <div class="fw-bold">{{ ucfirst($user->getRoleNames()->first()) }}</div>
            </div>
            
            <div class="mb-3">
                <small class="text-muted">Member Since</small>
                <div class="fw-bold">{{ $user->created_at->format('d M Y') }}</div>
            </div>
            
            <div class="mb-0">
                <small class="text-muted">Last Updated</small>
                <div class="fw-bold">{{ $user->updated_at->format('d M Y, h:i A') }}</div>
            </div>
        </div>
        
        <!-- Security Status -->
        <div class="stats-card">
            <h6 class="mb-3">
                <i class="fas fa-shield-alt me-2"></i>Security Status
            </h6>
            
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Password Status</span>
                    <span class="badge {{ $user->must_change_password ? 'bg-warning' : 'bg-success' }}">
                        {{ $user->must_change_password ? 'Change Required' : 'Secure' }}
                    </span>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span>Account Status</span>
                    <span class="badge bg-success">Active</span>
                </div>
            </div>
            
            <div class="d-grid">
                <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-key me-2"></i>Change Password
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
