@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 30%, #93c5fd 60%, #60a5fa 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Ensure navbar is visible above background with glass morphism */
    .navbar {
        position: relative;
        z-index: 1000 !important;
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08), 0 1px 0 rgba(255, 255, 255, 0.5) inset;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    /* Abstract background shapes */
    .login-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 0;
        overflow: hidden;
        pointer-events: none;
    }

    .abstract-shape {
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        filter: blur(60px);
        opacity: 0.6;
    }

    .shape-1 {
        width: 400px;
        height: 400px;
        top: -100px;
        right: -100px;
        animation: float 20s ease-in-out infinite;
    }

    .shape-2 {
        width: 300px;
        height: 300px;
        bottom: -50px;
        left: -50px;
        animation: float 25s ease-in-out infinite;
        animation-delay: -5s;
    }

    .shape-3 {
        width: 250px;
        height: 250px;
        top: 50%;
        left: 10%;
        animation: float 18s ease-in-out infinite;
        animation-delay: -10s;
    }

    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) scale(1);
        }
        50% {
            transform: translate(30px, -30px) scale(1.1);
        }
    }

    .login-container {
        min-height: calc(100vh - 80px);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 8vh 1rem 2rem 1rem;
        position: relative;
        z-index: 1;
    }

    .login-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12), 0 0 0 1px rgba(255, 255, 255, 0.5);
        width: 100%;
        max-width: 380px;
        padding: 2rem;
        position: relative;
        animation: slideUp 0.6s ease-out;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.8);
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-title {
        margin-bottom: 0.375rem;
        text-align: center;
    }

    .login-title h1 {
        font-family: 'Rubik', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .login-subtitle {
        font-family: 'Rubik', sans-serif;
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 1.75rem;
        line-height: 1.5;
        font-weight: 400;
        text-align: center;
    }

    .form-group-modern {
        margin-bottom: 1rem;
    }

    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        color: #4b5563;
        font-size: 1.15rem;
        pointer-events: none;
        z-index: 10;
        transition: all 0.3s ease;
        top: 50%;
        transform: translateY(-50%);
        display: inline-block;
        line-height: 1;
        width: 20px;
        text-align: center;
    }

    .input-wrapper:focus-within .input-icon {
        color: #3b82f6;
        transform: translateY(-50%) scale(1.1);
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 0.875rem;
        font-size: 0.9rem;
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        background: #fafafa;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        outline: none;
        color: #1f2937;
        font-weight: 400;
        text-align: left;
        box-sizing: border-box;
        margin: 0;
    }

    .form-input:hover {
        border-color: #cbd5e1;
        background: #ffffff;
    }

    .form-input:focus {
        border-color: #3b82f6;
        background: #ffffff;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12), 0 4px 12px rgba(59, 130, 246, 0.08);
        transform: translateY(-1px);
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .password-toggle {
        position: absolute;
        right: 0.875rem;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        z-index: 2;
        border-radius: 6px;
        width: 32px;
        height: 32px;
    }

    .password-toggle:hover {
        color: #3b82f6;
        background: rgba(59, 130, 246, 0.08);
    }

    .submit-button {
        width: 100%;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, #374151 0%, #1f2937 50%, #111827 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-bottom: 1rem;
        box-shadow: 0 6px 20px rgba(31, 41, 55, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.05);
        position: relative;
        overflow: hidden;
        letter-spacing: 0.01em;
    }

    .submit-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        transition: left 0.5s ease;
    }

    .submit-button:hover::before {
        left: 100%;
    }

    .submit-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(31, 41, 55, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.1);
    }

    .submit-button:active {
        transform: translateY(0);
    }

    .submit-button:disabled {
        opacity: 0.7;
        cursor: not-allowed;
        transform: none;
    }

    .submit-button .spinner {
        display: none;
        width: 18px;
        height: 18px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 0.5rem;
    }

    .submit-button.loading .spinner {
        display: inline-block;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .register-link {
        text-align: center;
        margin-top: 1.25rem;
        padding-top: 1.25rem;
        border-top: 1px solid #e5e7eb;
    }

    .register-link p {
        color: #6b7280;
        font-size: 0.875rem;
        margin: 0;
        font-weight: 400;
    }

    .register-link a {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
    }

    .register-link a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #3b82f6;
        transition: width 0.3s ease;
    }

    .register-link a:hover {
        color: #2563eb;
    }

    .register-link a:hover::after {
        width: 100%;
    }

    .alert-modern {
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid;
        animation: slideIn 0.4s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .error-text {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }

    .form-input.is-invalid {
        border-color: #ef4444;
    }

    .form-input.is-invalid:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }

    @media (max-width: 576px) {
        .login-card {
            padding: 2.5rem 2rem;
            border-radius: 24px;
        }

        .login-title h1 {
            font-size: 1.75rem;
        }

        .login-subtitle {
            font-size: 0.95rem;
        }
    }
