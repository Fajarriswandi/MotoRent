@extends('layouts.appblank')

@section('content')
<div class="loginContainer vh-100 d-flex justify-content-center align-items-center">
    <div class="card p-4 text-center">
        <h4 class="mb-3">Too Many Login Attempts</h4>
        <p>Silakan tunggu <span id="countdown">{{ session('login.attempt.expires', 60) }}</span> detik sebelum mencoba lagi.</p>
        <a href="{{ route('login') }}" class="btn btn-primary mt-3">Kembali ke Login</a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let countdown = parseInt(document.getElementById('countdown').textContent);
        const el = document.getElementById('countdown');

        const interval = setInterval(() => {
            countdown--;
            el.textContent = countdown;

            if (countdown <= 0) {
                clearInterval(interval);
                window.location.href = "{{ route('login') }}";
            }
        }, 1000);
    });
</script>
@endpush
