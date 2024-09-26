<x-mail::message>
# @lang('Halo, **:name**!', ['name' => $participant->name])

@lang('Terima kasih telah mendaftar di [:event](:url). Kamu telah terdaftar sebagai calon peserta dengan nomor BIB **:bib**.', [
	'event' => $schedule->title,
	'url' => route('schedule.view', $schedule->slug),
	'bib' => $participant->bib,
])

<br/>

@lang('Saat ini status pembayaran kamu tertunda. Segera selesaikan transaksi dengan mengklik tombol di bawah dan mengikuti instruksi yang ada pada halaman tersebut.')

<x-mail::button :url="$paymentUrl">
	Lakukan pembayaran sekarang
</x-mail::button>

@lang('Abaikan pesan ini jika kamu sudah melakukan pembayaran.')

@lang('Kamu dapat melihat status kepesertaan kamu dengan mengklik tombol di bawah.')

<x-mail::button :url="route('participant.status', $participant->ulid)">
@lang('Lihat status saya')
</x-mail::button>

@lang('Salam sehat selalu, sampai berjumpa di venue!')<br>
{{ config('app.name') }}
</x-mail::message>
