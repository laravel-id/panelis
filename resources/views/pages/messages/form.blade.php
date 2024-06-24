@extends('layouts.app')

@section('content')
  @if(session('success'))
    <article>
      {{ session('success') }}
    </article>
  @endif

  <article>
    <header>{{ $title }}</header>

    <form method="post" action="{{ route('message.submit') }}">
      @csrf

      <fieldset>
        <div class="grid">
          <label>
            @lang('message.name')*
            <input type="text" name="name" value="{{ old('value') }}" placeholder="@lang('message.placeholder_name')" @error('name') aria-invalid="true" @enderror>
          </label>

          <label>
            @lang('message.email')
            <input type="text" name="email" value="{{ old('email') }}" placeholder="@lang('message.email')" @error('email') aria-invalid="true" @enderror>
            <small>@lang('message.provide_email_if_want_reply')</small>
          </label>
        </div>

        <label>
          @lang('message.subject')
          <input type="text" name="subject" value="{{ old('subject') }}" @error('subject') aria-invalid="true" @enderror>
        </label>

        <label>
          @lang('message.body')*
          <textarea name="body" rows="5" @error('body') aria-invalid="true" @enderror>{{ old('body') }}</textarea>
        </label>

        <button type="submit">@lang('message.submit')</button>
      </fieldset>
    </form>
  </article>
@endsection