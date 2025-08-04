@extends('layouts.admin')
@section('title', 'Sunting Menu')

@section('content')
    <h1>Sunting Menu: {{ $product->name }}</h1>
    <div class="card form-card">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products._form')
        </form>
    </div>
@endsection