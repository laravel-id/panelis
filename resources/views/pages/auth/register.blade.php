@extends('layouts.app')

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
      <li>{{ $title }}</li>
    </ul>
  </nav>

  <form method="post" action="{{ route('register') }}">
    @csrf

    <article>
      <header>
        @lang('user.register')
      </header>

      <x-form.input label="user.name" name="name" required/>

      <x-form.input label="user.email" name="email" type="email" required/>

      <x-form.input label="user.password" name="password" type="password" required/>

      <x-form.input label="user.password_confirmation" name="password_confirmation" type="password" required/>

      <p>
        <i class="ri-lock-2-fill"></i>
        <a href="{{ route('login') }}">
          @lang('user.login')
        </a>
      </p>

      <footer>
        <x-form.button label="user.btn_register" />
      </footer>
    </article>
  </form>
@endsection