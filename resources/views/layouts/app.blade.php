<!doctype html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ config('color.mode', 'light') }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @stack('metadata')

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.{{ get_color_theme() }}.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.colors.min.css" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('js')

  <title>{{ $title ?? config('app.name') }}</title>
</head>
<body>
<main class="container">
  <nav>
    <ul>
      <li>
        <strong class="pico-color-{{ get_color_theme() }}">
          <i class="ri-run-fill"></i>
          {{ config('app.name') }}
        </strong>
      </li>
    </ul>
    <ul>
      <li><a href="{{ route('schedule.archive') }}">@lang('event.schedule_archive')</a></li>
      <li><a href="{{ route('message.form') }}">@lang('message.contact')</a></li>
    </ul>
  </nav>

  @yield('content')
</main>
</body>
</html>