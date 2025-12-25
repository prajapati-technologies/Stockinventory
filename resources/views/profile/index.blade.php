@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">My Profile</h1>
            @if(auth()->user()->hasRole('admin'))
            <div class="d-flex gap-2">
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
                <a href="{{ route('admin.profile.settings') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-cog me-2"></i>Settings
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <!-- Profile Information -->
    <div class="col-lg-8 mb-4">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 60px; height: 60px;">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-0">{{ ucfirst($user->getRoleNames()->first()) }}</p>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 40px; height: 40px;">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Phone Number</h6>
                            <p class="mb-0 text-muted">{{ $user->phone_number }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 40px; height: 40px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Location</h6>
                            <p class="mb-0 text-muted">
                                @if($user->district && $user->mandal)
                                    {{ $user->mandal->name }}, {{ $user->district->name }}
                                @else
                                    Not specified
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #f39c12, #e67e22); width: 40px; height: 40px;">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Member Since</h6>
                            <p class="mb-0 text-muted">{{ $user->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3 bg-light rounded">
                        <div class="stats-icon me-3" style="background: linear-gradient(45deg, #e74c3c, #c0392b); width: 40px; height: 40px;">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">Account Status</h6>
                            <p class="mb-0">
                                <span class="badge {{ $user->must_change_password ? 'bg-warning' : 'bg-success' }}">
                                    {{ $user->must_change_password ? 'Password Change Required' : 'Active' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-lg-4 mb-4">
        <div class="stats-card">
            <h5 class="mb-4">
                <i class="fas fa-bolt me-2"></i>Quick Actions
            </h5>
            
            <div class="d-grid gap-2">
                @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.profile.edit') }}" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>Edit Profile
                </a>
                
                <a href="{{ route('admin.profile.settings') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-cog me-2"></i>Account Settings
                </a>
                @endif
                
                <a href="{{ route('profile.change-password') }}" class="btn btn-outline-warning">
                    <i class="fas fa-key me-2"></i>Change Password
                </a>
                
                @if($user->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-info">
                        <i class="fas fa-tachometer-alt me-2"></i>Admin Dashboard
                    </a>
                @elseif($user->hasRole('store_manager'))
                    <a href="{{ route('store.dashboard') }}" class="btn btn-outline-info">
                        <i class="fas fa-store me-2"></i>Store Dashboard
                    </a>
                @elseif($user->hasRole('supervisor'))
                    <a href="{{ route('supervisor.dashboard') }}" class="btn btn-outline-info">
                        <i class="fas fa-user-tie me-2"></i>Supervisor Dashboard
                    </a>
                @endif
            </div>
        </div>
        
        <!-- Security Notice -->
        @if($user->must_change_password)
        <div class="stats-card border-warning">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #f39c12, #e67e22); width: 50px; height: 50px;">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="mb-1 text-warning">Security Notice</h6>
                    <p class="mb-2 text-muted">Please change your password for security.</p>
                    <a href="{{ route('profile.change-password') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-key me-1"></i>Change Now
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Recent Activity (if applicable) -->
@if($user->hasRole('admin'))
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <h5 class="mb-4">
                <i class="fas fa-history me-2"></i>Recent Activity
            </h5>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                            <i class="fas fa-store"></i>
                        </div>
                        <h6 class="mb-1">Total Stores</h6>
                        <p class="mb-0 text-primary fw-bold">{{ \App\Models\Store::count() }}</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 50px; height: 50px;">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h6 class="mb-1">Supervisors</h6>
                        <p class="mb-0 text-primary fw-bold">{{ \App\Models\User::role('supervisor')->count() }}</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                            <i class="fas fa-users"></i>
                        </div>
                        <h6 class="mb-1">Customers</h6>
                        <p class="mb-0 text-primary fw-bold">{{ \App\Models\Customer::count() }}</p>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="text-center p-3 bg-light rounded">
                        <div class="stats-icon mx-auto mb-2" style="background: linear-gradient(45deg, #f39c12, #e67e22); width: 50px; height: 50px;">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h6 class="mb-1">Total Sales</h6>
                        <p class="mb-0 text-primary fw-bold">{{ \App\Models\Sale::count() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
