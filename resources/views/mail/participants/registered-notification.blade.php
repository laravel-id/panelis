<x-mail::message>
# @lang('Halo!')

@lang('Peserta atas nama **:name** (**:bib**) baru saja mendaftar pada [:event](:url).', [
    'name' => $participant->name,
    'bib' => $participant->bib,
    'event' => $schedule->title,
    'url' => route('schedule.view', $schedule->slug),
])

@lang('Segera kirim pesan di bawah ke nomor yang terdaftar:') <br/>

[wa.me/{{ $participant->phone }}](https://wa.me/{{ $participant->whatsapp }})

<x-mail::panel>
	@lang('Halo **:name**!', ['name' => $participant->name]) <br/>
	@lang('Kamu telah terdaftar sebagai calon peserta di :event. Segera lakukan pembayaran dengan mengikuti instruksi pada tautan di bawah.', [
    'event' => $schedule->title,
	])<br/>

	{{ route('participant.status', $participant->ulid) }}

	@lang('Abaikan pesan ini jika kamu sudah melakukan pembayaran.')

	@lang('Terima kasih'),
	{{ config('app.name') }}
</x-mail::panel>

<x-mail::button :url="route('participant.status', $participant->ulid)">
@lang('Lihat status peserta')
</x-mail::button>

@lang('Salam'),<br>
{{ config('app.name') }}
</x-mail::message>
