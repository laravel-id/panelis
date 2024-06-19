@extends('layouts.app')

@section('content')
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
          <th scope="col">@lang('event.category')</th>
          <th scope="col">@lang('event.schedule_location')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($schedules as $schedule)
          <tr>
            <td>
              {{ $schedule->started_at->translatedFormat('d M') }}<sup>{{ $schedule->started_at->format('y') }}</sup>
            </td>
            <td>
              <a href="{{ route('schedule.view', $schedule->slug) }}">{{ $schedule->title }}</a>
            </td>
            <td>{{ implode(', ', $schedule->categories) }}</td>
            <td>{!! $schedule->full_location !!}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  </article>
@endsection