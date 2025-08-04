@extends('layouts.admin')
@section('title', 'Sunting Outlet')

@section('content')
    <h1>Sunting Outlet: {{ $outlet->name }}</h1>
    <div class="card form-card">
        <form action="{{ route('admin.outlets.update', $outlet) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.outlets._form')
        </form>
    </div>
@endsection