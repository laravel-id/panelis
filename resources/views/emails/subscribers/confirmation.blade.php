<x-mail::message>
  @lang('subscriber.mail_subscribe_confirmation', [
      'email' => $subscriber->email,
      'period' => $subscriber->period->label(),
  ])

  <x-mail::button :url="$url">
    @lang('subscriber.btn_confirm')
  </x-mail::button>

  <x-mail::panel>
    @lang('subscriber.mail_ignore_confirmation')
  </x-mail::panel>
</x-mail::message>
