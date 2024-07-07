<label>
  @if(!empty($label))
    @lang($label)
    {{ $attributes->has('required') ? '*' : '' }}
  @endif

  <textarea
    {!! $attributes->has('placeholder') ? sprintf('placeholder="%s"', __($attributes['placeholder'])) : '' !!}
    {{ $attributes->merge(['rows' => 5]) }}
    @error($attributes['name']) aria-invalid="true" @enderror
  >{{ old($attributes['name']) ?? '' }}</textarea>

  @if(!empty($helperText))
      <small>@lang($helperText)</small>
  @endif

  @if($displayError ANd $errors->has($attributes['name']))
    <small>{{ $errors->first($attributes['name']) }}</small>
  @endif
</label>