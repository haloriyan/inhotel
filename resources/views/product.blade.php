<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $user->name }} | {{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{{ asset('css/base/color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/column.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/font.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/button.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/modal.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">
</head>
<body>

<input type="hidden" id="user" value="{{ $user }}">
<input type="hidden" id="product" value="{{ $product }}">
<input type="hidden" id="forceHeaderActive" value="0">

<header class="TopBar active flex row item-center justify-center" style="background: {{ $user->accent_color }}">
    <form action="#" class="rounded flex row item-center ml-2">
        <input type="text" name="q" id="q" placeholder="Cari produk" value="{{ isset($request) ? $request->q : '' }}">
        <button class="material-icons">search</button>
    </form>
    <a href="{{ route('user.cart', [$user->username]) }}">
        <button class="material-icons ml-2">shopping_cart</button>
    </a>
</header>

<div class="content">
    <div class="ImagesArea mt-2">
        @foreach ($product->images as $image)
            <img src="{{ asset('storage/product_images/' . $image->filename) }}" alt="">
        @endforeach
    </div>
    <div class="flex row item-center">
        <div class="flex column grow-1">
            <h1 class="mb-0">{{ $product->name }}</h1>
            <div class="mt-05">@currencyEncode($product->price)</div>
        </div>
        <button class="text white" style="background: {{ $user->accent_color }}" onclick="addToCart()">
            + Keranjang
        </button>
    </div>

    <div class="mt-3">
        {{ $product->description }}
    </div>
</div>

<script src="{{ asset('js/base.js') }}"></script>
<script src="{{ asset('js/App.js') }}"></script>
<script>
    select(".TopBar form").style.background = pSBC(-0.4, user.accent_color);
</script>
<script>
    let product = JSON.parse(select("input#product").value);
    const addToCart = () => {
        post("/api/visitor/order/add", {
            user_id: user.id,
            visitor_id: visitor.id,
            product_id: product.id,
        })
        .then(res => {
            console.log(res);
        })
    }
</script>
    
</body>
</html>