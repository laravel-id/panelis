@php use App\Filament\Resources\Event\ScheduleResource\Pages\EditSchedule;use Carbon\CarbonInterface; @endphp
@extends('layouts.app')

@push('metadata')
	@php
		$description = Str::limit($schedule->description, 160);
	@endphp

	<link rel="canonical" href="{{ url()->current() }}"/>

	<meta name="description" content="{{ $description }}">

	<meta property="og:url" content="{{ url()->current() }}">
	<meta property="og:type" content="website">
	<meta property="og:title" content="{{ $title }}">
	<meta property="og:description" content="{{ $description }}">
	<meta property="og:image" content="{{ $schedule->opengraph_image }}">

	<meta name="twitter:card" content="summary_large_image">
	<meta property="twitter:domain" content="schedules.run">
	<meta property="twitter:url" content="{{ url()->current() }}">
	<meta name="twitter:title" content="{{ $title }}">
	<meta name="twitter:description" content="{{ $description }}">
	<meta name="twitter:image" content="{{ $schedule->opengraph_image }}">
@endpush

@section('content')
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">Home</a></li>
			<li>
				<a href="{{ route('schedule.filter', ['year' => $startedAt->format('Y')]) }}">{{ $startedAt->format('Y') }}</a>
			</li>
			<li>
				<a href="{{ route('schedule.filter', ['year' => $startedAt->format('Y'), 'month' => $startedAt->format('m')]) }}">
					{{ $startedAt->translatedFormat('F') }}
				</a>
			</li>
		</ul>
	</nav>

	<hgroup>
		<h2 class="pico-color-{{ get_color_theme() }}-700 event-title">{{ $schedule->title }}</h2>
	</hgroup>
	@if (!empty($schedule->parent))
		<div class="parent-event">
			<i class="ri-corner-down-right-line"></i> <a
							href="{{ route('schedule.view', $schedule->parent->slug) }}">{{ $schedule->parent->title }}</a>
		</div>
	@endif

	<article>
		@if (!empty($schedule->description))
			{!! Str::markdown($schedule->description) !!}
			<hr/>
		@endif

		@if(!empty($organizers))
			<div><small><i class="ri-community-line"></i> @lang('event.organizer'):</small></div>
			<p>{!! $organizers !!}</p>
			<hr/>
		@endif

		@if(!empty($schedule->types))
			<div><small><i class="ri-run-line"></i> @lang('event.type'):</small></div>
			<p>{{ $schedule->types->implode('title', ', ') }}</p>
			<hr/>
		@endif

		<div><small><i class="ri-node-tree"></i> @lang('event.categories'):</small></div>
		<small>
			@foreach($schedule->categories as $category)
				<mark>{{ $category }}</mark>
			@endforeach
		</small>
		<hr/>

		<div><small><i class="ri-calendar-2-line"></i> @lang('event.schedule_datetime'):</small></div>
		<p>{{ $schedule->held_at }} ({{ $startedAt->from(now(get_timezone()), CarbonInterface::DIFF_RELATIVE_TO_NOW, false, 2) }})</p>
		<div class="overflow-auto">
			@include('pages.schedules.related', compact('relatedSchedules'))
		</div>
		from

		@if(!$schedule->is_virtual)
			<hr/>
			<div><small><i class="ri-map-pin-line"></i> @lang('event.schedule_location'):</small></div>
			<p>
				@if (!empty($schedule->metadata['location_url']))
					<a href="{{ $schedule->metadata['location_url'] }}">{{ $schedule->full_location }}</a>
				@else
					{!! $schedule->full_location !!}
				@endif
			</p>
		@endif

		<hr/>
		<div><small><i class="ri-questionnaire-line"></i> @lang('event.schedule_info_registration'):</small></div>
		<p>
			<i class="ri-external-link-line"></i>
			@if (!$schedule->is_past)
				<a rel="nofollow" href="{{ $externalUrl }}?ref=schedules.run">{{ $externalUrl }}</a>
			@else
				<del>{{ $externalUrl }}</del>
			@endif
		</p>

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
		@livewire('schedule.toolbar', compact('schedule'))

		<details class="dropdown">
			<summary role="button" {{ !$schedule->is_past ?: 'disabled' }}>
				@lang('event.add_to_calendar')
			</summary>
			<ul>
				<li><a href="{{ $calendar->google() }}">@lang('event.calendar_google')</a></li>
				<li><a href="{{ $calendar->yahoo() }}">@lang('event.calendar_yahoo')</a></li>
				<li><a href="{{ $calendar->webOutlook() }}">@lang('event.calendar_outlook')</a></li>
				<li>
					<a href="{{ $calendar->ics(['UID' => $schedule->slug, 'URL' => url()->current()]) }}">@lang('event.calendar_download_ics')</a>
				</li>
			</ul>
		</details>
	</article>

	@includeWhen(!$schedule->packages->isEmpty(), 'pages.schedules.partials.packages', ['packages' => $schedule->packages])

	<div class="elfsight-app-b25033ce-b01a-46a6-8d58-b890986a911f" data-elfsight-app-lazy></div>
@endsection

@push('js')
	<script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
@endpush