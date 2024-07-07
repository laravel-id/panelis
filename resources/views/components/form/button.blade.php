<button {{ $attributes->merge(['type' => 'submit']) }}>
  @if(!empty($icon))
    <i class="{{ $icon }}"></i>
  @else
    @lang($label)
  @endif
</button>