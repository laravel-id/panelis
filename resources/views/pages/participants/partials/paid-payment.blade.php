<article>
	<p>Halo, <strong>{{ $participant->name }}</strong>!</p>
	<p>
		Terima kasih telah melakukan pembayaraan. Saat ini status kamu <strong>sudah terdaftar sebagai peserta</strong>
		<a href="{{ route('schedule.view', $participant->schedule->slug) }}">{{ $participant->schedule->title }}.</a>
	</p>

	<p>Berikut adalah informasi dan data kamu terkait acara:</p>

	<article>
		<table class="overflow-auto">
			<tbody>
			<tr>
				<td>Nomor BIB</td>
				<td><strong>{{ $participant->bib }}</strong></td>
			</tr>
			<tr>
				<td>Nama lengkap</td>
				<td>{{ $participant->name }}</td>
			</tr>
			<tr>
				<td>@lang('event.schedule_category')</td>
				<td>{{ $participant->package->title }}</td>
			</tr>
			</tbody>
		</table>
	</article>

	<p>Mohon tunjukkan halaman ini pada saat pengambilan <em>racepack</em>.</p>

	<p>Salam sehat selalu, sampai berjumpa di venue!</p>
</article>