@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        <form action="{{ route('motorbikes.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('motorbikes.partials.form', ['motorbike' => null])
        </form>
    </div>
@endsection