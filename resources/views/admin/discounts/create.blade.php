@extends('layouts.admin')
@section('title', 'Tambah Diskon Baru')
@section('content')
    <h1>Tambah Diskon Baru</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.discounts.store') }}" method="POST">
            @csrf
            @include('admin.discounts._form')
        </form>
    </div>
@endsection