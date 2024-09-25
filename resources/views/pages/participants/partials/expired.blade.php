@php use Illuminate\Support\Str; @endphp
<article>
	<p>@lang('Halo, :name!', ['name' => Str::wrap($participant->name, '<strong>', '</strong>')])</p>

	<p>
		@lang('Pembayaran kamu melebihi tenggat. Saat ini status kamu <strong>tidak terdaftar sebagai peserta</strong> di :event.', [
				'event' => Str::wrap($participant->schedule->title, '<a href="'.route('schedule.view', $participant->schedule->slug).'">', '</a>'),
		])
	</p>

	<p>@lang('Silakan mendaftar ulang pada acara yang sama dan melakukan pembayaran sesuai instruksi.')</p>

	<p>@lang('Jika kamu sudah melakukan pembayaran, tetapi status masih belum terdaftar, silakan menghubungi narahubung di bawah untuk pengecekan lebih lanjut.')</p>

	<article>
		@include('pages.schedules.partials.contact', ['schedule' => $participant->schedule])
	</article>

	<p>@lang('Salam sehat selalu!')</p>
</article>