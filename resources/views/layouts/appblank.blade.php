<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorent - Gaya Bali Visa</title>
    <link rel="icon" href="{{ asset('images/icon.png') }}" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>

<body>

    <!-- <main class="container-fluid ps-0 pe-0">
    
  </main> -->

    <div id="mainBlank">
        @yield('content')
    </div>


    @stack('scripts')
</body>

</html>