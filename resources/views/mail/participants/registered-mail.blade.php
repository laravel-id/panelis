<x-mail::message>
# Halo, {{ $participant->name }}!

Terima kasih telah mendaftar di [{{ $schedule->title }}]({{ route('schedule.view', $schedule->slug) }}). Kamu telah terdaftar sebagai calon peserta dengan nomor BIB **{{ $participant->bib }}**.

Saat ini status pembayaran kamu tertunda. Segera lakukan pembayaran dengan mentransfer sesuai nominal ke rekening tujuan yang tertera di bawah.

<x-mail::table>
|        |          |
| ------------- |-------------:|
| Nominal     | **{{ Number::money($participant->payment?->total ?? 0) }}**      |
| Bank tujuan | Bank Central Asia |
| Nomor rekening      | 12847576 |
</x-mail::table>

Abaikan pesan ini jika kamu sudah melakukan pembayaran.

<x-mail::button :url="route('participant.status', $participant->ulid)">
Lihat status saya
</x-mail::button>

Salam sehat selalu, sampai berjumpa di venue!<br>
{{ config('app.name') }}
</x-mail::message>
