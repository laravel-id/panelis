@extends('layouts.app')

@push('js')
  @vite('resources/js/calendar.js')
@endpush

@section('content')
  <div id="calendar"></div>
@endsection