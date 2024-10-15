<x-mail::message>
# @lang('Halo!')

@lang('Peserta atas nama **:name** (**:bib**) telah melakukan pembayaran pada [:event](:url).', [
		'name' => $participant->name,
		'bib' => $participant->bib,
		'event' => $schedule->title,
		'url' => route('schedule.view', $schedule->slug),
])

@lang('Segera kirim pesan di bawah ke nomor yang terdaftar:') <br/>

[wa.me/{{ $participant->phone }}](https://wa.me/{{ $participant->whatsapp }})

<x-mail::panel>
	@lang('Halo, **:name**!', ['name' => $participant->name]) <br/>
	@lang('Pembayaran kamu telah dikonfirmasi. Saat ini status kamu terdaftar sebagai peserta di :event. Kamu juga dapat melihat status kepesertaan pada tautan di bawah.', [
		'event' => $schedule->title,
	])<br/>

	{{ route('participant.status', $participant->ulid) }}

	@lang('Terima kasih'),
	{{ config('app.name') }}
</x-mail::panel>

<x-mail::button :url="route('participant.status', $participant->ulid)">
	@lang('Lihat status peserta')
</x-mail::button>

@lang('Salam'),<br>
{{ config('app.name') }}
</x-mail::message>
