@extends('layouts.app')

@section('content')
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li>{{ $title }}</li>
		</ul>
	</nav>

	<hgroup>
		<h2>{{ $title }}</h2>
		<p>@lang('event.past_and_upcoming_schedule')</p>
	</hgroup>

	<article>
		<div class="overflow-auto">
			<table>
				<thead>
					<tr>
						<th>@lang('event.schedule')</th>
						<th>@lang('event.schedule_started_at')</th>
						<th>@lang('ui.actions')</th>
					</tr>
				</thead>

				<tbody>
				@foreach($schedules as $schedule)
					<tr>
						<td>
							@if (!$schedule->is_past)
								<a href="{{ route('schedule.view', $schedule->slug) }}">{{ $schedule->title }}</a>
							@else
								<del>{{ $schedule->title }}</del>
							@endif
						</td>
						<td>{{ $schedule->held_at }}</td>
						<td>
							<a href="{{ route('participant.index', $schedule->slug) }}" role="button" @disabled($schedule->is_past)>
								@lang('event.organize_participant')
							</a>
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div>
	</article>
@endsection