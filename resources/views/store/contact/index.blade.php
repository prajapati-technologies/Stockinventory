@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Contact Us</h1>
            <a href="{{ route('store.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="stats-card">
            <div class="d-flex align-items-center mb-4">
                <div class="stats-icon me-3" style="background: linear-gradient(45deg, #e74c3c, #c0392b); width: 50px; height: 50px;">
                    <i class="fas fa-envelope"></i>
                </div>
                <h5 class="mb-0">Get in Touch</h5>
            </div>
            
            <p class="text-muted mb-4">Have a question or facing an issue? Fill out the form below and we'll get back to you as soon as possible.</p>

            <form method="POST" action="{{ route('store.contact.store') }}">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">
                        Name <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', auth()->user()->name) }}"
                        required
                        placeholder="Enter your name"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="phone_number" class="form-label">
                        Phone Number <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control @error('phone_number') is-invalid @enderror" 
                        id="phone_number" 
                        name="phone_number" 
                        value="{{ old('phone_number', auth()->user()->phone_number) }}"
                        required
                        placeholder="Enter your phone number"
                    >
                    @error('phone_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="issue" class="form-label">
                        Issue / Message <span class="text-danger">*</span>
                    </label>
                    <textarea 
                        class="form-control @error('issue') is-invalid @enderror" 
                        id="issue" 
                        name="issue" 
                        rows="6"
                        required
                        placeholder="Describe your issue or question in detail..."
                    >{{ old('issue') }}</textarea>
                    @error('issue')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Maximum 5000 characters</div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-redo me-2"></i>Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Send Message
                    </button>
                </div>
            </form>
        </div>

        <div class="stats-card mt-4">
            <h6 class="mb-3">
                <i class="fas fa-info-circle me-2"></i>Need Immediate Help?
            </h6>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-2">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <strong>Phone:</strong> +91-9440713214 / +91-7801067691
                    </p>
                </div>
                <div class="col-md-6">
                    <p class="mb-2">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <strong>Email:</strong> Contact@qapsoftware.com
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
