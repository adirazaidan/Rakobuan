@extends('layouts.admin')
@section('title', 'Tambah Meja Baru')
@section('content')
    <h1>Tambah Meja Baru</h1>
    <div class="card form-card">
        <form action="{{ route('admin.dining-tables.store') }}" method="POST">
            @csrf
            @include('admin.dining-tables._form')
        </form>
    </div>
@endsection