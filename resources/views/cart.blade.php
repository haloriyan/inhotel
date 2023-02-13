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
    <link rel="stylesheet" href="{{ asset('css/cart.css') }}">
    <link rel="stylesheet" href="{{ asset('js/flatpickr/dist/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('js/flatpickr/dist/themes/airbnb.css') }}">
</head>
<body>
    
<input type="hidden" id="user" value="{{ $user }}">

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
    <form action="#">
        @foreach ($carts as $i => $item)
            @php
                $product = $item->product;
            @endphp
            <div class="flex row mt-2 product-item">
                <img src="{{ asset('storage/product_images/' . $product->images[0]->filename ) }}" class="rounded product-image">
                <div class="product-info flex column grow-1">
                    <h4 class="m-0">{{ $product->name }}</h4>
                    <div class="mt-05" id="PriceArea_{{ $i }}">@currencyEncode($product->price)</div>
                    <input type="hidden" class="price" id="price_{{ $i }}" value="{{ $product->price }}">

                    <div class="flex row">
                        <div class="group flex column grow-1">
                            <input type="text" name="book_date[]" class="book_date" id="BookDate_{{ $i }}">
                            <label for="BookDate_{{ $i }}" class="active">Tanggal</label>
                        </div>
                        <div class="flex row w-20 ml-2 w-50 item-center">
                            <span 
                                onclick="setQuantity('decrease', '{{ $i }}', '{{ $product->price}}')"
                                style="border-color: {{ $user->accent_color }};color: {{ $user->accent_color }}" 
                                class="bordered mt-1 rounded pointer p-1 pl-2 pr-2 mr-1"
                            >-</span>
                            <div class="group">
                                <input type="text" name="pax[]" id="pax_{{ $i }}" value="1" min="1">
                                <label for="quantity">Pax</label>
                            </div>
                            <span 
                                onclick="setQuantity('increase', '{{ $i }}', '{{ $product->price}}')"
                                style="border-color: {{ $user->accent_color }};color: {{ $user->accent_color }}" 
                                class="bordered mt-1 rounded pointer p-1 pl-2 pr-2 ml-1"
                            >+</span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="mt-3 mb-3 flex row">
            <div class="text bold big flex grow-1">Total</div>
            <div id="TotalPrice">@currencyEncode($carts->sum('total'))</div>
        </div>

        <button type="button" class="w-100 text white" style="background-color: {{ $user->accent_color }}">Checkout</button>
    </form>
</div>

<script src="{{ asset('js/base.js') }}"></script>
<script src="{{ asset('js/Currency.js') }}"></script>
<script src="{{ asset('js/App.js') }}"></script>
<script src="{{ asset('js/flatpickr/dist/flatpickr.min.js') }}"></script>
<script>
    select(".TopBar form").style.background = pSBC(-0.4, user.accent_color);
</script>
<script>
    const calculateTotal = () => {
        let total = 0;
        selectAll(".price").forEach(input => {
            let price = parseInt(input.value);
            total += price;
        });
        select("#TotalPrice").innerText = Currency(total).encode();
    }
    const setQuantity = (type, index, price) => {
        let input = select(`#pax_${index}`);
        let qty = parseInt(input.value);
        let newQty = null;
        
        if (type == 'decrease') {
            if ((qty - 1) > 0) {
                newQty = qty - 1;
                input.value = newQty;
            }
        } else {
            newQty = qty + 1;
            input.value = newQty;
        }

        let newPrice = parseInt(price) * newQty;
        select(`#PriceArea_${index}`).innerText = Currency(newPrice).encode();
        select(`input#price_${index}`).value = newPrice;
        calculateTotal();
    }

    flatpickr(".book_date", {
        minDate: "{{ date('Y-m-d') }}"
    });
</script>

</body>
</html>