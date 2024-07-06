@extends('layouts.app')

@section('content')
  <form method="post" action="{{ route('login') }}">
    @csrf

    <article>
      <header>
        @lang('user.login')
      </header>

      <label>
        @lang('user.email')
        <input name="email" value="{{ old('email') }}" @error('password') aria-invalid="true" @enderror>
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
        <input type="checkbox" name="remember" value="1" @checked('remember') />
        @lang('user.remember_login')
      </label>

      <p>
        <a href="">@lang('user.forgot_password')</a>
      </p>

      <footer>
        <button type="submit">@lang('user.btn_login')</button>
      </footer>
    </article>
  </form>
@endsection