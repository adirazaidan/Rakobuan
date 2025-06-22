@extends('layouts.admin')
@section('title', 'Tambah Menu Baru')

@section('content')
<div class="container">
    <h1>Tambah Menu Baru</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.products._form')
        </form>
    </div>
</div>
@endsection