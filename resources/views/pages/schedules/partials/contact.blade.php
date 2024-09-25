@if(!empty($schedule->contacts) AND !$schedule->is_past)
	@foreach ($schedule->contacts as $contacts)
		<div>
			@if (!empty($contacts['is_wa']) && $contacts['is_wa'] === true)
				<i class="ri-whatsapp-line"></i>
			@else
				<i class="ri-phone-line"></i>
			@endif

			<span>
            @if (!empty($contacts['wa_url']))
					<a href="{{ $contacts['wa_url'] }}" target="_blank">{{ $contacts['phone'] }}</a>
				@else
					{{ $contacts['phone'] }}
				@endif
          </span>
			@if (!empty($contacts['name']))
				- {{ $contacts['name'] }}
			@endif
		</div>
	@endforeach
@endif