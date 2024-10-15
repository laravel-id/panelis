<x-mail::message>
  @lang('subscriber.mail_subscribe_confirmed', [
      'email' => $subscriber->email,
      'period' => $subscriber->period->label(),
  ])

  <x-mail::button :url="$url">
    @lang('subscriber.btn_unsubscribe')
  </x-mail::button>
</x-mail::message>
