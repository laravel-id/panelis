<article>
	<p>@lang('Halo, <strong>:name</strong>!', ['name' => $participant->name])</p>
	<p>
		@lang('Terima kasih telah mendaftar di :event. Kamu telah terdaftar sebagai <strong>calon peserta</strong> dengan nomor BIB <strong>:bib</strong>.', [
    	'event' => Str::wrap($participant->schedule->title, '<a href="'.route('schedule.view', $participant->schedule->slug).'">', '</a>'),
    	'bib' => $participant->bib,
		])
	</p>

	<p>@lang('Saat ini status pembayaran kamu tertunda. Segera lakukan pembayaran dengan mentransfer sesuai nominal ke rekening tujuan yang tertera di bawah.')</p>

	<article>
		<table class="overflow-auto">
			<tbody>
			<tr>
				<td>@lang('Nominal')</td>
				<td>
					<strong>{{ Number::money($participant->payment->total) }}</strong>
				</td>
			</tr>
			<tr>
				<td>@lang('Bank tujuan')</td>
				<td>{{ data_get($schedule->metadata, 'bank_name') }}</td>
			</tr>
			<tr>
				<td>@lang('Nomor rekening')</td>
				<td>{{ data_get($schedule->metadata, 'bank_number') }}</td>
			</tr>
			<tr>
				<td>@lang('Maksimal pembayaran')</td>
				<td>{{ $participant->payment->expired_at->timezone(get_timezone())->translatedFormat(get_datetime_format()) }}</td>
			</tr>
			</tbody>
		</table>
	</article>

	<p>@lang('Jika kamu mengalami kendala dengan status pembayaran, jangan sungkan untuk menghubungi narahubung di bawah.')</p>

	<article>
		@include('pages.schedules.partials.contact', ['schedule' => $participant->schedule])
	</article>

	<p>@lang('Salam sehat selalu, sampai berjumpa di venue!')</p>
</article>