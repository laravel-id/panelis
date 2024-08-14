<h3>@lang('event.schedule_packages')</h3>
@foreach($schedule->packages->chunk(3) as $chunk)
	<div class="grid" id="#packages">
		@foreach($chunk as $package)
			<article>
				<header class="pico-color-{{ get_color_theme() }}-700">
					@if ($package->is_past OR $package->is_sold)
						<del><strong>{{ $package->title }}</strong></del>
					@else
						<strong>{{ $package->title }}</strong>
					@endif
				</header>

				<div>
					@if(!empty($package->price_type))
						<p>
							<i class="ri-pages-line"></i>
							<strong>{{ $package->price_type->label() }}</strong>
						</p>
					@endif
					<p>
						<i class="ri-currency-fill"></i>
						@if ($package->price <= 0)
							<del>{{ config('app.currency_symbol') }}</del> @lang('event.package_free')
						@else
							{{ Number::money($package->price) }}
						@endif
					</p>
					@if (!empty($package->period))
						<p><i class="ri-calendar-2-fill"></i> {{ $package->period }}</p>
					@endif

					@if(!empty($package->url))
						<p><i class="ri-links-line"></i> <a href="{{ $package->url }}">@lang('event.link_package_register')</a></p>
					@endif
						<hr/>

					<p>{!! Str::markdown($package->description ?? '', ['html_input' => 'strip']) !!}</p>
				</div>
			</article>
		@endforeach
	</div>
@endforeach