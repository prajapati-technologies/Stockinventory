<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Stock Management System')</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-dark: #2c3e50;
            --secondary-dark: #34495e;
            --accent-orange: #e74c3c;
            --accent-blue: #3498db;
            --accent-green: #2ecc71;
            --accent-purple: #9b59b6;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .main-content {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .footer {
            margin-top: auto;
            background-color: #ffffff;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
        }
        
        .footer p {
            font-size: 0.9rem;
        }
        
        .footer .fa-heart {
            animation: heartbeat 1.5s ease-in-out infinite;
        }
        
        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .footer .social-links a {
            transition: all 0.3s ease;
        }
        
        .footer .social-links a:hover {
            color: var(--accent-blue) !important;
            transform: translateY(-3px);
        }
        
        .footer a:hover {
            color: var(--accent-blue) !important;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--secondary-dark) 100%);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .sidebar-profile {
            flex-shrink: 0;
            padding: 1.5rem;
        }
        
        .sidebar-nav-container {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            min-height: 0;
            padding: 0 1.5rem 1.5rem 1.5rem;
        }
        
        .sidebar-nav-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar-nav-container::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
        }
        
        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .sidebar-nav-container::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        .sidebar.collapsed {
            width: 80px;
        }
        
        .sidebar.collapsed .nav-link span {
            display: none;
        }
        
        .sidebar.collapsed .nav-icon {
            margin-right: 0;
            justify-content: center;
        }
        
        .sidebar.collapsed .profile-avatar {
            width: 50px;
            height: 50px;
            font-size: 1.2rem;
        }
        
        .sidebar.collapsed h5,
        .sidebar.collapsed small {
            display: none;
        }
        
        .sidebar.collapsed .nav-link {
            justify-content: center;
            padding: 12px 10px;
        }
        
        .sidebar.collapsed .nav-link:hover {
            transform: none;
        }
        
        .main-content {
            margin-left: 280px;
            transition: all 0.3s ease;
            padding-top: 80px;
        }
        
        .main-content.expanded {
            margin-left: 80px;
        }
        
        .top-header {
            background: var(--primary-dark);
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: fixed;
            top: 0;
            left: 280px;
            right: 0;
            width: calc(100% - 280px);
            z-index: 999;
            transition: all 0.3s ease;
        }
        
        .main-content.expanded .top-header {
            left: 80px;
            width: calc(100% - 80px);
        }
        
        .user-profile {
            background: var(--accent-orange);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }
        
        .nav-item {
            margin: 0.5rem 0;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .nav-item:hover {
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .nav-item.active {
            background-color: var(--accent-orange);
            border-left: 4px solid var(--accent-orange);
        }
        
        .nav-link {
            color: #ecf0f1;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            border-radius: 8px;
        }
        
        .nav-link:hover {
            color: white;
        }
        
        .nav-icon {
            width: 20px;
            margin-right: 12px;
            text-align: center;
        }
        
        .sub-menu {
            margin-left: 0.5rem;
            margin-top: 0.25rem;
        }
        
        .sub-nav-link {
            padding: 8px 20px;
            font-size: 0.9rem;
            color: rgba(236, 240, 241, 0.8);
        }
        
        .sub-nav-link:hover {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        
        .nav-item:hover .sub-menu {
            display: block;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            border: none;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
            margin-bottom: 1rem;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .social-card {
            color: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .social-card:hover {
            transform: translateY(-5px);
        }
        
        .social-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
            margin-bottom: 1rem;
        }
        
        .notification-badge {
            background: var(--accent-orange);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            position: absolute;
            top: -5px;
            right: -5px;
        }
        
        .profile-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(45deg, var(--accent-blue), var(--accent-purple));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: bold;
            margin: 0 auto 1rem;
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px;
                z-index: 1050;
                box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0 !important;
                width: 100%;
                padding-top: 70px;
            }
            
            .top-header {
                padding: 1rem;
                z-index: 1040;
                left: 0 !important;
                width: 100% !important;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .container-fluid {
                padding-left: 1rem;
                padding-right: 1rem;
            }
            
            /* Overlay for mobile sidebar */
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1045;
                display: none;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 100%;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .stats-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
            
            .top-header h4 {
                font-size: 1.1rem;
            }
            
            .top-header .btn {
                padding: 0.5rem;
            }
            
            /* Mobile button improvements */
            .btn-sm {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
            
            .flex-fill {
                flex: 1 1 auto;
            }
            
            .flex-md-fill-0 {
                flex: 0 0 auto;
            }
            
            @media (min-width: 768px) {
                .flex-md-fill-0 {
                    flex: 0 0 auto !important;
                }
            }
        }
        
        /* Ensure sidebar is visible when shown */
        .sidebar.show {
            transform: translateX(0) !important;
            visibility: visible !important;
        }
        
        /* Hide sidebar by default on mobile */
        @media (max-width: 768px) {
            .sidebar:not(.show) {
                visibility: hidden;
            }
        }
    </style>
</head>
<body>
    @auth
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Close Button for Mobile -->
        <div class="d-block d-md-none p-3 border-bottom border-secondary flex-shrink-0">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-white">
                    <strong>Menu</strong>
                </div>
                <button class="btn btn-link text-white p-0" id="sidebarClose" type="button">
                    <i class="fas fa-times fs-4"></i>
                </button>
            </div>
        </div>
        
        <!-- User Profile -->
        <div class="sidebar-profile">
            <div class="text-center">
                <div class="profile-avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <h5 class="text-white mb-1">{{ auth()->user()->name }}</h5>
                <small class="text-light">
                    <i class="fas fa-circle text-success"></i> Online
                </small>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <div class="sidebar-nav-container">
            <nav class="mt-4">
                @role('admin')
                    <div class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('admin.dashboard') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                            <i class="fas fa-tachometer-alt nav-icon" style="color: #e74c3c;"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.stores.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.stores.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Stores">
                            <i class="fas fa-store nav-icon" style="color: #3498db;"></i>
                            <span>Stores</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.supervisors.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.supervisors.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Supervisors">
                            <i class="fas fa-user-tie nav-icon" style="color: #9b59b6;"></i>
                            <span>Supervisors</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.sub-admins.level1.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sub-admins.level1.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Sub Admin Level-1">
                            <i class="fas fa-user-shield nav-icon" style="color: #16a085;"></i>
                            <span>Sub Admin Level-1</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.sub-admins.level2.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.sub-admins.level2.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Sub Admin Level-2">
                            <i class="fas fa-user-shield nav-icon" style="color: #27ae60;"></i>
                            <span>Sub Admin Level-2</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.stock-allotment-conditions.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.stock-allotment-conditions.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Add Condition">
                            <i class="fas fa-cog nav-icon" style="color: #e67e22;"></i>
                            <span>Add Condition</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.stores.export') ? 'active' : '' }}">
                        <a href="{{ route('admin.stores.export') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Export Stores">
                            <i class="fas fa-file-excel nav-icon" style="color: #f39c12;"></i>
                            <span>Export Stores</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.supervisors.export') ? 'active' : '' }}">
                        <a href="{{ route('admin.supervisors.export') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Export Supervisors">
                            <i class="fas fa-file-excel nav-icon" style="color: #f39c12;"></i>
                            <span>Export Supervisors</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.districts.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.districts.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Districts & Mandals">
                            <i class="fas fa-map-marker-alt nav-icon" style="color: #2ecc71;"></i>
                            <span>Districts & Mandals</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.purchase-history.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.purchase-history.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="View Purchase History">
                            <i class="fas fa-history nav-icon" style="color: #f39c12;"></i>
                            <span>Purchase History</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                        <a href="{{ route('admin.customers.search') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Customer Search">
                            <i class="fas fa-search nav-icon" style="color: #e67e22;"></i>
                            <span>Customer Search</span>
                        </a>
                    </div>
                @endrole

                @role('store_manager')
                    <div class="nav-item {{ request()->routeIs('store.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('store.dashboard') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                            <i class="fas fa-tachometer-alt nav-icon" style="color: #e74c3c;"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('store.customer.*') ? 'active' : '' }}">
                        <a href="{{ route('store.customer.search') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Customer">
                            <i class="fas fa-users nav-icon" style="color: #3498db;"></i>
                            <span>Customer</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('store.sale.*') ? 'active' : '' }}">
                        <a href="{{ route('store.sale.history') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Sales">
                            <i class="fas fa-shopping-cart nav-icon" style="color: #2ecc71;"></i>
                            <span>Sales</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('store.purchase-history.*') ? 'active' : '' }}">
                        <a href="{{ route('store.purchase-history.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Purchase History">
                            <i class="fas fa-history nav-icon" style="color: #f39c12;"></i>
                            <span>Purchase History</span>
                        </a>
                    </div>
{{--                     <div class="nav-item {{ request()->routeIs('store.districts.export') ? 'active' : '' }}">
                        <a href="{{ route('store.districts.export') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Export Districts">
                            <i class="fas fa-file-excel nav-icon" style="color: #f39c12;"></i>
                            <span>Export Districts</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('store.mandals.export') ? 'active' : '' }}">
                        <a href="{{ route('store.mandals.export') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Export Mandals">
                            <i class="fas fa-file-excel nav-icon" style="color: #f39c12;"></i>
                            <span>Export Mandals</span>
                        </a>
                    </div> --}}
                    <div class="nav-item {{ request()->routeIs('store.contact.*') ? 'active' : '' }}">
                        <a href="{{ route('store.contact.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Contact Us">
                            <i class="fas fa-envelope nav-icon" style="color: #e74c3c;"></i>
                            <span>Contact Us</span>
                        </a>
                    </div>
                @endrole

                @role('supervisor')
                    <div class="nav-item {{ request()->routeIs('supervisor.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.dashboard') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                            <i class="fas fa-tachometer-alt nav-icon" style="color: #e74c3c;"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('supervisor.reports.*') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.reports.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">
                            <i class="fas fa-chart-bar nav-icon" style="color: #2ecc71;"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('supervisor.customer.search') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.customer.search') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Search Customer">
                            <i class="fas fa-search nav-icon" style="color: #f39c12;"></i>
                            <span>Search Customer</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('supervisor.customers.*') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.customers.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Customer Edit">
                            <i class="fas fa-users nav-icon" style="color: #3498db;"></i>
                            <span>Customer Edit</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('supervisor.purchase-history.*') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.purchase-history.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Purchase History">
                            <i class="fas fa-history nav-icon" style="color: #f39c12;"></i>
                            <span>Purchase History</span>
                        </a>
                    </div>
{{--                     <div class="nav-item {{ request()->routeIs('supervisor.districts.export') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.districts.export') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Export Districts">
                            <i class="fas fa-file-excel nav-icon" style="color: #f39c12;"></i>
                            <span>Export Districts</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('supervisor.mandals.export') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.mandals.export') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Export Mandals">
                            <i class="fas fa-file-excel nav-icon" style="color: #f39c12;"></i>
                            <span>Export Mandals</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('supervisor.customers.template.download') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.customers.template.download') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Download Customer Template">
                            <i class="fas fa-file-download nav-icon" style="color: #9b59b6;"></i>
                            <span>Customer Template</span>
                        </a>
                    </div> --}}
                    <div class="nav-item {{ request()->routeIs('supervisor.contact.*') ? 'active' : '' }}">
                        <a href="{{ route('supervisor.contact.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Contact Us">
                            <i class="fas fa-envelope nav-icon" style="color: #e74c3c;"></i>
                            <span>Contact Us</span>
                        </a>
                    </div>
                @endrole

                @hasanyrole('sub_admin_level_1|sub_admin_level_2')
                    <div class="nav-item {{ request()->routeIs('sub-admin.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('sub-admin.dashboard') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Dashboard">
                            <i class="fas fa-tachometer-alt nav-icon" style="color: #e74c3c;"></i>
                            <span>Dashboard</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('sub-admin.purchase-history.*') ? 'active' : '' }}">
                        <a href="{{ route('sub-admin.purchase-history.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Purchase History">
                            <i class="fas fa-history nav-icon" style="color: #f39c12;"></i>
                            <span>Purchase History</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('sub-admin.reports.*') ? 'active' : '' }}">
                        <a href="{{ route('sub-admin.reports.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Reports">
                            <i class="fas fa-chart-bar nav-icon" style="color: #2ecc71;"></i>
                            <span>Reports</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('sub-admin.customers.search') ? 'active' : '' }}">
                        <a href="{{ route('sub-admin.customers.search') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Customer Search">
                            <i class="fas fa-search nav-icon" style="color: #f39c12;"></i>
                            <span>Customer Search</span>
                        </a>
                    </div>
                    <div class="nav-item {{ request()->routeIs('sub-admin.contact.*') ? 'active' : '' }}">
                        <a href="{{ route('sub-admin.contact.index') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Contact Us">
                            <i class="fas fa-envelope nav-icon" style="color: #e74c3c;"></i>
                            <span>Contact Us</span>
                        </a>
                    </div>
                    @role('sub_admin_level_1')
                    <div class="nav-item {{ request()->routeIs('sub-admin.customers.upload-form') ? 'active' : '' }}">
                        <a href="{{ route('sub-admin.customers.upload-form') }}" class="nav-link" data-bs-toggle="tooltip" data-bs-placement="right" title="Upload Customers">
                            <i class="fas fa-upload nav-icon" style="color: #3498db;"></i>
                            <span>Upload Customers</span>
                        </a>
                    </div>
                    @endrole
                @endhasanyrole
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="main-content">
        <!-- Top Header -->
        <div class="top-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-link text-white me-3" id="sidebarToggle" type="button">
                    <i class="fas fa-bars fs-5"></i>
                </button>
                <h4 class="text-white mb-0">
                    <i class="fas fa-store me-2"></i>Stock Management
                </h4>
            </div>
            
            <div class="d-flex align-items-center">
                <!-- Notifications -->
                {{-- <div class="position-relative me-3">
                    <button class="btn btn-link text-white">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">2</span>
                    </button>
                </div>
                
                <!-- Messages -->
                <div class="position-relative me-3">
                    <button class="btn btn-link text-white">
                        <i class="fas fa-envelope"></i>
                        <span class="notification-badge">3</span>
                    </button>
                </div> --}}
                
                <!-- User Profile Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-link text-white dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                        <div class="user-profile me-2">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        {{ auth()->user()->name }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="{{ route('profile.index') }}"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        @if(auth()->user()->hasRole('admin'))
                        <li><a class="dropdown-item" href="{{ route('admin.profile.edit') }}"><i class="fas fa-edit me-2"></i>Edit Profile</a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.profile.settings') }}"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        @endif
                        @if(auth()->user()->must_change_password)
                        <li><a class="dropdown-item text-warning" href="{{ route('profile.change-password') }}"><i class="fas fa-key me-2"></i>Change Password</a></li>
                        @endif
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    @endauth

        <!-- Flash Messages -->
        @if(session('success'))
        <div class="container-fluid px-4 py-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="container-fluid px-4 py-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <div class="container-fluid px-4 py-4">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="footer mt-auto py-4 bg-white border-top">
            <div class="container-fluid px-4">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <h6 class="fw-bold text-primary">
                            <i class="fas fa-store me-2"></i>Stock Management System
                        </h6>
                        <p class="mb-0 text-muted small">
                            Efficient inventory and sales tracking solution
                        </p>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0 text-center">
                        <p class="mb-1 text-muted small">
                            <i class="fas fa-phone me-2"></i>Support: +91-9440713214 / +91-7801067691
                        </p>
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-envelope me-2"></i>Contact@qapsoftware.com
                        </p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="social-links mb-2">
                            <a href="#" class="text-muted me-3" title="Facebook"><i class="fab fa-facebook fs-5"></i></a>
                            <a href="#" class="text-muted me-3" title="Twitter"><i class="fab fa-twitter fs-5"></i></a>
                            <a href="#" class="text-muted me-3" title="Instagram"><i class="fab fa-instagram fs-5"></i></a>
                            <a href="#" class="text-muted" title="LinkedIn"><i class="fab fa-linkedin fs-5"></i></a>
                        </div>
                        <p class="mb-0 text-muted small">
                            Version 1.0.0
                        </p>
                    </div>
                </div>
                <hr class="my-3">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-0 text-muted small">
                            <i class="fas fa-copyright me-1"></i>
                            {{ date('Y') }} <strong>Stock Management System</strong>. All rights reserved.
                        </p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        {{-- <p class="mb-0 text-muted small">
                            Developed with <i class="fas fa-heart text-danger"></i> | 
                            <a href="#" class="text-muted text-decoration-none">Privacy Policy</a> | 
                            <a href="#" class="text-muted text-decoration-none">Terms of Service</a>
                        </p> --}}
                    </div>
                </div>
            </div>
        </footer>
    </div>
    {{-- @endauth --}}

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Sidebar toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const sidebarClose = document.getElementById('sidebarClose');
            
            // Function to close sidebar
            function closeSidebar() {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            }
            
            // Function to open sidebar
            function openSidebar() {
                sidebar.classList.add('show');
                sidebarOverlay.classList.add('show');
            }
            
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // For mobile devices
                    if (window.innerWidth <= 768) {
                        if (sidebar.classList.contains('show')) {
                            closeSidebar();
                        } else {
                            openSidebar();
                        }
                    } else {
                        // For desktop devices - toggle collapse
                        const isCollapsed = sidebar.classList.contains('collapsed');
                        
                        if (isCollapsed) {
                            // Expand sidebar
                            sidebar.classList.remove('collapsed');
                            mainContent.classList.remove('expanded');
                        } else {
                            // Collapse sidebar
                            sidebar.classList.add('collapsed');
                            mainContent.classList.add('expanded');
                        }
                    }
                });
            }
            
            // Close button functionality
            if (sidebarClose) {
                sidebarClose.addEventListener('click', function(e) {
                    e.preventDefault();
                    closeSidebar();
                });
            }
            
            // Close sidebar when clicking overlay
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    closeSidebar();
                });
            }
            
            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth <= 768 && 
                    !sidebar.contains(event.target) && 
                    !sidebarToggle.contains(event.target) && 
                    !sidebarClose.contains(event.target) &&
                    sidebar.classList.contains('show')) {
                    closeSidebar();
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
</body>
</html>


