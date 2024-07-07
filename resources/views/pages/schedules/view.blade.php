@php use App\Filament\Resources\Event\ScheduleResource\Pages\EditSchedule; @endphp
@extends('layouts.app')

@push('metadata')
  @php
    $description = Str::limit($schedule->description, 160);
  @endphp

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
      <p><small>@lang('event.organizer'):</small></p>
      <p><i class="ri-community-line"></i> {!! $organizers !!}</p>
      <hr/>
    @endif

    @if(!empty($schedule->types))
      <p><small>@lang('event.type')</small></p>
      <p><i class="ri-run-line"></i> {{ $schedule->types->implode('title', ', ') }}</p>
      <hr/>
    @endif

    <p><small>@lang('event.category')</small></p>
    <p><i class="ri-node-tree"></i> {{ implode(', ', $schedule->categories) }}</p>
    <hr/>

    <p><small>@lang('event.schedule_datetime'):</small></p>
    <p><i class="ri-calendar-2-line"></i> {{ $startedAt->translatedFormat('D') }}, {{ $schedule->held_at }}</p>

    @if(!$schedule->is_virtual)
      <hr/>
      <p><small>@lang('event.schedule_location'):</small></p>
      <p><i class="ri-map-pin-line"></i>
        @if (!empty($schedule->metadata['location_url']))
          <a href="{{ $schedule->metadata['location_url'] }}">{{ $schedule->full_location }}</a>
        @else
          {!! $schedule->full_location !!}
        @endif
      </p>
    @endif

    <hr/>
    <p><small>@lang('event.schedule_info_registration'):</small></p>
    <p>
      <i class="ri-external-link-line"></i>
      @if (!$schedule->is_past)
        <a rel="nofollow" href="{{ $schedule->external_url }}">{{ $schedule->external_url }}</a>
      @else
        <del>{{ $schedule->external_url }}</del>
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
  </article>

  @if (!$schedule->packages->isEmpty())
    <article>
      <header>@lang('event.package')</header>

      <div class="overflow-auto">
        <table>
          <thead>
          <tr>
            <th scope="col">@lang('event.package_title')</th>
            <th scope="col">@lang('event.package_time')</th>
            <th scope="col">@lang('event.package_price')</th>
            <th scope="col">@lang('event.package_description')</th>
          </tr>
          </thead>

          <tbody>
          @foreach($schedule->packages as $package)
            <tr>
              <td>{{ $package->title }}</td>
              <td>
                @if (!empty($package->started_at) AND !empty($package->ended_at))
                  {{
                      vsprintf('%s - %s', [
                          $package->started_at->translatedFormat($dateFormat),
                          $package->ended_at->translatedFormat($dateFormat),
                      ])
                  }}
                @else
                  -
                @endif
              </td>
              <td>{{ $package->price > 0 ? Number::money($package->price) : '-' }}</td>
              <td>{{ $package->description ?? '-' }}</td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </article>
  @endif
@endsection