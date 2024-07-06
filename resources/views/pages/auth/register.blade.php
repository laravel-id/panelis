@extends('layouts.app')

@section('content')
  <form method="post" action="{{ route('register') }}">
    @csrf

    <article>
      <header>
        @lang('user.register')
      </header>

      <label>
        @lang('user.name')
        <input type="text" name="name" value="{{ old('name') }}" @error('name') aria-invalid="true" @enderror>
        @error('name')
          <small>{{ $message }}</small>
        @enderror
      </label>

      <label>
        @lang('user.email')
        <input name="email" type="email" value="{{ old('email') }}" @error('password') aria-invalid="true" @enderror>
        @error('email')
          <small>{{ $message }}</small>
        @enderror
      </label>

      <label>
        @lang('user.password')
        <input type="password" name="password" @error('password') aria-invalid="true" @enderror>
        @error('password')
          <small>{{ $message }}</small>
        @enderror
      </label>

      <label>
        @lang('user.password_confirmation')
        <input type="password" name="password_confirmation">
        @error('password_confirmation')
          <small>{{ $message }}</small>
        @enderror
      </label>

      <p>
        <i class="ri-lock-2-fill"></i>
        <a href="{{ route('login') }}">
          @lang('user.login')
        </a>
      </p>

      <footer>
        <button type="submit">@lang('user.btn_login')</button>
      </footer>
    </article>
  </form>
@endsection