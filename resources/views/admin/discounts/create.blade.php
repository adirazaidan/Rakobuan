@extends('layouts.admin')
@section('title', 'Tambah Diskon Baru')
@section('content')
    <h1>Tambah Diskon Baru</h1>
    <div class="card form-card">
        <form action="{{ route('admin.discounts.store') }}" method="POST">
            @csrf
            @include('admin.discounts._form')
        </form>
    </div>
@endsection