@php use Illuminate\Support\Str; @endphp
<article>
	<p>@lang('Halo, :name!', ['name' => Str::wrap($participant->name, '<strong>', '</strong>')])</p>
	<p>
		@lang('Terima kasih telah melakukan pembayaraan. Saat ini status kamu <strong>sudah terdaftar sebagai peserta</strong> di :event.', [
    	'event' => Str::wrap($participant->schedule->title, '<a href="'.route('schedule.view', $participant->schedule->slug).'">', '</a>')
		])
	</p>

	<p>@lang('Berikut adalah informasi dan data kamu terkait acara:')</p>

	<article>
		<table class="overflow-auto">
			<tbody>
			<tr>
				<td>@lang('event.schedule_category')</td>
				<td>
					<strong>{{ $participant->package->title }}</strong>
				</td>
			</tr>
			<tr>
				<td>@lang('event.participant_bib')</td>
				<td>
					<strong>{{ $participant->bib }}</strong>
				</td>
			</tr>
			<tr>
				<td>@lang('event.participant_name')</td>
				<td>
					<strong>{{ $participant->name }}</strong>
				</td>
			</tr>
			</tbody>
		</table>
	</article>

	<p>@lang('Mohon tunjukkan halaman ini pada saat pengambilan <em>racepack</em>.')</p>

	<p>
		@lang('Pengambilan <em>racepack</em> dapat dilakukan pada :time di :location.', [
			'time' => Str::wrap(data_get($participant->schedule->metadata, 'racepack_time'), '<strong>', '</strong>'),
			'location' => Str::wrap(data_get($participant->schedule->metadata, 'racepack_location'), '<a href="'.data_get($participant->schedule->metadata, 'racepack_location_url').'"><strong>', '</strong></a>'),
	])
	</p>

	<p>@lang('Salam sehat selalu, sampai berjumpa di venue!')</p>
</article>