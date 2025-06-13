<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Driver - Taxi</title>
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/taxi-app.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@200..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/flash-messages.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/home.css') }}"> --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('css')
</head>

<body>
    {{-- @include('partials.header') --}}

    <div class="flash-messages-container">
        @include('partials.flash-messages')
    </div>

    @yield('content')

    @yield('js')
    <script src="{{ asset('js/flash-messages.js') }}"></script>
    {{-- <script src="{{ asset('js/home.js') }}"></script> --}}
</body>

</html>
