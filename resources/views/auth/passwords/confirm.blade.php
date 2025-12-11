@extends('layouts.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 30%, #93c5fd 60%, #60a5fa 100%);
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Ensure navbar is visible above background */
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
        font-size: 1.5rem;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .login-subtitle {
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

    .form-input {
        width: 100%;
        padding: 0.75rem 0.875rem;
        padding-right: 3rem;
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

    .forgot-password {
        text-align: center;
        margin-top: 1rem;
    }

    .forgot-password a {
        color: #6b7280;
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
    }

    .forgot-password a::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 2px;
        background: #3b82f6;
        transition: width 0.3s ease;
    }

    .forgot-password a:hover {
        color: #3b82f6;
    }

    .forgot-password a:hover::after {
        width: 100%;
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
            <h1>Confirm Password</h1>
        </div>
        <p class="login-subtitle">Please confirm your password before continuing.</p>

        <form method="POST" action="{{ route('password.confirm') }}" novalidate id="confirmForm">
            @csrf

            <div class="form-group-modern">
                <div class="input-wrapper">
                    <input id="password" 
                           type="password" 
                           name="password" 
                           required 
                           autocomplete="current-password"
                           placeholder="Password"
                           class="form-input @error('password') is-invalid @enderror">
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Toggle password visibility">
                        <i class="fas fa-eye-slash" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="submit-button" id="submitBtn">
                <span>Confirm Password</span>
            </button>

            <div class="forgot-password">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Forgot Your Password?</a>
                @endif
            </div>
        </form>
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

        // Form submission loading state
        const confirmForm = document.getElementById('confirmForm');
        const submitBtn = document.getElementById('submitBtn');

        if (confirmForm && submitBtn) {
            confirmForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
            });
        }
    });
</script>

@endsection