</style>

<div class="login-background">
    <div class="abstract-shape shape-1"></div>
    <div class="abstract-shape shape-2"></div>
    <div class="abstract-shape shape-3"></div>
</div>

<div class="login-container">
    <div class="login-card">
        <div class="login-title">
            <h1>Create an account</h1>
        </div>
        <p class="login-subtitle">Welcome to WSHA! Please enter your details.</p>

        {{-- Session status --}}
        @if (session('status'))
            <div class="alert alert-success alert-modern" role="alert" style="border-left-color: #10b981; background: #f0fdf4;">
                {{ session('status') }}
            </div>
        @endif

        {{-- Validation errors (global) --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-modern" role="alert" style="border-left-color: #ef4444; background: #fef2f2;">
                <strong>There were some problems with your input.</strong>
                <ul class="mb-0 mt-2" style="font-size: 0.875rem;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" novalidate id="registerForm">
            @csrf

            <div class="form-group-modern">
                <div class="input-wrapper">
                    <input id="name" 
                           type="text" 
                           name="name" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus
                           placeholder="Name"
                           class="form-input @error('name') is-invalid @enderror">
                </div>
                @error('name')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <div class="input-wrapper">
                    <input id="email" 
                           type="email" 
                           name="email" 
                           value="{{ old('email') }}" 
                           required
                           placeholder="Email"
                           class="form-input @error('email') is-invalid @enderror">
                </div>
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <div class="input-wrapper">
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required
                           placeholder="Password"
                           class="form-input @error('password') is-invalid @enderror">
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="fas fa-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
                <small style="color: #6b7280; font-size: 0.75rem; display: block; margin-top: 0.25rem;">
                    Must be at least 12 characters with uppercase, lowercase, number, and special character
                </small>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group-modern">
                <div class="input-wrapper">
                    <input id="password-confirm" 
                           type="password" 
                           name="password_confirmation" 
                           required
                           placeholder="Confirm Password"
                           class="form-input">
                    <button type="button" class="password-toggle" id="togglePasswordConfirm" aria-label="Toggle password visibility">
                        <i class="fas fa-eye-slash" id="toggleIconConfirm"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="submit-button" id="submitBtn">
                <span class="spinner"></span>
                <span>Create account</span>
            </button>
        </form>

        <div class="register-link">
            <p>
                Already have an account? 
                <a href="{{ route('login') }}">Login</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    toggleIcon.classList.remove('fa-eye-slash');
                    toggleIcon.classList.add('fa-eye');
                } else {
                    toggleIcon.classList.remove('fa-eye');
                    toggleIcon.classList.add('fa-eye-slash');
                }
            });
        }

        // Password confirm visibility toggle
        const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
        const passwordConfirmInput = document.getElementById('password-confirm');
        const toggleIconConfirm = document.getElementById('toggleIconConfirm');

        if (togglePasswordConfirm && passwordConfirmInput) {
            togglePasswordConfirm.addEventListener('click', function() {
                const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordConfirmInput.setAttribute('type', type);
                
                if (type === 'text') {
                    toggleIconConfirm.classList.remove('fa-eye-slash');
                    toggleIconConfirm.classList.add('fa-eye');
                } else {
                    toggleIconConfirm.classList.remove('fa-eye');
                    toggleIconConfirm.classList.add('fa-eye-slash');
                }
            });
        }

        // Form submission loading state
        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');

        if (registerForm && submitBtn) {
            registerForm.addEventListener('submit', function() {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
            });
        }
    });
</script>

@endsection
