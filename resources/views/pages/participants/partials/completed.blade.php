@php use Illuminate\Support\Str; @endphp
<article>
	<p>@lang('Halo, :name!', ['name' => Str::wrap($participant->name, '<strong>', '</strong>')])</p>

	<p>@lang('Terima kasih telah berpartisipasi di :event. Sampai jumpa di lain acara!', ['event' => $schedule->title])</p>

	<p>@lang('Salam sehat selalu!')</p>
</article>