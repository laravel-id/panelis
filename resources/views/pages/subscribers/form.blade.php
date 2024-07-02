@extends('layouts.app')

@section('content')
  @if(session('success'))
    <article>
      {{ session('success') }}
    </article>
  @endif

  @if(session('error'))
    <article>
      {{ session('error') }}
    </article>
  @endif

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