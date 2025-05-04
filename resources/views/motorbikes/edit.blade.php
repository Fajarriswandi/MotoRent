@extends('layouts.app')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush


@section('content')

    <div class="container-fluid p-0">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('motorbikes.update', $motorbike) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @include('motorbikes.partials.form', ['motorbike' => $motorbike])
        </form>
    </div>


@endsection