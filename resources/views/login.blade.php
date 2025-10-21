@extends('layouts.master')

@section('content')

<!-- Login Section -->
<div class="container-fluid min-vh-100 d-flex align-items-center justify-content-center"
     style="background: linear-gradient(135deg, #6B73FF 0%, #000DFF 100%);">
    <div class="row w-100 justify-content-center">
        <!-- Form Column -->
        <div class="col-md-6 d-flex align-items-center justify-content-center">
            <div class="card p-5 shadow-lg"
                 style="max-width: 500px; width: 100%; min-height: 500px;
                        border-radius: 20px; background: rgba(255,255,255,0.85);
                        backdrop-filter: blur(10px);">

                <div class="card-body">
                    <div class="text-center mb-4">
                        <h3 class="fw-bold mb-2">Welcome Back! 👋</h3>
                        <p class="text-muted">Log in to your account to continue</p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                            <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
                        @csrf
                        <input type="hidden" name="redirect_url" value="{{ request('redirect_url') }}">

                        <!-- Email Field -->
                        <div>
                            <label for="email" class="form-label fw-semibold">Email or Username</label>
                            <input type="text"
                                   class="form-control form-control-lg"
                                   id="email"
                                   style="height: 60px;"
                                   name="email"
                                   placeholder="Enter your email or username"
                                   value="{{ old('email') }}">
                            @error('email')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div>
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <div class="input-group input-group-lg">
                                <input type="password"
                                       id="password"
                                       class="form-control"
                                       style="height: 60px;"
                                       name="password"
                                       placeholder="••••••••">
                                <span class="input-group-text toggle-password" style="cursor: pointer;">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            @error('password')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- Login Button -->
                        <div class="d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill"
                                    style="font-size: 20px; transition: all 0.3s ease;">
                                Login
                            </button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">Don't have an account?
                            <a href="#" class="text-decoration-none fw-semibold text-primary">Sign up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Column -->
        <div class="col-md-6 d-none d-md-block">
            <div class="h-100 w-100 position-relative overflow-hidden">
                <img src="{{ asset('assets/img/login.jpg') }}"
                     alt="Login Illustration"
                     class="img-fluid w-100 h-100"
                     style="object-fit: cover; filter: brightness(0.7); transition: transform 0.5s;">
            </div>
        </div>
    </div>
</div>

<!-- Password Toggle Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.querySelector('.toggle-password');
        if (togglePassword) {
            togglePassword.addEventListener('click', function () {
                const passwordInput = document.getElementById('password');
                const icon = this.querySelector('i');

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.replace('bx-hide', 'bx-show');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.replace('bx-show', 'bx-hide');
                }
            });
        }
    });
</script>

<style>
    /* Login button hover effect */
    .btn-primary:hover {
        background-color: #000dff;
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    }

    /* Image hover zoom effect */
    .col-md-6 img:hover {
        transform: scale(1.05);
    }
</style>

@endsection
