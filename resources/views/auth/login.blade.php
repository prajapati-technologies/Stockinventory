<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Stock Management System</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .logo-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 2rem;
            text-align: center;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }
        
        .form-control {
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            background: #f8f9fa;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background: white;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
        }
        
        /* Disclaimer Modal Styling */
        .disclaimer-modal .modal-content {
            background: #fff9c4;
            border: 2px solid #f0e68c;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .disclaimer-modal .modal-header {
            background: #fffacd;
            border-bottom: 2px solid #f0e68c;
            border-radius: 13px 13px 0 0;
            padding: 15px 20px;
        }
        
        .disclaimer-modal .modal-title {
            color: #8b6914;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .disclaimer-modal .modal-body {
            padding: 25px;
            color: #333;
            font-size: 1rem;
            line-height: 1.6;
        }
        
        .disclaimer-modal .modal-footer {
            border-top: 2px solid #f0e68c;
            background: #fffacd;
            border-radius: 0 0 13px 13px;
            padding: 15px 20px;
        }
        
        .disclaimer-modal .btn-ok {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .disclaimer-modal .btn-ok:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .login-disabled {
            pointer-events: none;
            opacity: 0.6;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="login-card login-disabled" id="loginCard">
                    <!-- Logo Section -->
                    <div class="logo-section">
                        <div class="logo-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <h1 class="h3 mb-1">Stock Management</h1>
                        <p class="mb-0 opacity-75">Sign in to your account</p>
                    </div>
                    
                    <!-- Form Section -->
                    <div class="p-4">
                        @if(session('error'))
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                        </div>
                        @endif

                        @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                        </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">
                                    <i class="fas fa-phone me-2"></i>Phone Number
                                </label>
                                <input 
                                    type="text" 
                                    name="phone_number" 
                                    id="phone_number" 
                                    value="{{ old('phone_number') }}"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    placeholder="Enter your phone number"
                                    required
                                    autofocus
                                >
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock me-2"></i>Password
                                </label>
                                <input 
                                    type="password" 
                                    name="password" 
                                    id="password" 
                                    class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Enter your password"
                                    required
                                >
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-check mb-4">
                                <input 
                                    type="checkbox" 
                                    name="remember" 
                                    id="remember" 
                                    class="form-check-input"
                                >
                                <label for="remember" class="form-check-label">
                                    Remember me
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-login w-100">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <small class="text-muted">
                                Default password for new accounts: <strong>guest</strong>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <p class="text-white-50">
                        © {{ date('Y') }} Stock Management System. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Disclaimer Modal -->
    <div class="modal fade disclaimer-modal" id="disclaimerModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Disclaimer
                    </h5>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>We are using this web application voluntarily and there is no compulsion from QA PROGRAMMER.</strong>
                    </p>
                    {{-- <p class="mb-0">
                        <strong>QA PROGRAMMER is not responsible for any consequences that may arise from this.</strong>
                    </p> --}}
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-ok" id="disclaimerOkBtn">
                        <i class="fas fa-check me-2"></i>OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Disclaimer Modal Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let okButtonClicked = false;
            const modalElement = document.getElementById('disclaimerModal');
            const disclaimerModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
            
            // Check if disclaimer has been shown before using localStorage
            const disclaimerShown = localStorage.getItem('disclaimerShown');
            
            if (!disclaimerShown) {
                // Show the disclaimer modal only if not shown before
                disclaimerModal.show();
            } else {
                // If already shown, enable login form immediately
                const loginCard = document.getElementById('loginCard');
                loginCard.classList.remove('login-disabled');
                document.getElementById('phone_number').focus();
            }
            
            // Prevent closing modal by clicking outside, pressing ESC, or any other method
            // Only allow closing when OK button is clicked
            modalElement.addEventListener('hide.bs.modal', function(e) {
                if (!okButtonClicked) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
            
            // Handle OK button click
            document.getElementById('disclaimerOkBtn').addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Set flag to allow modal closing
                okButtonClicked = true;
                
                // Save to localStorage that disclaimer has been shown
                localStorage.setItem('disclaimerShown', 'true');
                
                // Hide the modal
                disclaimerModal.hide();
                
                // Enable the login form
                const loginCard = document.getElementById('loginCard');
                loginCard.classList.remove('login-disabled');
                
                // Focus on phone number input
                setTimeout(function() {
                    document.getElementById('phone_number').focus();
                }, 300);
            });
        });
    </script>
</body>
</html>


