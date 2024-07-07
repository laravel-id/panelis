@extends('layouts.app')

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
      <li>{{ $title }}</li>
    </ul>
  </nav>

  <form method="post" action="{{ route('login') }}">
    @csrf

    <article>
      <header>
        @lang('user.login')
      </header>

      <x-form.input label="user.email" name="email" type="email" required />

      <x-form.input label="user.password" name="password" type="password" required />

      <x-form.checkbox label="user.remember_login" name="remember" />

      <p>
        <a href="">@lang('user.forgot_password')</a>
      </p>

      <footer>
        <x-form.button label="user.btn_login" />
      </footer>
    </article>
  </form>
@endsection