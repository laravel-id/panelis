<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.{{ config('color.theme', 'zinc') }}.min.css"/>
  <link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet"/>
  <title>{{ $title ?? config('app.name') }}</title>
</head>
<body>
<main class="container">
  <nav>
    <ul>
      <li><a href="{{ route('index') }}" title="{{ config('app.description') }}"><strong>{{ config('app.name') }}</strong></a> </li>
    </ul>
    <ul>
      <li><a href="{{ route('schedule.archive') }}">@lang('event.schedule_archive')</a></li>
    </ul>
  </nav>

  @yield('content')
</main>
</body>
</html>