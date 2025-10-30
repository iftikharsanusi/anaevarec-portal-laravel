@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-5">
            <div class="text-center mb-4">
                {{-- Optional: app name or logo --}}
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <h1 class="h3 mb-1"><b>LOGIN</b></h1>
                </a>
                <p class="text-muted small">Sign in to your account</p>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">

                    {{-- Session status --}}
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">{{ session('status') }}</div>
                    @endif

                    {{-- Validation errors (global) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <strong>There were some problems with your input.</strong>
                            <ul class="mb-0 mt-2">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="form-control @error('email') is-invalid @enderror"
                                   aria-describedby="emailHelp">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" required
                                   class="form-control @error('password') is-invalid @enderror"
                                   aria-describedby="passwordHelp">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>

                            <div>
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="small">Forgot password?</a>
                                @endif
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Sign in</button>
                        </div>
                    </form>

                    <div class="text-center mt-3 small">
                        @if (Route::has('register'))
                            Don't have an account? <a href="{{ route('register') }}">Register</a>
                        @endif
                    </div>

                </div>
            </div>

            <p class="text-center text-muted small mt-3">© {{ date('Y') }} {{ config('app.name', 'Anaevarec') }}. All rights reserved.</p>
        </div>
    </div>
</div>

@endsection
