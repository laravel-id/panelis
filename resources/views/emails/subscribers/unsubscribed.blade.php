<x-mail::message>
  @lang('subscriber.mail_unsubscribed', [
      'email' => $subscriber->email,
      'period' => $subscriber->period->label(),
  ])

  <x-mail::button :url="$url">
    @lang('subscriber.btn_subscribe')
  </x-mail::button>
</x-mail::message>
