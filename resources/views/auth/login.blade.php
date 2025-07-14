<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Laporan Kegiatan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
            --text-color: #5a5c69;
        }
        
        body {
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: url('https://www.kppmining.com/assets/images/kpp-home-banner.png') no-repeat center center fixed;
            background-size: cover;
            position: relative;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }
        
        .auth-container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.3);
            overflow: hidden;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.95);
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.4);
        }
        
        .auth-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .auth-body {
            padding: 2rem;
        }
        
        .form-control {
            height: calc(2.5em + 0.75rem + 2px);
            border-radius: 0.35rem;
            padding: 0.375rem 0.75rem;
            border: 1px solid #d1d3e2;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 2rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            transform: translateY(-1px);
        }
        
        .auth-logo {
            max-height: 5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s;
        }
        
        .auth-logo:hover {
            transform: scale(1.05);
        }
        
        .divider {
            position: relative;
            margin: 1.5rem 0;
        }
        
        .divider:before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(to right, transparent, #d1d3e2, transparent);
        }
        
        .divider-text {
            position: relative;
            padding: 0 1rem;
            background: white;
            display: inline-block;
            color: var(--text-color);
            font-size: 0.8rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-color);
        }
        
        .social-login .btn {
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            transition: all 0.3s;
        }
        
        .social-login .btn:hover {
            transform: translateY(-3px);
        }
        
        .floating-label {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .floating-label label {
            position: absolute;
            top: 0.6rem;
            left: 1rem;
            color: #6c757d;
            transition: all 0.2s;
            pointer-events: none;
            background: white;
            padding: 0 0.25rem;
        }
        
        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            top: -0.6rem;
            left: 0.8rem;
            font-size: 0.75rem;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .auth-card {
                margin: 1rem;
            }
            
            .auth-body {
                padding: 1.5rem;
            }

            body {
                background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://www.kppmining.com/assets/images/kpp-home-banner.png') no-repeat center center;
                background-size: cover;
            }
        }
        
        /* Animation classes */
        .animate-bounce {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {transform: translateY(0);}
            40% {transform: translateY(-15px);}
            60% {transform: translateY(-7px);}
        }
        
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0% {transform: translateY(0px);}
            50% {transform: translateY(-15px);}
            100% {transform: translateY(0px);}
        }

        /* Custom glass effect for the card */
        .glass-effect {
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.85);
            border-radius: 1rem;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                    <div class="auth-card card animate__animated animate__fadeIn glass-effect">
                        <div class="row no-gutters">
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="p-5 h-100 d-flex flex-column justify-content-center" style="background: linear-gradient(rgba(78, 115, 223, 0.8), rgba(78, 115, 223, 0.8));">
                                    <div class="text-white text-center">
                                        <img src="https://seen.asia/file/logocorporate/Logo_CR-U-AA-000446530861be54ae.png" 
                                             class="auth-logo animate-float" alt="Company Logo">
                                        <h2 class="mb-3">Welcome Back!</h2>
                                        <p class="mb-0">Login to access your dashboard and manage your activity reports.</p>
                                    </div>
                                    <div class="mt-auto text-center text-white-50 small">
                                        <p class="mb-0">Don't have an account?</p>
                                        <a href="{{ route('register') }}" class="text-white font-weight-bold">Sign Up</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="auth-body p-5">
                                    <div class="text-center mb-4">
                                        <img src="https://seen.asia/file/logocorporate/Logo_CR-U-AA-000446530861be54ae.png" 
                                             class="auth-logo d-lg-none" alt="Company Logo" style="max-height: 3rem;">
                                        <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                                    </div>
                                    
                                    <form method="POST" action="{{ route('login') }}" id="loginForm" class="needs-validation" novalidate>
                                        @csrf
                                        
                                        <!-- Username Input -->
                                        <div class="floating-label">
                                            <input type="text" id="username" name="username"
                                                class="form-control @error('username') is-invalid @enderror"
                                                placeholder=" "
                                                value="{{ old('username') }}"
                                                required autofocus />
                                            <label for="username">Username</label>
                                            <div class="invalid-feedback">
                                                Please enter your username.
                                            </div>
                                            @error('username')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Password Input -->
                                        <div class="floating-label position-relative">
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder=" "
                                                required />
                                            <label for="password">Password</label>
                                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                                            <div class="invalid-feedback">
                                                Please enter your password.
                                            </div>
                                            @error('password')
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                        
                                        <div class="form-group d-flex justify-content-between align-items-center mb-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="remember">Remember Me</label>
                                            </div>
                                            @if (Route::has('password.request'))
                                                <a href="{{ route('password.request') }}" class="small text-primary">Forgot Password?</a>
                                            @endif
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-block btn-icon-split">
                                            <span class="icon">
                                                <i class="fas fa-sign-in-alt"></i>
                                            </span>
                                            <span class="text" id="loginText">Login</span>
                                            <span id="loginSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        <div class="text-center small">
                                            Don't have an account? <a href="{{ route('register') }}" class="text-primary">Create an Account!</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @if(session()->has('alert_message'))
        <x-alert
            type="{{ session('alert_type') }}"
            message="{{ session('alert_message') }}"
            title="{{ session('alert_title') }}"
            timer="{{ session('alert_timer') }}"
            showConfirmButton="{{ session('alert_showConfirmButton') }}"
            position="{{ session('alert_position') }}"
            background="{{ session('alert_background') }}"
        />
    @endif
    
    <script>
        // Password toggle visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('password');
            const icon = this;
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
        
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                const forms = document.getElementsByClassName('needs-validation');
                Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // AJAX form submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');
            
            loginText.textContent = 'Logging in...';
            loginSpinner.classList.remove('d-none');
            
            // Add shake animation if form is invalid
            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                loginText.textContent = 'Login';
                loginSpinner.classList.add('d-none');
                
                // Add shake animation to invalid fields
                const invalidFields = this.querySelectorAll('.is-invalid');
                invalidFields.forEach(field => {
                    field.classList.add('animate__animated', 'animate__headShake');
                    field.addEventListener('animationend', () => {
                        field.classList.remove('animate__animated', 'animate__headShake');
                    });
                });
                
                return;
            }
            
            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success SweetAlert with confetti
                    Swal.fire({
                        title: 'Login Successful!',
                        text: data.message || 'Redirecting to your dashboard...',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => {
                            // Add confetti effect
                            const confettiSettings = { target: 'my-canvas', max: 100 };
                            const confetti = new ConfettiGenerator(confettiSettings);
                            confetti.render();
                            
                            setTimeout(() => {
                                confetti.clear();
                            }, 2000);
                        },
                        willClose: () => {
                            window.location.href = data.redirect;
                        }
                    });
                } else {
                    throw new Error(data.message || 'Login failed');
                }
            })
            .catch(error => {
                // Reset loading state
                loginText.textContent = 'Login';
                loginSpinner.classList.add('d-none');
                
                let errorMessage = 'Login failed. Please try again.';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).join('\n');
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                // Show error with animation
                Swal.fire({
                    title: 'Oops...',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Try Again',
                    showClass: {
                        popup: 'animate__animated animate__bounceIn'
                    },
                    hideClass: {
                        popup: 'animate__animated animate__fadeOut'
                    }
                }).then(() => {
                    // Focus on the first error field
                    const invalidField = document.querySelector('.is-invalid');
                    if (invalidField) {
                        invalidField.focus();
                    }
                });
            });
        });
        
        // Add animation to elements on hover
        document.querySelectorAll('.btn, .auth-logo, .social-login .btn').forEach(element => {
            element.addEventListener('mouseenter', () => {
                element.classList.add('animate__animated', 'animate__pulse');
            });
            element.addEventListener('mouseleave', () => {
                element.classList.remove('animate__animated', 'animate__pulse');
            });
            element.addEventListener('animationend', () => {
                element.classList.remove('animate__animated', 'animate__pulse');
            });
        });
    </script>
    
    <!-- Confetti library for success animation -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.4.0/dist/confetti.browser.min.js"></script>
    <canvas id="my-canvas" style="position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;pointer-events:none;"></canvas>
</body>
</html>