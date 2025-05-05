@extends('layouts.appblank')

@section('content')

<div class="loginContainer vh-100 d-flex justify-content-center align-items-center">
    <div class="card">
        <img src="{{ asset('images/iconLogo.png') }}" alt="Logo MotoRent" width="50" class="text-center mb-4">
        <div class="text-center mb-4">
            <h3>Welcome Back!</h3>
            <h5>Let's get you signed in securely.</h5>
        </div>

        {{-- 🔒 Alert jika melebihi limit login --}}
        @if (session('login.attempt.expires'))
            <div class="alert alert-danger text-center mx-4" id="lockoutAlert">
                Terlalu banyak percobaan login. Coba lagi dalam 
                <span id="countdown">{{ session('login.attempt.expires') }}</span> detik.
            </div>
        @endif

        @if (session()->has('login.attempt.expires'))
            <div class="alert alert-danger">
                Terlalu banyak percobaan login. Silakan coba lagi dalam 
                <span id="retryCountdown">{{ session('login.attempt.expires') }}</span> detik.
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    let countdown = parseInt(document.getElementById('retryCountdown').textContent);
                    const el = document.getElementById('retryCountdown');
                    const interval = setInterval(() => {
                        countdown--;
                        if (countdown <= 0) {
                            clearInterval(interval);
                            window.location.reload(); // Refresh halaman saat bisa login lagi
                        }
                        el.textContent = countdown;
                    }, 1000);
                });
            </script>
        @endif


        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-floating mb-3">
                <input type="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="">
                <label for="email">Email address</label>

                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="form-floating">
                <input id="password" type="password" 
                    class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password" placeholder="">
                <label for="password">Password</label>

                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="row mb-3 mt-4">
                <div class="col-md-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                            {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember Me
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary d-block w-100 pt-3 pb-3">
                        Login
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
    @if (session('login.attempt.expires'))
        <script>
            let countdown = parseInt(document.getElementById("countdown").textContent);
            let countdownEl = document.getElementById("countdown");

            let timer = setInterval(() => {
                countdown--;
                if (countdown <= 0) {
                    clearInterval(timer);
                    location.reload();
                } else {
                    countdownEl.textContent = countdown;
                }
            }, 1000);
        </script>
    @endif
@endpush
