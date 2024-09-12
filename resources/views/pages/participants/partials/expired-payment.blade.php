<article>
	<p>Halo, <strong>{{ $participant->name }}</strong>!</p>
	<p>
		Pembayaran kamu melebihi tenggat.
		Saat ini status kamu <strong>tidak terdaftar sebagai peserta</strong> di
		<a href="{{ route('schedule.view', $participant->schedule->slug) }}">{{ $participant->schedule->title }}</a>.
	</p>

	<p>
		Silakan mendaftar ulang pada acara yang sama dan melakukan pembayaran sesuai instruksi.
	</p>

	<p>
		Jika kamu sudah melakukan pembayaran, tetapi status masih belum terdaftar, silakan menghubungi narahubung di bawah untuk pengecekan lebih lanjut.
	</p>

	<article>
		@include('pages.schedules.partials.contact', ['schedule' => $participant->schedule])
	</article>

	<p>Salam sehat selalu!</p>
</article>