<label>
  <input {{ $attributes->merge(['type' => 'checkbox', 'value' => 1]) }} @checked($attributes['name']) />
  @if(!empty($label))
    @lang($label)
  @endif
</label>