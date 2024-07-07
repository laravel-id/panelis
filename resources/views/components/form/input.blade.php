<label>
  @if(!empty($label))
    @lang($label)
    {{ $attributes->has('required') ? '*' : '' }}
  @endif

  <input
          {!! $attributes->has('placeholder') ? sprintf('placeholder="%s"', __($attributes['placeholder'])) : '' !!}
          {{ $attributes->except(['placeholder'])->merge(['type' => 'text', 'value' => old($attributes['name'])]) }}
          @error($attributes['name']) aria-invalid="true" @enderror
  >

  @if(!empty($helperText))
    <small>@lang($helperText)</small>
  @endif

  @if($displayError AND $errors->has($attributes['name']))
    <small>{{ $errors->first($attributes['name']) }}</small>
  @endif
</label>