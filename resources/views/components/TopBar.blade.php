<header class="TopBar flex row item-center justify-center">
    <form action="#" class="bordered rounded flex row item-center ml-2">
        <input type="text" name="q" id="q" placeholder="Cari produk" value="{{ isset($request) ? $request->q : '' }}">
        <button class="material-icons">search</button>
    </form>
    <a href="{{ route('user.cart', [$username]) }}">
        <button class="material-icons ml-2">shopping_cart</button>
    </a>
</header>