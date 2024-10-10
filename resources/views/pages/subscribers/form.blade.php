@extends('layouts.app')

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
      <li>{{ $title }}</li>
    </ul>
  </nav>

  <x-alert :message="session('success')" />
  <x-alert :message="session('error')" type="error" />

  <form method="post" action="{{ route('subscriber.submit') }}">
    @csrf

    <article>
      <header>@lang('subscriber.subscribe')</header>

      <x-form.input label="subscriber.email" name="email" type="email" required />

      <x-form.radio label="subscriber.period" name="period" :options="$periods" required />

      <footer>
        <x-form.button label="subscriber.btn_subscribe" />
      </footer>
    </article>
  </form>
@endsection