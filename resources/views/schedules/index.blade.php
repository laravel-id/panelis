@extends('layouts.app')

@push('metadata')
  @php
    $title = $title ?? config('app.name');
    $description = $description ?? config('app.description');
  @endphp

  <meta name="description" content="{{ $description }}">

  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $title }}">
  <meta property="og:description" content="{{ $description }}">

  <meta name="twitter:card" content="summary_large_image">
  <meta property="twitter:domain" content="schedules.run">
  <meta property="twitter:url" content="{{ url()->current() }}">
  <meta name="twitter:title" content="{{ $title }}">
  <meta name="twitter:description" content="{{ $description }}">
@endpush

@section('content')
  @livewire('schedule.index')
@endsection