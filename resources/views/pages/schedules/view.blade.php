@php use App\Filament\Resources\Event\ScheduleResource\Pages\EditSchedule; @endphp
@extends('layouts.app')

@push('metadata')
  @php
    $description = Str::limit($schedule->description, 160);
  @endphp

  <link rel="canonical" href="{{ url()->current() }}" />

  <meta name="description" content="{{ $description }}">

  <meta property="og:url" content="{{ url()->current() }}">
  <meta property="og:type" content="website">
  <meta property="og:title" content="{{ $title }}">
  <meta property="og:description" content="{{ $description }}">
  <meta property="og:image" content="{{ $schedule->opengraph_image }}">

  <meta name="twitter:card" content="summary_large_image">
  <meta property="twitter:domain" content="schedules.run">
  <meta property="twitter:url" content="{{ url()->current() }}">
  <meta name="twitter:title" content="{{ $title }}">
  <meta name="twitter:description" content="{{ $description }}">
  <meta name="twitter:image" content="{{ $schedule->opengraph_image }}">
@endpush

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">Home</a></li>
      <li>
        <a href="{{ route('schedule.filter', ['year' => $startedAt->format('Y')]) }}">{{ $startedAt->format('Y') }}</a>
      </li>
      <li>
        <a href="{{ route('schedule.filter', ['year' => $startedAt->format('Y'), 'month' => $startedAt->format('m')]) }}">
          {{ $startedAt->translatedFormat('F') }}
        </a>
      </li>
    </ul>
  </nav>

  <hgroup>
    <h2 class="pico-color-{{ get_color_theme() }}-700">{{ $schedule->title }}</h2>
  </hgroup>

  <article>
    @if (!empty($schedule->description))
      {!! Str::markdown($schedule->description) !!}
      <hr/>
    @endif

    @if(!empty($organizers))
      <div><small><i class="ri-community-line"></i> @lang('event.organizer'):</small></div>
      <p>{!! $organizers !!}</p>
      <hr/>
    @endif

    @if(!empty($schedule->types))
      <div><small><i class="ri-run-line"></i> @lang('event.type'):</small></div>
      <p>{{ $schedule->types->implode('title', ', ') }}</p>
      <hr/>
    @endif

    <div><small><i class="ri-node-tree"></i> @lang('event.categories'):</small></div>
    <small>
      @foreach($schedule->categories as $category)
        <mark>{{ $category }}</mark>
      @endforeach
    </small>
    <hr/>

    <div><small><i class="ri-calendar-2-line"></i> @lang('event.schedule_datetime'):</small></div>
    <p>{{ $schedule->held_at }}</p>
    <div class="overflow-auto">
      @include('pages.schedules.related', compact('relatedSchedules'))
    </div>

    @if(!$schedule->is_virtual)
      <hr/>
      <div><small><i class="ri-map-pin-line"></i> @lang('event.schedule_location'):</small></div>
      <p>
        @if (!empty($schedule->metadata['location_url']))
          <a href="{{ $schedule->metadata['location_url'] }}">{{ $schedule->full_location }}</a>
        @else
          {!! $schedule->full_location !!}
        @endif
      </p>
    @endif

    <hr/>
    <div><small><i class="ri-questionnaire-line"></i> @lang('event.schedule_info_registration'):</small></div>
    <p>
      <i class="ri-external-link-line"></i>
      @if (!$schedule->is_past)
        <a rel="nofollow" href="{{ $externalUrl }}">{{ $externalUrl }}</a>
      @else
        <del>{{ $externalUrl }}</del>
      @endif
    </p>

    @if(!empty($schedule->contacts) AND !$schedule->is_past)
      @foreach ($schedule->contacts as $contacts)
        <div>
          @if (!empty($contacts['is_wa']) && $contacts['is_wa'] === true)
            <i class="ri-whatsapp-line"></i>
          @else
            <i class="ri-phone-line"></i>
          @endif

          <span>
            @if (!empty($contacts['wa_url']))
              <a href="{{ $contacts['wa_url'] }}" target="_blank">{{ $contacts['phone'] }}</a>
            @else
              {{ $contacts['phone'] }}
            @endif
          </span>
          @if (!empty($contacts['name']))
            - {{ $contacts['name'] }}
          @endif
        </div>
      @endforeach
    @endif
    @livewire('schedule.toolbar', compact('schedule'))

    <details class="dropdown">
      <summary role="button">
        @lang('event.add_to_calendar')
      </summary>
      <ul>
        <li><a href="{{ $calendar->google() }}">@lang('event.calendar_google')</a></li>
        <li><a href="{{ $calendar->yahoo() }}">@lang('event.calendar_yahoo')</a></li>
        <li><a href="{{ $calendar->webOutlook() }}">@lang('event.calendar_outlook')</a></li>
        <li><a href="{{ $calendar->ics() }}">@lang('event.calendar_download_ics')</a></li>
      </ul>
    </details>
  </article>

  @if (!$schedule->packages->isEmpty())
    <h3>@lang('event.schedule_packages')</h3>
    @foreach($schedule->packages->chunk(3) as $chunk)
      <div class="grid" id="#packages">
        @foreach($chunk as $package)
          <article>
            <header class="pico-color-{{ get_color_theme() }}-700">
              <strong>{{ $package->title }}</strong><br/>
            </header>

            <div>
              <p>
                <i class="ri-currency-fill"></i>
                @if ($package->price <= 0)
                  <del>{{ config('app.currency_symbol') }}</del> @lang('event.package_free')
                @else
                  {{ Number::money($package->price) }}
                @endif
              </p>
              @if (!empty($package->period))
                <p><i class="ri-calendar-2-fill"></i> {{ $package->period }}</p>
              @endif

              @if(!empty($package->url))
                <p><i class="ri-links-line"></i> <a href="{{ $package->url }}">@lang('event.link_package_register')</a> </p>
              @endif

              <p>{!! Str::markdown($package->description ?? '', ['html_input' => 'strip']) !!}</p>
            </div>
          </article>
        @endforeach
      </div>
    @endforeach
  @endif
@endsection