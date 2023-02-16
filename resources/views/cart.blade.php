@extends('layouts.page')

@php
    use Carbon\Carbon;
@endphp

@section('head.dependencies')
<link rel="stylesheet" href="{{ asset('css/cart.css') }}">
<link rel="stylesheet" href="{{ asset('js/flatpickr/dist/flatpickr.min.css') }}">
<link rel="stylesheet" href="{{ asset('js/flatpickr/dist/themes/airbnb.css') }}">
<style>
    .quantity-control {
        padding: 5px 10px;
        cursor: pointer;
        border-radius: 7px;
    }
</style>
@endsection

@section('content')
<div style="height: 120px;"></div>
<div class="padding-on-mobile">
    <form onsubmit="checkout(event)">
        <div id="CartArea"></div>
        <button class="small w-100" id="CheckoutButton" style="background: {{ $accent_color }};color: #fff">Bayar Sekarang</button>
    </form>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/flatpickr/dist/flatpickr.min.js') }}"></script>
<script src="{{ asset('js/element.js') }}"></script>
<script src="{{ asset('js/Currency.js') }}"></script>
<script>
    select(".TopBar").classList.add('active');
    const focusing = input => {
        input.classList.remove('red')
    }
    const setQuantity = (id, type) => {
        post("/api/visitor/cart-quantity", {
            id: id,
            action: type
        })
        .then(res => {
            getCart();
        })
    }

    const getCart = () => {
        post("/api/visitor/cart", {
            visitor_id: visitor.id,
            user_id: user.id,
        })
        .then(res => {
            let carts = res.carts;
            select("#CartArea").innerHTML = "";
            let count = 0;
            carts.forEach(cart => {
                count += cart.quantity;
            })
            select(".TopBar .CartCount").innerText = count;
            if (carts.length == 0) {
                select("#CartArea").innerHTML = `<div class='text center mt-1 mb-3'>Anda belum menambahkan produk di keranjang. 
                <br /><span class="pointer text bold" onclick="redirect([''])" style="color: ${user.accent_color}">Lihat produk</span> dari ${user.name}</div>`
            } else {
                let datepickers = [];
                carts.forEach(item => {
                    let toRender = `
                    <img src="/storage/product_images/${item.product.images[0].filename}" class="product-image">
                    <div class="flex grow-1 column shrink-1 ml-2">
                        <div class="text bold">${item.product.name}</div>
                        <div class="flex row item-center mt-1">
                            <div class="flex column grow-1">
                                <div>${Currency(item.total).encode()}</div>
                            </div>
                            <div class="flex row item-center">`;
                    if (item.quantity > 1) {
                        toRender += `<div 
                            class="quantity-control mr-1" 
                            style="border: 1px solid ${user.accent_color};color: ${user.accent_color};"
                            onclick="setQuantity('${item.id}', 'decrease')"
                        >-</div>`;
                    } else {
                        toRender += `<div 
                            class="quantity-control mr-1" 
                            style="border: 1px solid ${user.accent_color};color: ${user.accent_color};"
                            onclick="setQuantity('${item.id}', 'decrease')"
                        >remove</div>`;
                    }
                    toRender += `${item.quantity} pax(s)`;
                    toRender += `<div 
                                    class="quantity-control ml-1" 
                                    style="border: 1px solid ${user.accent_color};color: ${user.accent_color};"
                                    onclick="setQuantity('${item.id}', 'increase')"
                                >+</div>
                            </div></div>`;

                    toRender += `<div class='mt-2 group'>
                        <input id="bookdate_${item.id}" class="bookdate" name="bookdate[]" item_id="${item.id}" class="h-45 p-1" onfocus="focusing(this)" placeholder="Pilih tanggal dan waktu" required />
                        <label for="bookdate_${item.id}" class='active'>Tanggal Booking</label>
                    </div>`;
                    toRender += `</div>`;
                    
                    Element("div", {
                        class: "product-item flex row",
                        id: `item_${item.id}`,
                    })
                    .render("#CartArea", toRender);

                    flatpickr(`#bookdate_${item.id}`, {
                        dateFormat: 'Y-m-d',
                        minDate: "{{ Carbon::now()->format('Y-m-d') }}"
                    });
                })
            }
        })
    }
    getCart();

    const checkout = (event) => {
        let isValid = true;
        let itemsNotValid = [];

        let bookDates = selectAll(".bookdate");
        bookDates.forEach(d => {
            if (d.value == "") {
                isValid = false;
                let itemID = d.getAttribute('item_id');
                itemsNotValid.push(itemID);
            }
        })

        if (isValid) {
            let formData = new FormData();
            formData.append('visitor_id', visitor.id);
            formData.append('user_id', user.id);
            bookDates.forEach(dt => {
                formData.append('bookdate[]', dt.value);
            });

            select("#CheckoutButton").innerHTML = "Memproses...";
            let req = fetch("/api/visitor/checkout", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(res => {
                window.location = res.payment.payment_link;
            })
        } else {
            showBadge('Mohon melengkapi tanggal booking untuk setiap order Anda', '#e74c3c');
            itemsNotValid.forEach(item => {
                select(`.bookdate[item_id='${item}']`).classList.add('bordered', 'red');
            })
        }
        event.preventDefault();
    }
</script>
@endsection