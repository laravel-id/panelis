@extends('layouts.app')

@section('content')
		<nav aria-label="breadcrumb">
			<ul>
				<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
				<li>{{ $title }}</li>
			</ul>
		</nav>

	<form method="post" action="{{ route('password.reset', $token) }}">
		@csrf
		<input type="hidden" name="token" value="{{ $token }}">

		<article>
			<x-form.input label="user.email" :value="request('email')" name="email" type="email" readonly />

			<x-form.input label="user.password" name="password" type="password" required/>

			<x-form.input label="user.password_confirmation" name="password_confirmation" type="password" required/>

			<footer>
				<x-form.button label="user.btn_request_password" />
			</footer>
		</article>
	</form>
@endsection