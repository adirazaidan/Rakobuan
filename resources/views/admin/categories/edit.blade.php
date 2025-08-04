@extends('layouts.admin')
@section('title', 'Sunting Kategori')

@section('content')
    <h1>Sunting Kategori: {{ $category->name }}</h1>
    <div class="card form-card">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.categories._form')
        </form>
    </div>
@endsection