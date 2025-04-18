@extends('layouts.app')

@section('content')
<div class="col-md-8 mx-auto">
    <h2 class="mb-4">Tambah Motor</h2>
    <form action="{{ route('motorbikes.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('motorbikes.partials.form', ['motorbike' => null])
    </form>
</div>
@endsection