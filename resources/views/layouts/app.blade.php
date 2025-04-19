<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MotoRent</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

  @vite(['resources/scss/app.scss', 'resources/js/app.js'])

</head>

<body>

  @include('layouts.navbar')

  <main class="container-fluid pt-5 ps-0 pe-0">
    @yield('content')
  </main>

  @stack('scripts')
</body>

</html>