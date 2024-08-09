<!doctype html>
<html lang="en">
<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="color-scheme" content="light dark">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
		<title>{{ $title ?? config('app.name') }}</title>
</head>
<body>
<main class="container">
		<nav>
				<ul>
						<li><h1>{{ config('app.name') }}</h1></li>
				</ul>
				<ul>
						<li><a href="{{ config('app.demo_url') }}">@lang('navigation.demo')</a></li>
						<li><a href="{{ config('app.repository_url') }}">@lang('navigation.repository')</a></li>
				</ul>
		</nav>

		@yield('content')
</main>
</body>
</html>