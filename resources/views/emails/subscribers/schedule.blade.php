<x-mail::message>
  @lang('subscriber.mail_subscriber_opening')

  ## @lang('subscriber.schedule_for_this_month') ##
  <x-mail::table>
    |        |          |
    | ------------- |-------------|
    @foreach($schedules['current'] as $schedule)
    | [{{ $schedule->title }}]({{ route('schedule.view', $schedule->slug) }}) | {{ $schedule->started_at->timezone(get_timezone())->format('d M') }} |
    @endforeach
  </x-mail::table>

  ## @lang('subscriber.schedule_for_next_month') ##
  <x-mail::table>
    |        |          |
    | ------------- |-------------|
    @foreach($schedules['next'] as $schedule)
      | [{{ $schedule->title }}]({{ route('schedule.view', $schedule->slug) }}) | {{ $schedule->started_at->timezone(get_timezone())->format('d M') }} |
    @endforeach
  </x-mail::table>

  @lang('subscriber.mail_subscriber_closing')

  <x-mail::button :url="route('index')">
    @lang('subscriber.btn_view_all')
  </x-mail::button>

  <x-mail::panel>
    @lang('subscriber.mail_unsubscribe', ['url' => $unsubscribeUrl])
  </x-mail::panel>
</x-mail::message>
