<span>
  <button wire:click="toggleModal" class="outline" data-tooltip="@lang('schedule.tip_report')"><i class="ri-error-warning-fill"></i></button>

  <form wire:submit="submit">
    <dialog {{ $modal }}>
      <article>
        <header>
          <button aria-label="Close" rel="prev" wire:click.prevent="toggleModal()"></button>
          <p>
            <strong>@lang('event.report_schedule', ['title' => $schedule->title])</strong>
          </p>
        </header>

        @auth
        <fieldset>
          <label>
            <input name="anonymous" wire:model="anonymous" type="checkbox" role="switch" {{ $anonymous ? 'checked' : '' }} />
            @lang('event.report_as_anonymous')
          </label>
        </fieldset>
        @endauth

        <label>
          @lang('event.report_message')
          <textarea wire:model="message" rows="5" name="message" @error('message') aria-invalid="true" @enderror>{{ $message }}</textarea>
          @error('message')
            <small>{{ $message }}</small>
          @enderror
        </label>

        <footer>
          <button type="submit">@lang('schedule.btn_report')</button>
        </footer>
      </article>
    </dialog>
  </form>
</span>