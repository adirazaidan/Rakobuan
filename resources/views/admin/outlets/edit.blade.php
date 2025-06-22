@extends('layouts.admin')
@section('title', 'Sunting Outlet')

@section('content')
<div class="container">
    <h1>Sunting Outlet: {{ $outlet->name }}</h1>
    <div class="card" style="padding: 2rem;">
        <form action="{{ route('admin.outlets.update', $outlet) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('admin.outlets._form')
        </form>
    </div>
</div>
@endsection