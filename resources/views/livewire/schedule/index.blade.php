<div>
  <form>
    <fieldset role="group">
      <input name="keyword" wire:model.live.debounce.100ms="keyword" value="{{ request('keyword') }}" id="search-input" type="text" placeholder="@lang('event.schedule_placeholder_search')" />
      @if (!empty($keyword))
        <a href="#" x-on:click.prevent="$wire.keyword = ''; $wire.$refresh()" type="reset" class="outline secondary"><i class="ri-close-line"></i></a>
      @endif
    </fieldset>

    @if (empty($date))
      <label>
        <input name="virtual" wire:model.live="virtual" type="checkbox" role="switch" @checked(request('virtual')) />
        @lang('event.schedule_with_virtual')
      </label>
      <label>
        <input name="past" wire:model.live="past" type="checkbox" role="switch" @checked(request('past')) />
        @lang('event.schedule_with_past')
      </label>
    @endif
  </form>
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
          <th scope="col">@lang('event.schedule_category')</th>
        </tr>
        </thead>
        <tbody>
        @foreach($schedules as $schedule)
          <tr>
            <td>
              <div class="pico-color-{{ config('color.theme', 'zinc') }}-600">{{ $schedule->started_at->timezone($timezone)->translatedFormat('d') }}</div>
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
            </td>
            <td>{{ implode(', ', $schedule->categories) }}</td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
  @endif
</div>
