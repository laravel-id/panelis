@extends('layouts.app')

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
      <li>{{ $title }}</li>
    </ul>
  </nav>

  @if(session('success'))
    <article>
      {{ session('success') }}
    </article>
  @endif

  <x-alert :message="session('success')" />
  <x-alert :message="session('error')" type="error" />

  <form method="post" action="{{ route('subscriber.submit') }}">
    @csrf

    <article>
      <header>@lang('subscriber.subscribe')</header>

      <label>
        @lang('subscriber.email')
        <input type="email" name="email" value="{{ old('email') }}" @error('email') aria-invalid="true" @enderror required>
      </label>

      <fieldset>
        <legend>@lang('subscriber.period')</legend>
        @foreach ($periods as $value => $label)
          <label>
            <input type="radio" name="period" value="{{ $value }}" @checked('period')/>
            {{ $label }}
          </label>
        @endforeach
      </fieldset>

      <footer>
        <button type="submit">@lang('subscriber.btn_subscribe')</button>
      </footer>
    </article>
  </form>
@endsection