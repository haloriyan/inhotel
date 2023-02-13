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
    <link rel="stylesheet" href="{{ asset('css/page.css') }}">
</head>
<body>
    
<input type="hidden" id="user" value="{{ $user }}">
<input type="hidden" id="forceHeaderActive" value="0">

@include('components.TopBar', [
    'request' => $request,
    'username' => $user->username
])
@include('components.LoginVisitor')

<div class="content">
    <div class="ProfileArea relative">
        <img src="{{ asset('storage/user_covers/' . $user->cover) }}" alt="Cover" class="cover">
        <div class="ProfileContent flex column item-center justify-end">
            <img src="{{ asset('storage/user_photos/' . $user->photo) }}" alt="Photo" class="photo">
            <div class="ProfileInfo">
                <h1>{{ $user->name }}</h1>
                <p class="m-0">{{ $user->bio }}</p>
            </div>
        </div>
    </div>

    <div class="CategoryArea">
        <div class="CategoryContainer bg-white flex row item-center justify-center bordered rounded p-2">
            <div class="CategoryItem pointer flex column grow-1 item-center justify-center" style="color: {{ $category == '' ? $user->accent_color : '#444' }}" onclick="chooseCategory('')">
                <ion-icon name="business-outline"></ion-icon>
                <div class="text small mt-05">Semua</div>
            </div>
            <div class="CategoryItem pointer flex column grow-1 item-center justify-center" style="color: {{ $category == 'fnb' ? $user->accent_color : '#444' }}" onclick="chooseCategory('fnb')">
                <ion-icon name="fast-food-outline"></ion-icon>
                <div class="text small mt-05">FnB</div>
            </div>
            <div class="CategoryItem pointer flex column grow-1 item-center justify-center" style="color: {{ $category == 'mice' ? $user->accent_color : '#444' }}" onclick="chooseCategory('mice')">
                <ion-icon name="calendar-outline"></ion-icon>
                <div class="text small mt-05">MICE</div>
            </div>
            <div class="CategoryItem pointer flex column grow-1 item-center justify-center" style="color: {{ $category == 'gym' ? $user->accent_color : '#444' }}" onclick="chooseCategory('gym')">
                <span class="material-icons">fitness_center</span>
                <div class="text small mt-05">Gym</div>
            </div>
            <div class="CategoryItem pointer flex column grow-1 item-center justify-center" style="color: {{ $category == 'pool' ? $user->accent_color : '#444' }}" onclick="chooseCategory('pool')">
                <span class="material-icons">pool</span>
                <div class="text small center mt-05">Pool</div>
            </div>
            <div class="CategoryItem pointer flex column grow-1 item-center justify-center" style="color: {{ $category == 'other' ? $user->accent_color : '#444' }}" onclick="chooseCategory('other')">
                <span class="material-icons">more_horiz</span>
                <div class="text small mt-05">Lainnya</div>
            </div>
        </div>
    </div>

    <div class="h-50"></div>
    <div class="ProductArea p-4 flex row ">
        @foreach ($products as $product)
            <div class="product-inner bg-white bordered m-2">
                <a href="{{ route('user.product', [$user->username, $product->id]) }}">
                    <img src="{{ asset('storage/product_images/' . $product->images[0]->filename) }}" class="product-image">
                </a>
                <div class="p-2 flex row wrap item-center">
                    <a href="#" class="flex column grow-1 shrink-1 product-info w-60 text black">
                        <h4 class="m-0">{{ $product->name }}</h4>
                        <div>Rp 25.240.000</div>
                    </a>
                    <a href="/">
                        <button class="primary">Beli</button>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="p-4">
        @foreach ($galleries as $gallery)
            <h3>{{ $gallery->name }}</h3>
            <div class="GalleryContainer" id="gallery_{{ $gallery->id }}" divide="{{ $gallery->item_per_row }}">
                @foreach ($gallery->images as $image)
                    <div class="GalleryItem">
                        <div class="m-2">
                            <img src="{{ asset('storage/gallery_images/' . $user->id . '/' . $image->filename) }}" class="w-100">
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    <div style="height: 2430px"></div>
</div>

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="{{ asset('js/base.js') }}"></script>
<script src="{{ asset('js/Masonryan.js') }}"></script>
<script src="{{ asset('js/App.js') }}"></script>
<script>
    const chooseCategory = category => {
        window.location = `/${user.username}/${category}`;
    }

    selectAll(".primary").forEach(el => {
        el.style.backgroundColor = user.accent_color;
    });

    let masonries = {};
    selectAll(".GalleryContainer").forEach(GalleryContainer => {
        let id = GalleryContainer.getAttribute('id');
        let divide = GalleryContainer.getAttribute('divide');

        masonries[id] = new Masonryan({
            container: `#${id}`,
            items: `#${id} .GalleryItem`,
            dividedBy: divide
        })
    })
</script>

</body>
</html>