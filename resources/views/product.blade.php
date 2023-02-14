@extends('layouts.page')

@section('head.dependencies')
<style>
    body {
        background-color: #eee;
    }
    .detail {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
</style>
@endsection

@section('content')
<div class="h-90"></div>
<div class="ImagesArea mt-2">
    @foreach ($product->images as $image)
        <img src="{{ asset('storage/product_images/' . $image->filename) }}" alt="">
    @endforeach
</div>

<div class="bordered bg-white detail p-3 mt-2">
    <h3 class="m-0">{{ $product->name }}</h3>
    <pre>{{ $product->description }}</pre>
</div>

<div class="h-50"></div>

<div class="bottom-control fixed bg-white h-70 flex row item-center bordered">
    <div class="flex grow-1 text big bold" style="color: {{ $accent_color }}">
        @currencyEncode($product->price)
    </div>
    <button 
        onclick="addToCart('{{ $product->id }}')"
        class="text small white" 
        style="background: {{ $accent_color }}"
    >
        + Keranjang
    </button>
</div>
@endsection

@section('javascript')
<script>
    select(".TopBar").classList.add('active');
</script>
@endsection