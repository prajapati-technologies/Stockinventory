@extends('layouts.app')

@section('title', 'Sub Admin Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Dashboard</h1>
            <div class="d-flex">
                <span class="badge bg-primary me-2">
                    @if(auth()->user()->hasRole('sub_admin_level_1'))
                        Sub Admin Level-1
                    @else
                        Sub Admin Level-2
                    @endif
                </span>
                <small class="text-muted">Last updated: {{ now()->format('d M Y, h:i A') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Sub-Admin Information Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card" style="background: linear-gradient(135deg, #16a085 0%, #138d75 100%); color: white;">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 60px; height: 60px;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h2 class="mb-1">{{ $user->name }}</h2>
                    <p class="mb-0 opacity-75">
                        @if(auth()->user()->hasRole('sub_admin_level_1'))
                            Sub Admin Level-1 Dashboard
                        @else
                            Sub Admin Level-2 Dashboard
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">District</h6>
                            <p class="mb-0 fw-bold">{{ $user->district->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
                
                @if(auth()->user()->hasRole('sub_admin_level_2'))
                <div class="col-md-6">
                    <div class="d-flex align-items-center p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="stats-icon me-3" style="background: rgba(255,255,255,0.2); width: 40px; height: 40px;">
                            <i class="fas fa-map-signs"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 opacity-75">Mandals</h6>
                            <p class="mb-0 fw-bold">
                                @if($user->mandal_ids && count($user->mandal_ids) > 0)
                                    @php
                                        $mandals = \App\Models\Mandal::whereIn('id', $user->mandal_ids)->get();
                                    @endphp
                                    {{ $mandals->pluck('name')->implode(', ') }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-4 mb-md-0">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 50px; height: 50px;">
                    <i class="fas fa-store"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_stores'] }}</h3>
                    <small class="text-muted">Total Stores</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4 mb-md-0">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #2ecc71, #27ae60); width: 50px; height: 50px;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['active_stores'] }}</h3>
                    <small class="text-muted">Active Stores</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4 mb-md-0">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #9b59b6, #8e44ad); width: 50px; height: 50px;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_customers'] }}</h3>
                    <small class="text-muted">Total Customers</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-4 mb-md-0">
        <div class="stats-card">
            <div class="d-flex align-items-center">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #f39c12, #e67e22); width: 50px; height: 50px;">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div>
                    <h3 class="mb-0">{{ $stats['total_sales'] }}</h3>
                    <small class="text-muted">Total Sales (Bags)</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stores List -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">
                    <i class="fas fa-store me-2"></i>Stores
                </h5>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Store Name</th>
                            <th>Phone</th>
                            <th>Mandal</th>
                            <th>Status</th>
                            <th>Valid Till</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stores as $store)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="stats-icon me-2" style="background: linear-gradient(45deg, #3498db, #2980b9); width: 30px; height: 30px;">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <span class="fw-bold">{{ $store->name }}</span>
                                </div>
                            </td>
                            <td>{{ $store->phone_number }}</td>
                            <td>
                                <span class="badge bg-info">{{ $store->mandal->name ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <span class="badge {{ $store->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $store->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $store->valid_till > now() ? 'bg-success' : 'bg-danger' }}">
                                    {{ $store->valid_till->format('d M Y') }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No stores found</h5>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

