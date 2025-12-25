@extends('layouts.app')

@section('title', 'Subscription Expired')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg p-8 text-center">
        <svg class="mx-auto h-24 w-24 text-red-500 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Subscription Expired</h1>
        <p class="text-gray-600 mb-4">Your store subscription has expired on {{ $store->valid_till->format('d M Y') }}.</p>
        <p class="text-gray-600 mb-8">Please contact the administrator to extend your validity period.</p>
        
        <div class="bg-gray-50 rounded-lg p-6">
            <div class="text-left space-y-2">
                <p class="text-sm"><span class="font-semibold">Store:</span> {{ $store->name }}</p>
                <p class="text-sm"><span class="font-semibold">Location:</span> {{ $store->district->name }}, {{ $store->mandal->name }}</p>
                <p class="text-sm"><span class="font-semibold">Expired On:</span> {{ $store->valid_till->format('d M Y') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
