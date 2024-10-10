@php
	use App\Enums\ThemeMode;
	use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
@endphp
<div>
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li>@lang('user.setting')</li>
		</ul>
	</nav>

	<form wire:submit="update">
		<article>
			<header>@lang('user.general_setting')</header>

			<fieldset>
				<legend>@lang('user.language_preference') *</legend>
				@foreach ($locales as $locale)
					<label>
						<input type="radio" value="{{ $locale }}" wire:model.live="language">
						{{ LanguageSwitch::make()->getLabel($locale) }}
					</label>
				@endforeach
			</fieldset>

			<fieldset>
				<legend>@lang('user.theme_mode') *</legend>
				@foreach(ThemeMode::cases() as $theme)
					<label wire:key="{{ $theme->value }}">
						<input type="radio" value="{{ $theme->value }}" wire:model.live="color">
						{{ $theme->label() }}
					</label>
				@endforeach
			</fieldset>

			<label for="timezone">
				@lang('user.timezone') *
				<select wire:model="timezone" name="timezone">
					@foreach ($timezones as $tz)
						<option wire:key="{{ $tz  }}" value="{{ $tz }}">{{ $tz }}</option>
					@endforeach
				</select>
			</label>

			<footer>
				<button type="submit">@lang('user.btn_update_setting')</button>
			</footer>
		</article>
	</form>
</div>
