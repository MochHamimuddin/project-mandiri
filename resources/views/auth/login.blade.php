<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Project Mandiri</title>
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
                    <img src="https://seen.asia/file/logocorporate/Logo_CR-U-AA-000446530861be54ae.png"
                        class="img-fluid" alt="Sample image">
                </div>
                <div class="col-md-8 col-lg-6 col-xl-4 offset-xl-1">
                    <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                        <div class="card-body">
                            <form method="POST" action="{{ route('login') }}" id="loginForm">
                                @csrf
                                <div class="divider d-flex align-items-center my-4">
                                    <h3 class="text-center fw-bold mx-3 mb-0">Login</h3>
                                </div>

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

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-body">Forgot password?</a>
                                    @endif
                                </div>

                                <div class="text-center text-lg-start mt-4 pt-2">
                                    <button type="submit" class="btn btn-primary btn-lg"
                                        style="padding-left: 2.5rem; padding-right: 2.5rem;">
                                        <span id="loginText">Login</span>
                                        <span id="loginSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    </button>
                                    <p class="small fw-bold mt-2 pt-1 mb-0">Don't have an account?
                                        <a href="{{ route('register') }}" class="link-danger">Register</a>
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
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // Show loading state
            const loginText = document.getElementById('loginText');
            const loginSpinner = document.getElementById('loginSpinner');

            loginText.classList.add('d-none');
            loginSpinner.classList.remove('d-none');

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
                        title: 'Login Successful!',
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
                    throw new Error(data.message || 'Login failed');
                }
            })
            .catch(error => {
                // Reset loading state
                loginText.classList.remove('d-none');
                loginSpinner.classList.add('d-none');

                let errorMessage = 'Login failed. Please try again.';
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
