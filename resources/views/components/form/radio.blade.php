<fieldset>
  <legend>@lang('subscriber.period')</legend>
  @foreach ($options as $value => $label)
    <label>
      <input
        {{ $attributes->merge(['value' => $value, 'type' => 'radio']) }}
        @checked($attributes['name'])
        @error($attributes['name']) aria-invalid="true" @enderror
      />
      {{ $label }}
    </label>
  @endforeach

  @if(!empty($helperText))
    <small>@lang($helperText)</small>
  @endif

  @if($displayError AND $errors->has($attributes['name']))
    <small>{{ $errors->first($attributes['name']) }}</small>
  @endif
</fieldset>