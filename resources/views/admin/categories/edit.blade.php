@extends('layouts.admin')
@section('title', 'Sunting Kategori')

@section('content')
<div class="container">
    <h1>Sunting Kategori: {{ $category->name }}</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection