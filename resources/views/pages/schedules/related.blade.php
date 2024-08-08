<details>
  <summary>
    <small>@lang('event.related_schedules', ['count' => $relatedSchedules->count()])</small>
  </summary>
  @foreach($relatedSchedules as $schedule)
    <a href="{{ route('schedule.view', ['slug' => $schedule->slug]) }}">{{ $schedule->title }}</a>
    <div><small>{{ $schedule->full_location }}</small></div>
    <div>
      <small>
        @foreach($schedule->categories as $category)
          <mark>{{ $category }}</mark>
        @endforeach
      </small>
    </div>

    @if (!$loop->last)
      <hr/>
    @endif
  @endforeach
</details>