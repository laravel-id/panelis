@extends('layouts.app')

@push('metadata')
  <meta name="robots" content="noindex,nofollow" />
@endpush

@section('content')
  <article>
    @lang('subscriber.message_subscribed')
  </article>
@endsection