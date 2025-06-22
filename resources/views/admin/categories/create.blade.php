@extends('layouts.admin')
@section('title', 'Tambah Kategori Baru')

@section('content')
<div class="container">
    <h1>Tambah Kategori Baru</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            @include('admin.categories._form')
        </form>
    </div>
</div>
@endsection