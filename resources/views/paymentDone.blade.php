<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Success</title>
    <link rel="stylesheet" href="{{ asset('css/base/color.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/column.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/font.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/button.css') }}">
    <link rel="stylesheet" href="{{ asset('css/base/modal.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
    <link rel="stylesheet" href="{{ asset('css/page.css') }}">
    <style>
        .content {
            position: fixed;
            top: 0px;left: 10px;right: 10px;bottom: 0px;
        }
        .check-icon {
            width: 150px;
            aspect-ratio: 1;
            border-radius: 999px;
            background: {{ $accent_color }};
            color: #fff;
            margin-bottom: 30px;
        }
        .check-icon span { 
            font-size: 48px;
        }
        @media (max-width: 480px) {
            .check-icon {
                width: 100px;
            }
        }
    </style>
</head>
<body>
    
<div class="content flex column item-center justify-center">
    <div class="check-icon flex row item-center justify-center">
        <span class="material-icons">done</span>
    </div>
    <h1>Pembayaran Berhasil</h1>
    <p class="mt-0 text center">
        {{ $message }}
    </p>

    <div class="mt-5 text center small">sekarang Anda bisa menutup laman ini</div>
</div>

</body>
</html>