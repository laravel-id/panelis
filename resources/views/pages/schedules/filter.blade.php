@extends('layouts.app')

@push('metadata')
	@php
		$title = $pageTitle ?? $title ?? config('app.name');
		$description = $description ?? config('app.description');
	@endphp

	<meta name="description" content="{{ $description }}">

	<link rel="canonical" href="{{ url()->current() }}">

	<meta property="og:url" content="{{ url()->current() }}">
	<meta property="og:type" content="website">
	<meta property="og:title" content="{{ $title }}">
	<meta property="og:description" content="{{ $description }}">

	<meta name="twitter:card" content="summary_large_image">
	<meta property="twitter:domain" content="schedules.run">
	<meta property="twitter:url" content="{{ url()->current() }}">
	<meta name="twitter:title" content="{{ $title }}">
	<meta name="twitter:description" content="{{ $description }}">
@endpush

@section('content')
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li>{{ $title }}</li>
		</ul>
	</nav>

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
							<div class="pico-color-{{ $schedule->is_past ? 'grey' : get_color_theme() }}-600">{{ $schedule->started_at->timezone($timezone)->translatedFormat('d') }}</div>
							{{ $schedule->started_at->timezone($timezone)->format('M') }}<sup>{{ $schedule->started_at->timezone($timezone)->format('y') }}</sup>
						</td>
						<td>
							<a href="{{ route('schedule.view', ['slug' => $schedule->slug]) }}"
							   class="{{ $schedule->is_past ? 'secondary' : 'primary' }}">
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
@endsection
