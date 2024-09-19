@extends('layouts.app')

@section('content')
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li>{{ $title }}</li>
		</ul>
	</nav>

	<form method="post" action="{{ route('password.request') }}">
		@csrf


		<article>
			<header>{{ $title }}</header>

			<x-form.input label="user.email" :value="old('email')" name="email" type="email" required />

			<footer>
				<x-form.button label="user.btn_request_password" />
			</footer>
		</article>
	</form>
@endsection