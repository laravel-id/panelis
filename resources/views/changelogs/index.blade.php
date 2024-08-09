@php use Illuminate\Support\Str; @endphp
@extends('layouts.app')

@section('content')
		<article>
				<strong>@lang('changelog.changelog')</strong>
				<hr/>

				<div class="overflow-auto">
						<table>
								<thead>
								<tr>
										<th>@lang('changelog.logged_at')</th>
										<th>@lang('changelog.types')</th>
										<th>@lang('changelog.description')</th>
								</tr>
								</thead>

								<tbody>
								@foreach($changelogs as $changelog)
										<tr>
												<td>{{ $changelog->logged_at->format(config('app.date_format', 'Y-m-d')) }}</td>
												<td>
														@foreach($changelog->types as $type)
																<mark>{{ $type }}</mark>
														@endforeach
												</td>
												<td>
														<a href="{{ $changelog->url }}">{{ $changelog->title }}</a> <br/>
														<small>{{ $changelog->description }}</small>
												</td>
										</tr>
								@endforeach
								</tbody>
						</table>
				</div>
		</article>
@endsection