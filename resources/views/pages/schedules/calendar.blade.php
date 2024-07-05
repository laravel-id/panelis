@extends('layouts.app')

@push('js')
  @vite('resources/js/calendar.js')
@endpush

@push('metadata')
  <meta name="description" content="{{ config('app.description') }}">

  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $title }}">
  <meta property="og:description" content="{{ config('app.description') }}">
@endpush

@section('content')
  <div id="calendar"></div>
@endsection