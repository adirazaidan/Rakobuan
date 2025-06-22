@extends('layouts.admin')
@section('title', 'Sunting Menu')

@section('content')
<div class="container">
    <h1>Sunting Menu: {{ $product->name }}</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.products._form')
        </form>
    </div>
</div>
@endsection