<div>
	<form>
		<fieldset role="group">
			<input aria-busy="true" name="keyword" wire:model.live.debounce.100ms="keyword" id="search-input" type="text" placeholder="@lang('event.schedule_placeholder_search')"/>
			<a wire:loading.attr="aria-busy" x-show="$wire.keyword && $wire.keyword.length >= 1" href="#"
				 x-on:click.prevent="$wire.keyword = ''; $wire.$refresh(); $wire.dispatch('keyword-cleared')" type="reset" class="outline secondary">
				<i wire:loading.remove class="ri-close-line"></i>
			</a>
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
					<th scope="col" class="large-screen">@lang('event.schedule_categories')</th>
				</tr>
				</thead>
				<tbody>
				@foreach($schedules as $schedule)
					@php
						$localStartedAt = $schedule->started_at->timezone(get_timezone());
					@endphp
					<tr wire:key="{{ $schedule->slug }}">
						<td>
							<div class="pico-color-{{ $schedule->is_past ? 'grey' : get_color_theme()  }}-600">
								<a href="{{ route('schedule.filter', [$localStartedAt->year, $localStartedAt->month, $localStartedAt->day]) }}" class="schedule-title">
									{{ $localStartedAt->translatedFormat('d') }}
								</a>
							</div>
							{{ $localStartedAt->translatedFormat('M') }}<sup>{{ $localStartedAt->format('y') }}</sup>
						</td>
						<td>
							@if ($schedule->is_pinned && !$schedule->is_past)
								<small class="pico-color-{{ get_color_theme() }}-700"><i class="ri-pushpin-2-fill"></i> @lang('event.schedule_pinned')</small><br/>
							@endif
							<a href="{{ route('schedule.view', ['slug' => $schedule->slug]) }}"
								 class="{{ $schedule->is_past ? 'secondary' : 'primary' }} schedule-title">
								{!! $schedule->marked_title !!}
							</a>
							@if ($schedule->is_ongoing)
								<i class="ri-broadcast-fill pico-color-red-500"></i>
							@endif
							<br/>
							<small>
								@if ($schedule->is_virtual OR empty($schedule->location))
									<i class="ri-earth-line"></i>
								@else
									{!! $schedule->marked_location !!}
								@endif
							</small>
							<br/>
							<small class="small-screen">
								@foreach($schedule->categories as $category)
									<mark>{{ $category }}</mark>
								@endforeach
							</small>
						</td>
						<td class="large-screen">
							@foreach($schedule->categories as $category)
								<mark>{{ $category }}</mark>
							@endforeach
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	@endif
</div>

@push('js')
@script
<script>
    $wire.on('keyword-cleared', (event) => {
        const searchInput = document.getElementById('search-input')
				searchInput.focus()
		});
</script>
@endscript
@endpush
