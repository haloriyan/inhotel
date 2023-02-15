<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/base/color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/column.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/font.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/button.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/modal.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="{{ asset('css/page.css') }}">
    @yield('head.dependencies')
</head>
<body>
    
<input type="hidden" id="user" value="{{ $user }}">

<header class="TopBar fixed row item-center">
    <img src="{{ asset('storage/user_photos/' . $user->photo) }}" class="photo pointer" onclick="redirect([''])">
    <div class="flex grow-1 column ml-2 pointer" onclick="redirect([''])">
        <h1 class="m-0">{{ $user->name }}</h1>
        <div class="username text small muted">{{ "@".$user->username }}</div>
    </div>
    <div class="mr-2 flex row item-center pointer" onclick="redirect(['cart'])">
        <div class="CartCount text bold" style="background: {{ $accent_color }};">0</div>
        <div class="CartButton flex row item-center justify-center" style="background: {{ $accent_color }};">
            <span class="material-icons">shopping_cart</span>
        </div>
    </div>
</header>

<div class="container absolute">
    @yield('content')
</div>

<div class="modal" id="LoginVisitor">
    <div class="modal-body" style="width: 40%;">
        <form action="{{ route('visitor.login') }}" class="modal-content" onsubmit="loginVisitor(event)">
            {{ csrf_field() }}
            <h3 class="mt-0">Halo, boleh kami sedikit lebih mengenal tentangmu?</h3>
            <div class="group">
                <input type="text" name="name" id="name" required>
                <label for="name" class="active">Nama</label>
            </div>
            <div class="group">
                <input type="text" name="email" id="email">
                <label for="email">Email</label>
            </div>
            <div class="group">
                <input type="text" name="phone" id="phone">
                <label for="phone">No. Whatsapp</label>
            </div>

            <button class="primary w-100 mt-2" style="background-color: {{ $accent_color }}">Kirim</button>
        </form>
    </div>
</div>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ asset('js/base.js') }}"></script>
<script>
    let user = JSON.parse(select("#user").value);
    let visitor = JSON.parse(localStorage.getItem(`visitor_${user.id}`));
    
    if (visitor == null || visitor.user_id != user.id) {
        modal("#LoginVisitor").show();
    }

    const redirect = paths => {
        window.location = `/${user.username}/${paths.join('/')}`;
    }
    const loginVisitor = (e) => {
        let name = select("#LoginVisitor input#name");
        let email = select("#LoginVisitor input#email");
        let phone = select("#LoginVisitor input#phone");

        post("/api/visitor/login", {
            name: name.value,
            email: email.value,
            phone: phone.value,
            user_id: user.id
        })
        .then(res => {
            console.log(res);
            localStorage.setItem(`visitor_${user.id}`, JSON.stringify(res.visitor));
            modal("#LoginVisitor").hide();
        });

        e.preventDefault();
    }

    const loadCart = () => {
        post("/api/visitor/cart", {
            visitor_id: visitor.id,
            user_id: user.id
        })
        .then(res => {
            console.log(res);
            let count = 0;
            res.carts.forEach(cart => {
                count += cart.quantity;
            })
            select(".TopBar .CartCount").innerText = count;
        })
    }
    const addToCart = (productID) => {
        post("/api/visitor/order/add", {
            user_id: user.id,
            visitor_id: visitor.id,
            product_id: productID,
        })
        .then(res => {
            loadCart();
        })
    }
    loadCart();
</script>
@yield('javascript')

</body>
</html>