@php use App\Filament\Resources\Event\ScheduleResource\Pages\EditSchedule; @endphp

<div x-data="{modal: false}" @reported="modal = false">
  <hr/>

  <div role="group">
    <button @if(auth()->guest()) disabled @endif wire:click="bookmark" class="{{ $marked ? 'primary' : 'outline' }}" data-tooltip="@lang('event.tip_schedule_bookmark')"><i class="ri-bookmark-fill"></i> {{ $this->count }}</button>
    <button @if($schedule->is_past) disabled @endif x-on:click="modal = true" class="outline" data-tooltip="@lang('event.tip_schedule_report')"><i class="ri-error-warning-fill"></i></button>
    @auth
      <a href="{{ EditSchedule::getUrl(['record' => $schedule]) }}" role="button" class="outline"><i class="ri-pencil-fill"></i></a>
    @endauth
  </div>

  <form wire:submit="report">
    <dialog :open="modal">
      <article>
        <header>
          <button aria-label="Close" rel="prev" x-on:click.prevent="modal = false"></button>
          <p>
            <strong>@lang('event.report_schedule', ['title' => $schedule->title])</strong>
          </p>
        </header>

        @auth
          <fieldset>
            <label>
              <input name="anonymous" wire:model="reportForm.anonymous" type="checkbox" role="switch" {{ $reportForm->anonymous ? 'checked' : '' }} />
              @lang('event.report_as_anonymous')
            </label>
          </fieldset>
        @endauth

        <label>
          @lang('event.report_message')
          <textarea wire:model="reportForm.message" rows="5" name="message" @error('message') aria-invalid="true" @enderror>{{ $reportForm->message }}</textarea>
          @error('reportForm.message')
          <small>{{ $message }}</small>
          @enderror
        </label>

        <footer>
          <button type="submit">@lang('schedule.btn_report')</button>
        </footer>
      </article>
    </dialog>
  </form>
</div>