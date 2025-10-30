@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-8 col-lg-5">
            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="text-decoration-none">
                    <h1 class="h3 mb-1"><b>REGISTER</b></h1>
                </a>
                <p class="text-muted small">Create a new account</p>
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

                    <form method="POST" action="{{ route('register') }}" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                                   class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                   class="form-control @error('email') is-invalid @enderror">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input id="password" type="password" name="password" required
                                   class="form-control @error('password') is-invalid @enderror">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Confirm Password</label>
                            <input id="password-confirm" type="password" name="password_confirmation" required
                                   class="form-control">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Create account</button>
                        </div>
                    </form>

                    <div class="text-center mt-3 small">
                        Already have an account? <a href="{{ route('login') }}">Sign in</a>
                    </div>

                </div>
            </div>

            <p class="text-center text-muted small mt-3">© {{ date('Y') }} {{ config('app.name', 'Anaevarec') }}.</p>
        </div>
    </div>
</div>

@endsection
