@extends('layouts.admin')
@section('title', 'Tambah Kategori Baru')

@section('content')
    <h1>Tambah Kategori Baru</h1>
    <div class="card form-card">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
@endsection