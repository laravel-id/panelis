@extends('layouts.app')

@push('metadata')
	<meta name="robots" content="noindex">
	<meta name="googlebot" content="noindex">
@endpush

@section('content')
	<article>
		<table>
			<tr>
				<td>Acara</td>
				<td><a href="{{ route('schedule.view', $participant->schedule->slug) }}">{{ $participant->schedule->title }}</a></td>
			</tr>
			<tr>
				<td>Status</td>
				<td>{{ $participant->status->label() }}</td>
			</tr>
			<tr>
				<td>BIB</td>
				<td><strong>{{ $participant->bib }}</strong></td>
			</tr>
			<tr>
				<td>Nama lengkap</td>
				<td>{{ $participant->name }}</td>
			</tr>
			<tr>
				<td>Jenis kelamin</td>
				<td>{{ $participant->gender->label() }}</td>
			</tr>
			<tr>
				<td>Tanggal lahir</td>
				<td>{{ $participant->birthdate->translatedFormat('d/m/Y') }}</td>
			</tr>
			@if (!empty($participant->blood_type))
			<tr>
				<td>Golongan darah</td>
				<td>{{ $participant->blood_type->label() }}</td>
			</tr>
			@endif
			<tr>
				<td>No. handphone</td>
				<td>{{ $participant->phone }}</td>
			</tr>
			<tr>
				<td>Alamat email</td>
				<td>{{ $participant->email }}</td>
			</tr>
			<tr>
				<td>Nama kontak darurat</td>
				<td>{{ $participant->emergency_name }}</td>
			</tr>
			<tr>
				<td>No. HP kontak darurat</td>
				<td>{{ $participant->emergency_phone }}</td>
			</tr>
			<tr>
				<td>Hubungan dengan kontak darurat</td>
				<td>{{ $participant->emergency_relation->label() }}</td>
			</tr>
		</table>
	</article>
@endsection