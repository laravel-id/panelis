<article>
	<p>@lang('Halo, <strong>:name</strong>!', ['name' => $participant->name])</p>
	<p>
		@lang('Terima kasih telah mendaftar di :event. Kamu telah terdaftar sebagai <strong>calon peserta</strong> dengan nomor BIB <strong>:bib</strong>.', [
    	'event' => Str::wrap($participant->schedule->title, '<a href="'.route('schedule.view', $participant->schedule->slug).'">', '</a>'),
    	'bib' => $participant->bib,
		])
	</p>

	<p>@lang('Saat ini status pembayaran kamu tertunda. Segera selesaikan transaksi dengan mengklik tombol di bawah dan mengikuti instruksi yang ada pada halaman tersebut.')</p>

	<p>
		<a href="{{ $participant->transaction->metadata['payment_url'] }}" class="full-width" role="button">@lang('Bayar sekarang')</a>
	</p>

	<p>@lang('Jika kamu mengalami kendala dengan tata cara pembayaran, jangan sungkan untuk mengirim pesan ke narahubung di bawah.')</p>

	<article>
		@include('pages.schedules.partials.contact', ['schedule' => $participant->schedule])
	</article>

	<p>@lang('Salam sehat selalu, sampai berjumpa di venue!')</p>
</article>