@extends('layouts.admin')
@section('title', 'Tambah Menu Baru')

@section('content')
    <h1>Tambah Menu Baru</h1>
    <div class="card form-card">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
@endsection