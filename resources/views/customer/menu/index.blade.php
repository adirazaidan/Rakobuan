@extends('layouts.customer')

@section('content')
<div class="menu-container" style="padding: 2rem;">
    {{-- ... Search & Kategori ... --}}
    <div class="menu-cards" id="menu-list">
        @foreach ($products as $product)
            <div class="card">
                {{-- tambahkan class dan data- attributes pada tombol --}}
                <button class="add-to-cart-btn" 
                    data-id="{{ $product->id }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->price }}"
                    data-description="{{ $product->description }}"
                    data-image="{{ $product->image ? Storage::url('products/' . $product->image) : 'https://via.placeholder.com/150' }}">
                    + Keranjang
                </button>
                {{-- ... Sisa card ... --}}
            </div>
        @endforeach
    </div>
</div>
@endsection