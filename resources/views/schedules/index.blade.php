@extends('layouts.app')

@section('content')
  @if(!empty($search) AND $search)
  <form>
    <fieldset role="group">
      <input name="keyword" value="{{ request('keyword') }}" type="text" placeholder="@lang('event.schedule_placeholder_search')" />
      <button type="submit"><i class="ri-search-line"></i></button>
    </fieldset>
  </form>

  @if (!empty(request('keyword')))
    <a href="{{ route('index') }}" class="button" role="button">@lang('event.schedule_button_clear_filter')</a>
  @endif

  @endif
  <hr>


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
            {{ $schedule->started_at->translatedFormat('d M') }}<sup>{{ $schedule->started_at->format('y') }}</sup>
          </td>
          <td>
            <a href="{{ route('schedule.view', [$schedule->started_at->format('Y'), $schedule->slug]) }}">{{ $schedule->title }}</a>
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