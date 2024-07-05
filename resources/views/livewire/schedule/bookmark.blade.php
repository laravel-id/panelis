<span>
  @if ($bookmarked)
    <button wire:click="unmark" data-tooltip="@lang('event.tip_unmark_schedule')"><i class="ri-bookmark-fill"></i> {{ $this->count }}</button>
  @else
    <button @guest disabled @endguest wire:click="mark" class="outline" data-tooltip="@lang('schedule.tip_mark_schedule')"><i class="ri-bookmark-fill"></i> {{ $this->count }}</button>
  @endif
</span>
