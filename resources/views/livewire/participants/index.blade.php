@php use App\Enums\Participants\Status; @endphp
<div>
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li><a href="{{ route('schedule.view', $schedule->slug) }}">{{ $schedule->title }}</a></li>
			<li>@lang('event.schedule_participants')</li>
		</ul>
	</nav>

	<hgroup>
		<h2>@lang('event.schedule_participants')</h2>
		<p>{{ $schedule->title }}</p>
	</hgroup>

	<article>
		<input type="search" wire:model.live="keyword" placeholder="@lang('event.participant_search_placeholder')">

		@if ($schedule->participants->isEmpty())
			<article>@lang('event.schedule_no_participants')</article>
		@endif

		@if (!$schedule->participants->isEmpty())
			<div class="overflow-auto">
				<table>
					<thead>
					<tr>

						<th>@lang('event.participant_bib')</th>
						<th>@lang('event.participant_name')</th>
						<th>@lang('event.participant_gender')</th>
						<th>@lang('event.participant_status')</th>
						<td>#</td>
					</tr>
					</thead>

					<tbody>
					@foreach($schedule->participants as $participant)
						<tr wire:key="{{ $participant->ulid }}">
							<td><strong>{{ $participant->bib }}</strong></td>
							<td><a href="javascript:void(0)"
										 wire:click="view('{{ $participant->ulid }}')">{{ $participant->name }}</a></td>
							<td>{{ $participant->gender->label() }}</td>
							<td>
								<ins>{{ $participant->status->label() }}</ins>
							</td>
							<td>
								<button
									@if ($participant->status !== Status::Paid) disabled @endif
									wire:click="complete('{{ $participant->ulid }}')"
									wire:confirm="@lang('event.confirm_participant_complete')"
									data-tooltip="@lang('event.tooltip_participant_complete')"
								>
									<i class="ri-check-double-line"></i>
								</button>
							</td>
						</tr>
					@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</article>

	@if (!empty($selectedParticipant))
		<dialog {{ $openDialog ? 'open' : 'close' }}>
			<article>
				<header>
					<button aria-label="Close" rel="prev" wire:click="close"></button>
					<p>
						<strong>@lang('event.participant_detail')</strong>
					</p>
				</header>
				<div class="overflow-auto">
					<table>
						<tbody>
						<tr>
							<td>@lang('event.participant_status')</td>
							<td>
								<strong>
									<ins>{{ $selectedParticipant->status->label() }}</ins>
								</strong>
							</td>
						</tr>
						<tr>
							<td>@lang('event.participant_id_type')</td>
							<td>
								<strong>{{ $selectedParticipant->id_type->label() }}</strong>
							</td>
						</tr>
						<tr>
							<td>@lang('event.participant_id_number')</td>
							<td>
								<strong>{{ $selectedParticipant->id_number }}</strong>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<strong>@lang('event.participant_data')</strong>
							</td>
						</tr>
						<tr>
							<td>@lang('event.participant_bib')</td>
							<td><strong>{{ $selectedParticipant->bib }}</strong></td>
						</tr>
						<tr>
							<td>@lang('event.participant_name')</td>
							<td><strong>{{ $selectedParticipant->name }}</strong></td>
						</tr>
						<tr>
							<td>@lang('event.participant_birthdate')</td>
							<td><strong>{{ $selectedParticipant->birthdate->translatedFormat('d F Y') }}</strong></td>
						</tr>
						<tr>
							<td>@lang('event.participant_gender')</td>
							<td><strong>{{ $selectedParticipant->gender->label() }}</strong></td>
						</tr>
						<tr>
							<td>@lang('event.participant_blood_type')</td>
							<td><strong>{{ $selectedParticipant->blood_type->label() }}</strong></td>
						</tr>
						<tr>
							<td>@lang('event.participant_phone')</td>
							<td>
								<strong>
									<a href="https://wa.me/{{ $selectedParticipant->phone }}" target="_blank">{{ $selectedParticipant->phone }}</a>
								</strong>
							</td>
						</tr>
						<tr>
							<td>@lang('event.participant_email')</td>
							<td><strong>{{ $selectedParticipant->email }}</strong></td>
						</tr>

						<tr>
							<td colspan="2"><strong>@lang('event.participant_emergency_contact')</strong></td>
						</tr>
						<tr>
							<td>@lang('event.participant_emergency_name')</td>
							<td>
								<strong>{{ $selectedParticipant->emergency_name }}</strong>
							</td>
						</tr>
						<tr>
							<td>@lang('event.participant_emergency_relation')</td>
							<td>
								<strong>{{ $selectedParticipant->emergency_relation->label() }}</strong>
							</td>
						</tr>
						<tr>
							<td>@lang('event.participant_emergency_phone')</td>
							<td>
								<strong>
									<a href="http://wa.me/{{ $selectedParticipant->emergency_phone }}" target="_blank">{{ $selectedParticipant->emergency_phone }}</a>
								</strong>
							</td>
						</tr>

						</tbody>
					</table>
				</div>

				<footer>
					<div role="group">
						<button
							@if ($selectedParticipant->status !== Status::Paid) disabled @endif
							wire:click="complete('{{ $selectedParticipant->ulid }}')"
							wire:confirm="@lang('event.confirm_participant_complete')"
						>@lang('event.btn_participant_complete')</button>
						<button class="outline" wire:click="close">@lang('event.btn_participant_close')</button>
					</div>
				</footer>
			</article>
		</dialog>
	@endif
</div>
