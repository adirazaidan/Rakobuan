@extends('layouts.admin')
@section('title', 'Edit Meja')
@section('content')
    <h1>Edit Meja</h1>
    <div class="card form-card">
        <form action="{{ route('admin.dining-tables.update', $diningTable) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.dining-tables._form', ['diningTable' => $diningTable])
        </form>
    </div>
@endsection