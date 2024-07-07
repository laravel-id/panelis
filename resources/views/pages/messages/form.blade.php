@extends('layouts.app')

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
      <li>{{ $title }}</li>
    </ul>
  </nav>

  <x-alert :message="session('success')" />

  <article>
    <header>{{ $title }}</header>

    <form method="post" action="{{ route('message.submit') }}">
      @csrf

      <fieldset>
        <div class="grid">
          <x-form.input label="message.name" name="name" placeholder="message.placeholder_name" required />

          <x-form.input label="message.email" name="email" type="email" placeholder="message.placeholder_email" helper-text="message.provide_email_if_want_reply" />
        </div>

        <x-form.input label="message.subject" name="subject" />

        <x-form.textarea label="message.body" name="body" />

        <footer>
          <x-form.button label="message.button_submit" />
        </footer>
      </fieldset>
    </form>
  </article>
@endsection