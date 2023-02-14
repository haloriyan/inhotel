@extends('layouts.page')

@section('title', "Dailyhotels")

@section('content')
<input type="hidden" id="category" value="{{ $category }}">

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
            <a href="{{ route('user.product', [$user->username, $product->id]) }}" class="text black">
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
                                class="small" 
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
</script>
@endsection