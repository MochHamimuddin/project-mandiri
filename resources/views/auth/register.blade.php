<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Laporan Kegiatan</title>
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
            background: linear-gradient(135deg, var(--secondary-color) 0%, #d2d6de 100%);
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
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
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2);
        }
        
        .auth-header {
            background: var(--primary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .auth-body {
            padding: 2rem;
            background: white;
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
        
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        
        .password-strength-weak {
            background-color: #dc3545;
            width: 25%;
        }
        
        .password-strength-medium {
            background-color: #ffc107;
            width: 50%;
        }
        
        .password-strength-strong {
            background-color: #28a745;
            width: 75%;
        }
        
        .password-strength-very-strong {
            background-color: #28a745;
            width: 100%;
        }
        
        .password-requirements {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }
        
        .requirement {
            display: flex;
            align-items: center;
            margin-bottom: 0.25rem;
        }
        
        .requirement i {
            margin-right: 0.5rem;
            font-size: 0.7rem;
        }
        
        .requirement.valid {
            color: #28a745;
        }
        
        .requirement.invalid {
            color: #dc3545;
        }
        
        @media (max-width: 768px) {
            .auth-card {
                margin: 1rem;
            }
            
            .auth-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                    <div class="auth-card card animate__animated animate__fadeIn">
                        <div class="row no-gutters">
                            <div class="col-lg-6 d-none d-lg-block">
                                <div class="p-5 h-100 d-flex flex-column justify-content-center" style="background: linear-gradient(rgba(78, 115, 223, 0.8), rgba(78, 115, 223, 0.8)), url('https://source.unsplash.com/random/600x800?office') center/cover;">
                                    <div class="text-white text-center">
                                        <img src="https://seen.asia/file/logocorporate/Logo_CR-U-AA-000446530861be54ae.png" 
                                             class="auth-logo" alt="Company Logo">
                                        <h2 class="mb-3">Join Us Today!</h2>
                                        <p class="mb-0">Create an account to start managing your activity reports.</p>
                                    </div>
                                    <div class="mt-auto text-center text-white-50 small">
                                        <p class="mb-0">Already have an account?</p>
                                        <a href="{{ route('login') }}" class="text-white font-weight-bold">Sign In</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="auth-body p-5">
                                    <div class="text-center mb-4">
                                        <img src="https://seen.asia/file/logocorporate/Logo_CR-U-AA-000446530861be54ae.png" 
                                             class="auth-logo d-lg-none" alt="Company Logo" style="max-height: 3rem;">
                                        <h1 class="h4 text-gray-900 mb-4">Create Your Account</h1>
                                    </div>
                                    
                                    <form method="POST" action="{{ route('register') }}" id="registerForm" class="needs-validation" novalidate>
                                        @csrf
                                        
                                        @if($errors->any())
                                        <div class="alert alert-danger animate__animated animate__shakeX">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        @endif
                                        
                                        <!-- Full Name -->
                                        <div class="floating-label">
                                            <input type="text" id="nama_lengkap" name="nama_lengkap"
                                                class="form-control @error('nama_lengkap') is-invalid @enderror"
                                                placeholder=" "
                                                value="{{ old('nama_lengkap') }}"
                                                required />
                                            <label for="nama_lengkap">Full Name</label>
                                            <div class="invalid-feedback">
                                                Please enter your full name.
                                            </div>
                                        </div>
                                        
                                        <!-- Username -->
                                        <div class="floating-label">
                                            <input type="text" id="username" name="username"
                                                class="form-control @error('username') is-invalid @enderror"
                                                placeholder=" "
                                                value="{{ old('username') }}"
                                                required />
                                            <label for="username">Username</label>
                                            <div class="invalid-feedback">
                                                Please choose a username.
                                            </div>
                                        </div>
                                        
                                        <!-- Email -->
                                        <div class="floating-label">
                                            <input type="email" id="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                placeholder=" "
                                                value="{{ old('email') }}"
                                                required />
                                            <label for="email">Email Address</label>
                                            <div class="invalid-feedback">
                                                Please enter a valid email address.
                                            </div>
                                        </div>
                                        
                                        <!-- Phone Number -->
                                        <div class="floating-label">
                                            <input type="tel" id="no_telp" name="no_telp"
                                                class="form-control @error('no_telp') is-invalid @enderror"
                                                placeholder=" "
                                                value="{{ old('no_telp') }}"
                                                pattern="^62\d{8,13}$"
                                                title="Phone number must start with 62 and contain 10-15 digits"
                                                required />
                                            <label for="no_telp">Phone Number (62xxxxxxxxxx)</label>
                                            <div class="invalid-feedback">
                                                Please enter a valid phone number starting with 62.
                                            </div>
                                        </div>
                                        
                                        <!-- Password -->
                                        <div class="floating-label position-relative">
                                            <input type="password" id="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror"
                                                placeholder=" "
                                                required
                                                minlength="8" />
                                            <label for="password">Password</label>
                                            <i class="fas fa-eye password-toggle" id="togglePassword"></i>
                                            <div class="invalid-feedback">
                                                Password must be at least 8 characters.
                                            </div>
                                            <div class="password-strength" id="passwordStrength"></div>
                                            <div class="password-requirements">
                                                <div class="requirement" id="lengthReq">
                                                    <i class="fas fa-circle"></i>
                                                    <span>At least 8 characters</span>
                                                </div>
                                                <div class="requirement" id="numberReq">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Contains a number</span>
                                                </div>
                                                <div class="requirement" id="specialReq">
                                                    <i class="fas fa-circle"></i>
                                                    <span>Contains a special character</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Confirm Password -->
                                        <div class="floating-label position-relative">
                                            <input type="password" id="password_confirmation" name="password_confirmation"
                                                class="form-control"
                                                placeholder=" "
                                                required />
                                            <label for="password_confirmation">Confirm Password</label>
                                            <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                                            <div class="invalid-feedback">
                                                Passwords must match.
                                            </div>
                                        </div>
                                        
                                        <div class="form-group d-flex justify-content-between align-items-center mb-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="terms" required>
                                                <label class="custom-control-label" for="terms">
                                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-primary btn-block btn-icon-split">
                                            <span class="icon">
                                                <i class="fas fa-user-plus"></i>
                                            </span>
                                            <span class="text" id="registerText">Register</span>
                                            <span id="registerSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                        
                                        <div class="text-center small mt-3">
                                            Already have an account? <a href="{{ route('login') }}" class="text-primary">Sign In</a>
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
    
    <!-- Terms & Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms & Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Acceptance of Terms</h6>
                    <p>By registering an account, you agree to be bound by these Terms and Conditions.</p>
                    
                    <h6>2. Account Registration</h6>
                    <p>You must provide accurate and complete information when creating an account.</p>
                    
                    <h6>3. User Responsibilities</h6>
                    <p>You are responsible for maintaining the confidentiality of your account credentials.</p>
                    
                    <h6>4. Privacy Policy</h6>
                    <p>Your personal information will be handled in accordance with our Privacy Policy.</p>
                    
                    <h6>5. Termination</h6>
                    <p>We reserve the right to terminate accounts that violate our terms.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    @if(session()->has('alert_message'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: '{{ session('alert_title') }}',
                    text: '{{ session('alert_message') }}',
                    icon: '{{ session('alert_type') }}',
                    timer: {{ session('alert_timer') ?? 2000 }},
                    showConfirmButton: {{ session('alert_showConfirmButton') ? 'true' : 'false' }},
                    position: '{{ session('alert_position') ?? 'center' }}',
                    background: '{{ session('alert_background') ?? '#fff' }}'
                });
            });
        </script>
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
        
        // Confirm Password toggle visibility
        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            const password = document.getElementById('password_confirmation');
            const icon = this;
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        });
        
        // Password strength checker
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrength');
            const lengthReq = document.getElementById('lengthReq');
            const numberReq = document.getElementById('numberReq');
            const specialReq = document.getElementById('specialReq');
            
            // Reset classes
            strengthBar.className = 'password-strength';
            
            // Check requirements
            const hasLength = password.length >= 8;
            const hasNumber = /\d/.test(password);
            const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            
            // Update requirement indicators
            lengthReq.className = hasLength ? 'requirement valid' : 'requirement invalid';
            numberReq.className = hasNumber ? 'requirement valid' : 'requirement invalid';
            specialReq.className = hasSpecial ? 'requirement valid' : 'requirement invalid';
            
            // Calculate strength
            let strength = 0;
            if (hasLength) strength += 1;
            if (hasNumber) strength += 1;
            if (hasSpecial) strength += 1;
            
            // Update strength bar
            if (password.length === 0) {
                strengthBar.style.width = '0%';
                strengthBar.style.backgroundColor = 'transparent';
            } else if (password.length < 8) {
                strengthBar.className = 'password-strength password-strength-weak';
            } else {
                switch(strength) {
                    case 1:
                        strengthBar.className = 'password-strength password-strength-weak';
                        break;
                    case 2:
                        strengthBar.className = 'password-strength password-strength-medium';
                        break;
                    case 3:
                        strengthBar.className = 'password-strength password-strength-strong';
                        break;
                }
            }
        });
        
        // Confirm password match
        document.getElementById('password_confirmation').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (confirmPassword && password !== confirmPassword) {
                this.setCustomValidity("Passwords don't match");
            } else {
                this.setCustomValidity('');
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
                            
                            // Add shake animation to invalid fields
                            const invalidFields = form.querySelectorAll(':invalid');
                            invalidFields.forEach(field => {
                                field.classList.add('animate__animated', 'animate__headShake');
                                field.addEventListener('animationend', () => {
                                    field.classList.remove('animate__animated', 'animate__headShake');
                                }, { once: true });
                            });
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
        
        // AJAX form submission
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            const registerText = document.getElementById('registerText');
            const registerSpinner = document.getElementById('registerSpinner');
            
            registerText.textContent = 'Registering...';
            registerSpinner.classList.remove('d-none');
            
            // Add shake animation if form is invalid
            if (!this.checkValidity()) {
                this.classList.add('was-validated');
                registerText.textContent = 'Register';
                registerSpinner.classList.add('d-none');
                
                // Add shake animation to invalid fields
                const invalidFields = this.querySelectorAll(':invalid');
                invalidFields.forEach(field => {
                    field.classList.add('animate__animated', 'animate__headShake');
                    field.addEventListener('animationend', () => {
                        field.classList.remove('animate__animated', 'animate__headShake');
                    }, { once: true });
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
                    // Show success SweetAlert
                    Swal.fire({
                        title: 'Registration Successful!',
                        text: data.message || 'You are being redirected...',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        willClose: () => {
                            window.location.href = data.redirect;
                        }
                    });
                } else {
                    throw new Error(data.message || 'Registration failed');
                }
            })
            .catch(error => {
                // Reset loading state
                registerText.textContent = 'Register';
                registerSpinner.classList.add('d-none');
                
                let errorMessage = 'Registration failed. Please try again.';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).join('\n');
                } else if (error.message) {
                    errorMessage = error.message;
                }
                
                // Show error with animation
                Swal.fire({
                    title: 'Registration Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK',
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
    </script>
</body>
</html> 