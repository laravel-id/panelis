<article>
	<p>Halo, <strong>{{ $participant->name }}</strong>!</p>
	<p>
		Terima kasih telah mendaftar di
		<a href="{{ route('schedule.view', $participant->schedule->slug) }}" target="_blank">{{ $participant->schedule->title }}</a>.
		Kamu telah terdaftar sebagai <strong>calon peserta</strong> dengan nomor BIB <strong>{{ $participant->bib }}</strong>.
	</p>

	<p>
		Saat ini status pembayaran kamu tertunda.
		Segera lakukan pembayaran dengan mentransfer sesuai nominal ke rekening tujuan yang tertera di bawah.
	</p>

	<article>
		<table class="overflow-auto">
			<tbody>
			<tr>
				<td>Nominal</td>
				<td>{{ Number::money($participant->payment->total) }}</td>
			</tr>
			<tr>
				<td>Bank tujuan</td>
				<td>Bank Central Asia</td>
			</tr>
			<tr>
				<td>Nomor rekening</td>
				<td>11111111</td>
			</tr>
			<tr>
				<td>Maksimal pembayaran</td>
				<td>{{ $participant->payment->expired_at->timezone(get_timezone())->translatedFormat(get_datetime_format()) }}</td>
			</tr>
			</tbody>
		</table>
	</article>

	<p>
		Jika kamu mengalami kendala dengan status pembayaran, jangan sungkan untuk menghubungi narahubung di bawah.
	</p>

	<article>
		@include('pages.schedules.partials.contact', ['schedule' => $participant->schedule])
	</article>

	<p>Salam sehat selalu, sampai berjumpa di venue!</p>

</article>