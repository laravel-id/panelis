<x-mail::message>
# @lang('Halo, :name!', ['name' => $participant->name])

@lang('Terima kasih telah mendaftar di [:event](:url). Kamu telah terdaftar sebagai calon peserta dengan nomor BIB **:bib**.', [
	'event' => $schedule->title,
	'url' => route('schedule.view', $schedule->slug),
	'bib' => $participant->bib,
])

<br/>

@lang('Saat ini status pembayaran kamu tertunda. Segera lakukan pembayaran dengan mentransfer sesuai nominal ke rekening tujuan yang tertera di bawah.')

<x-mail::table>
|        |          |
| ------------- |-------------:|
| @lang('Nominal')     | **{{ Number::money($participant->payment?->total ?? 0) }}**      |
| @lang('Bank tujuan') | Bank Central Asia |
| @lang('Nomor rekening')      | 12847576 |
| @lang('Maksimal pembayaran') | {{ $participant->payment->expired_at->timezone(get_timezone())->translatedFormat(get_datetime_format()) }} |
</x-mail::table>

@lang('Abaikan pesan ini jika kamu sudah melakukan pembayaran.')

<x-mail::button :url="route('participant.status', $participant->ulid)">
@lang('Lihat status saya')
</x-mail::button>

@lang('Salam sehat selalu, sampai berjumpa di venue!')<br>
{{ config('app.name') }}
</x-mail::message>
