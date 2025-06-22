@extends('layouts.admin')
@section('title', 'Tambah Outlet Baru')

@section('content')
<div class="container">
    <h1>Tambah Outlet Baru</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.outlets.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.outlets._form')
        </form>
    </div>
</div>
@endsection