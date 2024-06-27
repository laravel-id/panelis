<!doctype html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ config('theme.mode', 'light') }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark" />

  @stack('metadata')

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.{{ config('color.theme', 'zinc') }}.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>

  @vite(['resources/css/app.css', 'resources/js/app.js'])

  <title>{{ $title ?? config('app.name') }}</title>
</head>
<body>
<main class="container">
  <nav>
    <ul>
      <li><a href="{{ route('index') }}"><strong>{{ config('app.name') }}</strong></a> </li>
    </ul>
    <ul>
      <li><a href="{{ route('schedule.archive') }}">@lang('event.schedule_archive')</a></li>
      <li><a href="{{ route('message.form') }}">@lang('message.contact')</a></li>
    </ul>
  </nav>

  @yield('content')
</main>

@stack('js')
</body>
</html>