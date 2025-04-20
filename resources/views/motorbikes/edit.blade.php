@extends('layouts.app')
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush
@section('content')
    <div class="col-md-8 mx-auto">
        <h2 class="mb-4">Edit Motor</h2>
        <form action="{{ route('motorbikes.update', $motorbike) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('motorbikes.partials.form', ['motorbike' => $motorbike])
        </form>
    </div>
@endsection