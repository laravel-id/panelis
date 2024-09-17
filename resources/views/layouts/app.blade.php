<!doctype html>
<html lang="{{ app()->getLocale() }}" data-theme="{{ config('color.mode', 'light') }}">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light dark" />
  <meta name="csrf_token" content="{{ csrf_token() }}">

  @stack('metadata')

  <link rel="stylesheet" href="{{ asset('css/pico.'.get_color_theme().'.min.css') }}"/>
  <link rel="stylesheet" href="{{ asset('css/pico.colors.min.css') }}" />
	<title>{{ $pageTitle ?? $title ?? config('app.name') }}</title>

	@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<main class="container">
	<nav>
		<ul>
			<li><strong>
					<a href="{{ route('index') }}">{{ config('app.name') }}</a>
				</strong></li>
		</ul>
		<ul class="navigation">
			@guest
				<li>
					<details class="dropdown">
						<summary>
							@lang('navigation.menu')
						</summary>
						<ul class="rtl">
							<li><a href="{{ route('login') }}">@lang('user.login')</a></li>
							<li><a href="{{ route('register') }}">@lang('user.register')</a></li>
							<li><a href="{{ route('password.request') }}">@lang('user.forgot_password')</a></li>
							<li class="divider"></li>
							<li><a href="{{ route('schedule.archive') }}">@lang('event.schedule_archive')</a></li>
							<li><a href="{{ route('message.form') }}">@lang('message.contact')</a></li>
						</ul>
					</details>
				</li>
			@endguest

			@auth
			<li>
				<details class="dropdown">
					<summary>
						@lang('user.account')
					</summary>
					<ul dir="rtl">
						<li><a href="{{ route('user.profile') }}">@lang('user.profile')</a></li>
						<li><a href="{{ route('user.setting') }}">@lang('user.setting')</a></li>
						<li><a href="{{ route('logout') }}">@lang('user.logout')</a></li>
						<li class="divider"></li>
						<li><a href="{{ route('schedule.archive') }}">@lang('event.schedule_archive')</a></li>
						<li><a href="{{ route('message.form') }}">@lang('message.contact')</a></li>
					</ul>
				</details>
			</li>
			@endauth
		</ul>
	</nav>

  @yield('content')
</main>
<!-- 100% privacy-first analytics -->
<script async defer src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
<noscript><img src="https://queue.simpleanalyticscdn.com/noscript.gif" alt="" referrerpolicy="no-referrer-when-downgrade" /></noscript>
@stack('js')
</body>
</html>