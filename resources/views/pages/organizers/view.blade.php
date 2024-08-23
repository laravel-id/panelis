@extends('layouts.app')

@push('metadata')
  @php
    $description = Str::limit($organizer->description, 160);
  @endphp

  <meta name="description" content="{{ $description }}">

  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $organizer->name }}">
  <meta property="og:description" content="{{ $description }}">

  <meta name="twitter:card" content="summary_large_image">
  <meta property="twitter:domain" content="schedules.run">
  <meta property="twitter:url" content="{{ url()->current() }}">
  <meta name="twitter:title" content="{{ $organizer->name }}">
  <meta name="twitter:description" content="{{ $description }}">
@endpush

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
      <li>@lang('event.organizer')</li>
    </ul>
  </nav>

  <article>
    <header>{{ $organizer->name }}</header>

    @if(!empty($organizer->description))
      <p>{!! Str::markdown($organizer->description) !!}</p>
    @endif

    @if(!empty($organizer->address))
      <hr/>
      <small>@lang('event.organizer_info'):</small>
      <p>{!! nl2br($organizer->address) !!}</p>
    @endif

    @if(!empty($organizer->phone))
      <span><i class="ri-phone-line"></i> {{ $organizer->phone }}</span><br>
    @endif

    @if(!empty($organizer->email))
      <span><i class="ri-mail-send-line"></i> {{ $organizer->email }}</span><br>
    @endif

    @if(!empty($organizer->website))
      <span><i class="ri-links-line"></i> <a href="{{ $organizer->website }}">{{ $organizer->website }}</a></span>
    @endif
  </article>

  <article>
    <header>@lang('event.schedule_by_organizer', ['name' => $organizer->name])</header>

    <div class="overflow-auto">
      <table>
        <thead>
        <tr>
          <th scope="col">@lang('event.schedule_date')</th>
          <th scope="col">@lang('event.schedule_title')</th>
          <th scope="col" class="large-screen">@lang('event.schedule_categories')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($schedules as $schedule)
          <tr>
            <td>
              <div class="pico-color-{{ $schedule->is_past ? 'grey' : get_color_theme() }}-600">{{ $schedule->started_at->timezone($timezone)->translatedFormat('d') }}</div>
              {{ $schedule->started_at->timezone($timezone)->format('M') }}<sup>{{ $schedule->started_at->timezone($timezone)->format('y') }}</sup>
            </td>
            <td>
              <a href="{{ route('schedule.view', ['slug' => $schedule->slug]) }}" class="{{ $schedule->is_past ? 'secondary' : 'primary' }}">
                {{ $schedule->title }}
              </a>

              <br/>
              <small>
                @if ($schedule->is_virtual OR empty($schedule->full_location))
                  <i class="ri-earth-line"></i>
                @else
                  {!! $schedule->full_location !!}
                @endif
              </small>

              <br/>
              <small class="small-screen">
                @foreach($schedule->categories as $category)
                  <mark>{{ $category }}</mark>
                @endforeach
              </small>
            </td>
            <td class="large-screen">
              @foreach($schedule->categories as $category)
                <mark>{{ $category }}</mark>
              @endforeach
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </article>
@endsection
