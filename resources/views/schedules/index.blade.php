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
  @if(!empty($search) AND $search)
  <form action="{{ url()->current() }}">
    <fieldset role="group">
      <input name="keyword" value="{{ request('keyword') }}" id="search-input" type="text" placeholder="@lang('event.schedule_placeholder_search')" />
      @if (!empty(request('keyword')))
      <a href="{{ url()->current() }}" type="reset" class="outline secondary"><i class="ri-close-line"></i></a>
      @endif
      <button type="submit"><i class="ri-search-line"></i></button>
    </fieldset>
  </form>
  @endif
  <hr>

  <div id="calendar"></div>

  @if($schedules->isEmpty())
    <article>@lang('event.schedule_not_found')</article>
  @endif

  @if (!$schedules->isEmpty())
  <div class="overflow-auto">
    <table>
      <thead>
      <tr>
        <th scope="col">@lang('event.schedule_date')</th>
        <th scope="col">@lang('event.schedule_title')</th>
        <th scope="col">@lang('event.category')</th>
        <th scope="col">@lang('event.schedule_location')</th>
      </tr>
      </thead>
      <tbody>
      @foreach($schedules as $schedule)
        <tr>
          <td>
            {{ $schedule->started_at->timezone($timezone)->translatedFormat('d M') }}<sup>{{ $schedule->started_at->timezone($timezone)->format('y') }}</sup>
          </td>
          <td>
            <a href="{{ route('schedule.view', ['slug' => $schedule->slug]) }}" class="{{ $schedule->is_past ? 'secondary' : 'primary' }}">
              {{ $schedule->title }}
            </a>
          </td>
          <td>{{ implode(', ', $schedule->categories) }}</td>
          <td>
            @if ($schedule->is_virtual OR empty($schedule->full_location))
              <i class="ri-earth-line"></i>
            @else
              {!! $schedule->full_location !!}
            @endif
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
  </div>
  @endif
@endsection