@extends('layouts.page')

@section('title', "Dailyhotels")

@section('head.dependencies')
<style>
    #TopCartButton {
        width: 45px;
        height: 45px;
        padding: 0px;
        background: none;
        position: absolute;
        left: 10px;top: 10px;
        color: #fff;
        border-radius: 99px;
        opacity: 0.75;
    }
    #TopCartButton span {
        font-size: 12px;
    }
    #TopCartButton:hover { opacity: 1; }
</style>
@endsection

@section('content')
<input type="hidden" id="category" value="{{ $category }}">

<button id="TopCartButton" onclick="redirect(['cart'])" style="background: {{ $user->accent_color }}">
    <span class="material-icons">shopping_cart</span>
</button>
<img src="{{ asset('storage/user_covers/' . $user->cover) }}" alt="Cover" class="cover">

<div class="ProfileArea">
    <div class="text center">
        <img src="{{ asset('storage/user_photos/' . $user->photo) }}" class="photo">
        <h2 class="m-0">{{ $user->name }}</h2>
        <div class="text small muted">{{ "@".$user->username }}</div>
    </div>
    <div class="flex row item-center justify-center mt-1">
        @foreach ($user->socials as $social)
            <a href="{{ $social->url }}" target="_blank">
                <div class="social-item rounded-max flex row item-center justify-center" style="background: {{ $accent_color }}30">
                    <img src="{{ asset('icons/' . strtolower($social->type) . '.png') }}" alt="">
                </div>
            </a>
        @endforeach
    </div>
</div>

<div class="padding-on-mobile">
    @if ($banners->count() > 0)
        <div class="ImagesArea mt-2" style="height: 120px;">
            @foreach ($banners as $image)
                <a href="{{ $image->link }}" target="_blank">
                    <img src="{{ asset('storage/banner_images/' . $image->filename) }}" alt="">
                </a>
            @endforeach
        </div>
    @endif
    <div class="CategoryArea mt-2 bordered p-2 pl-0 pr-0 flex row item-center justify-center">
        <div class="flex column grow-1 item-center pointer" style="color: {{ $category == '' ? $user->accent_color : '#444' }}" onclick="chooseCategory('')">
            <ion-icon name="business-outline"></ion-icon>
            <div class="text small mt-05 {{ $category == '' ? 'bold' : '' }}">Semua</div>
        </div>
        <div class="flex column grow-1 item-center pointer" style="color: {{ $category == 'fnb' ? $user->accent_color : '#444' }}" onclick="chooseCategory('fnb')">
            <ion-icon name="fast-food-outline"></ion-icon>
            <div class="text small mt-05 {{ $category == 'fnb' ? 'bold' : '' }}">FnB</div>
        </div>
        <div class="flex column grow-1 item-center pointer" style="color: {{ $category == 'mice' ? $user->accent_color : '#444' }}" onclick="chooseCategory('mice')">
            <ion-icon name="calendar-outline"></ion-icon>
            <div class="text small mt-05 {{ $category == 'mice' ? 'bold' : '' }}">MICE</div>
        </div>
        <div class="flex column grow-1 item-center pointer" style="color: {{ $category == 'gym' ? $user->accent_color : '#444' }}" onclick="chooseCategory('gym')">
            <span class="material-icons">fitness_center</span>
            <div class="text small mt-05 {{ $category == 'gym' ? 'bold' : '' }}">Gym</div>
        </div>
        <div class="flex column grow-1 item-center pointer" style="color: {{ $category == 'pool' ? $user->accent_color : '#444' }}" onclick="chooseCategory('pool')">
            <span class="material-icons">pool</span>
            <div class="text small mt-05 {{ $category == 'pool' ? 'bold' : '' }}">Pool</div>
        </div>
        <div class="flex column grow-1 item-center pointer" style="color: {{ $category == 'other' ? $user->accent_color : '#444' }}" onclick="chooseCategory('other')">
            <span class="material-icons">more_horiz</span>
            <div class="text small mt-05 {{ $category == 'other' ? 'bold' : '' }}">Lainnya</div>
        </div>
    </div>

    <div class="ProductArea mt-3">
        @foreach ($products as $product)
            <a href="{{ route('user.product', [$user->username, $product->id]) }}" class="text black" onclick="productLink(event)">
                <div class="product-item flex row ">
                    <img src="{{ asset('storage/product_images/' . $product->images[0]->filename) }}" class="product-image">
                    <div class="flex grow-1 column shrink-1 ml-2">
                        <div class="text bold">{{ $product->name }}</div>
                        <div class="flex row item-center mt-1">
                            <div class="flex column grow-1">
                                <div>@currencyEncode($product->price)</div>
                            </div>
                            <button 
                                onclick="addToCart('{{ $product->id }}')"
                                class="text small" 
                                style="background: {{ $accent_color }};color: #fff;"
                            >
                                + Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection

@section('javascript')
<script>
    const chooseCategory = category => {
        window.location = `/${user.username}/${category}`;
    }
    window.addEventListener('scroll', e => {
        let scroll = window.scrollY;
        if (scroll > 100) {
            select(".TopBar").classList.add('active');
        } else {
            select(".TopBar").classList.remove('active');
        }
    });

    const productLink = e => {
        if (e.target.tagName == 'BUTTON') {
            e.preventDefault();
        }
    }
</script>
@endsection