@extends('layouts.master')

@section('content')

<!-- Login Section -->
<div class="container-fluid">
    <div class="row min-vh-100 align-items-center">
        <!-- Form Column -->
        <div class="col-md-6 d-flex align-items-center justify-content-center bg-white">
<div class="card shadow px-5 py-5 w-100" style="max-width: 500px; width: 100%; min-height: 450px;">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h4 class="mb-2">Welcome back! 👋</h4>
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
                            <label for="email" class="form-label">Email or Username</label>
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
                            <label for="password" class="form-label">Password</label>
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
                        <div class="d-flex justify-content-center">
<button type="submit" class="btn btn-primary btn-lg w-100 py-3 rounded-pill" style="font-size: 20px">Login</button>
                        </div>
                    </form>

                    <div class="text-center mt-4">
                        <p class="text-muted">Don't have an account?
                            <a href="#" class="text-decoration-none">Sign up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Image Column -->
        <div class="col-md-6 p-0 d-none d-md-block">
            <div class="h-100 w-100">
                <img src="{{ asset('assets/images/login.jpg') }}"
                     alt="Login Illustration"
                     class="img-fluid w-100 h-100"
                     style="object-fit: cover;">
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
@endsection
