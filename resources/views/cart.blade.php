@extends('layouts.page')

@section('head.dependencies')
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
    <div id="CartArea"></div>

    <button class="small w-100" id="CheckoutButton" onclick="checkout(this)" style="background: {{ $accent_color }};color: #fff">Bayar Sekarang</button>
</div>
@endsection

@section('javascript')
<script src="{{ asset('js/element.js') }}"></script>
<script src="{{ asset('js/Currency.js') }}"></script>
<script>
    select(".TopBar").classList.add('active');
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
            if (carts.length == 0) {
                // 
            } else {
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
                    }
                    toRender += `${item.quantity} pax(s)`;
                    toRender += `<div 
                                    class="quantity-control ml-1" 
                                    style="border: 1px solid ${user.accent_color};color: ${user.accent_color};"
                                    onclick="setQuantity('${item.id}', 'increase')"
                                >+</div>
                            </div>
                        </div>
                    </div>`;
                    
                    Element("div", {
                        class: "product-item flex row"
                    })
                    .render("#CartArea", toRender);
                })
            }
        })
    }
    getCart();

    const checkout = (btn) => {
        btn.innerHTML = "Memproses...";
        post("/api/visitor/checkout", {
            visitor_id: visitor.id,
            user_id: user.id,
        })
        .then(res => {
            window.location = res.payment.payment_link;
        })
    }
</script>
@endsection