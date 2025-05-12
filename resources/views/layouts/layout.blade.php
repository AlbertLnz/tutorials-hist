<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>@yield('title')</title>

  {{-- CSS --}}
  {{-- @vite('resources/css/header.css') --}}

  {{-- Tailwind CSS --}}
  @vite('resources/css/app.css')
</head>


<body>
  {{-- Way 1: Create a 'header' using a layout --}}
  @include('layouts.partials.header')

  <main>
    @yield('content')
  </main>

  {{-- Way 2: Create a 'footer' using a component --}}
  <x-footer />
</body>


</html>