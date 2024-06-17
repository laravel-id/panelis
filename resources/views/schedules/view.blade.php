@php use App\Filament\Resources\Event\ScheduleResource\Pages\EditSchedule; @endphp
@extends('layouts.app')

@section('content')
  <nav aria-label="breadcrumb">
    <ul>
      <li><a href="{{ route('index') }}">Home</a></li>
      <li><a href="{{ route('schedule.filter', ['year' => $year]) }}">{{ $year }}</a></li>
      <li>
        <a href="{{ route('schedule.filter', ['year' => $year, 'month' => $schedule->started_at->format('m')]) }}">
          {{ $schedule->started_at->translatedFormat('F') }}
        </a>
      </li>
    </ul>
  </nav>

  <article>
    <header>
      {{ $schedule->title }}
      @if ($schedule->is_virtual)
        <sup>@lang('event.schedule_is_virtual')</sup>
      @endif
    </header>

    @if (!empty($schedule->description))
      {!! Str::markdown($schedule->description) !!}
      <hr/>
    @endif

    @if(!empty($organizers))
      <p><small>@lang('event.organizer'):</small></p>
      <p>{!! $organizers !!}</p>
      <hr/>
    @endif

    @if(!empty($schedule->types))
      <p><small>@lang('event.type')</small></p>
      <p>{{ $schedule->types->implode('title', ', ') }}</p>
      <hr/>
    @endif

    <p><small>@lang('event.category')</small></p>
    <p>{{ implode(', ', $schedule->categories) }}</p>
    <hr/>

    <p><small>@lang('event.schedule_datetime'):</small></p>
    <p>{{ $schedule->held_at }}</p>
    <hr/>

    <p><small>@lang('event.schedule_location'):</small></p>
    <i class="ri-map-pin-line"></i>
    {!! $schedule->full_location !!}

    @if ($schedule->started_at->gt(now()))
      <hr/>
      <p><small>@lang('event.schedule_info_registration'):</small></p>
      <a href="{{ $schedule->external_url }}">{{ $schedule->url }}</a>

      @if(!empty($schedule->contacts))
        <ul>
          @foreach ($schedule->contacts as $contacts)
            <li>
              {{ $contacts['phone'] }}
              @if(!empty($contacts['name']))
                - {{ $contacts['name'] }}
              @endif
            </li>
          @endforeach
        </ul>
      @endif
    @endif
  </article>

  @if (!$schedule->packages->isEmpty())
    <article>
      <header>@lang('event.package')</header>

      <table>
        <thead>
        <tr>
          <th scope="col">@lang('event.package_title')</th>
          <th scope="col">@lang('event.package_price')</th>
          <th scope="col">@lang('event.package_description')</th>
        </tr>
        </thead>

        <tbody>
        @foreach($schedule->packages as $package)
          <tr>
            <td>{{ $package->title }}</td>
            <td>{{ $package->price > 0 ? Number::money($package->price) : '-' }}</td>
            <td>{{ $package->description ?? '-' }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </article>
  @endif

  <div>
    <a href="{{ EditSchedule::getUrl(['record' => $schedule]) }}" role="button">@lang('event.schedule_button_edit')</a>
  </div>
@endsection