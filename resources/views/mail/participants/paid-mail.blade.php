<x-mail::message>
# @lang('Halo, **:name**!', ['name' => $participant->name])

@lang('Terima kasih telah melakukan pembayaraan. Saat ini status kamu **sudah terdaftar sebagai peserta** di [:event](:url).', [
    'event' => $schedule->title,
    'url' => route('schedule.view', $schedule->slug),
])

@lang('Tunjukkan email ini pada saat mengambil _racepack_ untuk mengkonfirmasi kepesertaan.')

<x-mail::table>
	|        |          |
	| ------------- |-------------:|
	| @lang('event.schedule_category')      | **{{ $participant->package->title }}**      |
	| @lang('event.participant_bib')      | **{{ $participant->bib }}**      |
	| @lang('event.participant_name')      | **{{ $participant->name }}** |
</x-mail::table>

@lang('Pengambilan _racepack_ dapat dilakukan pada **:time** di **[:location](:location_url)**.', [
    'time' => data_get($schedule->metadata, 'racepack_time'),
    'location' => data_get($schedule->metadata, 'racepack_location'),
    'location_url' => data_get($schedule->metadata, 'racepack_location_url'),
])

<x-mail::button :url="route('participant.status', $participant->ulid)">
@lang('Lihat status saya')
</x-mail::button>

@lang('Salam sehat selalu, sampai jumpa di venue!')<br>
{{ config('app.name') }}
</x-mail::message>
