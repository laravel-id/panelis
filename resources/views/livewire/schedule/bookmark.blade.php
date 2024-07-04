<span>
  @if ($bookmarked)
    <button wire:click="unmark"><i class="ri-bookmark-fill"></i></button>
  @else
    <button @guest disabled @endguest wire:click="mark" class="outline" data-tooltip="@lang('schedule.tip_bookmark')"><i class="ri-bookmark-fill"></i></button>
  @endif
</span>
