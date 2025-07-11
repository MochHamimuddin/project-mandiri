<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Project Mandiri</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        .divider:after,
        .divider:before {
            content: "";
            flex: 1;
            height: 1px;
            background: #eee;
        }
        .h-custom {
            height: calc(100% - 73px);
        }
        @media (max-width: 450px) {
            .h-custom {
                height: 100%;
            }
        }
        .bg-auth {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body class="bg-auth">
    <section class="vh-100">
        <div class="container-fluid h-custom">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-md-9 col-lg-6 col-xl-5">
                    <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-login-form/draw2.webp"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                        <div class="card-body">
                            <form method="POST" action="{{ route('register') }}" id="registerForm">
                                @csrf
                                <div class="divider d-flex align-items-center my-4">
                                    <h3 class="text-center fw-bold mx-3 mb-0">Register</h3>
                                </div>

                                @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                <!-- Username Input -->
                                <div class="form-outline mb-4">
                                    <input type="text" id="username" name="username"
                                        class="form-control form-control-lg @error('username') is-invalid @enderror"
                                        placeholder="Enter your username"
                                        value="{{ old('username') }}"
                                        required autofocus />
                                    <label class="form-label" for="username">Username</label>
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Full Name Input -->
                                <div class="form-outline mb-4">
                                    <input type="text" id="nama_lengkap" name="nama_lengkap"
                                        class="form-control form-control-lg @error('nama_lengkap') is-invalid @enderror"
                                        placeholder="Enter your full name"
                                        value="{{ old('nama_lengkap') }}"
                                        required />
                                    <label class="form-label" for="nama_lengkap">Full Name</label>
                                    @error('nama_lengkap')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Email Input -->
                                <div class="form-outline mb-4">
                                    <input type="email" id="email" name="email"
                                        class="form-control form-control-lg @error('email') is-invalid @enderror"
                                        placeholder="Enter your email"
                                        value="{{ old('email') }}"
                                        required />
                                    <label class="form-label" for="email">Email Address</label>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Password Input -->
                                <div class="form-outline mb-3">
                                    <input type="password" id="password" name="password"
                                        class="form-control form-control-lg @error('password') is-invalid @enderror"
                                        placeholder="Enter password" required />
                                    <label class="form-label" for="password">Password</label>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <!-- Confirm Password Input -->
                                <div class="form-outline mb-3">
                                    <input type="password" id="password_confirmation" name="password_confirmation"
                                        class="form-control form-control-lg"
                                        placeholder="Confirm password" required />
                                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                                </div>

                                <!-- Phone Number Input -->
                                <div class="form-outline mb-4">
                                    <input type="text" id="no_telp" name="no_telp"
                                        class="form-control form-control-lg @error('no_telp') is-invalid @enderror"
                                        placeholder="Enter phone number (optional)"
                                        value="{{ old('no_telp') }}" />
                                    <label class="form-label" for="no_telp">Phone Number</label>
                                    @error('no_telp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="text-center text-lg-start mt-4 pt-2">
                                    <button type="submit" class="btn btn-primary btn-lg"
                                        style="padding-left: 2.5rem; padding-right: 2.5rem;">
                                        <span id="registerText">Register</span>
                                        <span id="registerSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                    <p class="small fw-bold mt-2 pt-1 mb-0">Already have an account?
                                        <a href="{{ route('login') }}" class="link-danger">Login</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
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
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const registerText = document.getElementById('registerText');
            const registerSpinner = document.getElementById('registerSpinner');

            registerText.classList.add('d-none');
            registerSpinner.classList.remove('d-none');
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
                    Swal.fire({
                        title: 'Registration Successful!',
                        text: data.message || 'You are being redirected...',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false,
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
                registerText.classList.remove('d-none');
                registerSpinner.classList.add('d-none');

                let errorMessage = 'Registration failed. Please try again.';
                if (error.errors) {
                    errorMessage = Object.values(error.errors).join('\n');
                } else if (error.message) {
                    errorMessage = error.message;
                }

                Swal.fire({
                    title: 'Error!',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
</body>
</html>
