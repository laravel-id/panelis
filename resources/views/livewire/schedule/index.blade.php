<div>
	<form>
		<fieldset role="group">
			<input aria-busy="true" name="keyword" wire:model.live.debounce.100ms="keyword" value="{{ request('keyword') }}"
			       id="search-input" type="text" placeholder="@lang('event.schedule_placeholder_search')"/>
			<a wire:loading.attr="aria-busy" x-show="$wire.keyword.length >= 1" href="#"
			   x-on:click.prevent="$wire.keyword = ''; $wire.$refresh()" type="reset" class="outline secondary">
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
					<tr>
						<td>
							<div class="pico-color-{{ $schedule->is_past ? 'grey' : get_color_theme()  }}-600">{{ $schedule->started_at->timezone($timezone)->translatedFormat('d') }}</div>
							{{ $schedule->started_at->timezone($timezone)->translatedFormat('M') }}<sup>{{ $schedule->started_at->timezone($timezone)->format('y') }}</sup>
						</td>
						<td>
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
