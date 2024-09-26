@php
	use App\Enums\Participants\BloodType;
	use App\Enums\Participants\Gender;
	use App\Enums\Participants\IdentityType;
	use App\Enums\Participants\Relation;use Illuminate\Support\Str;
@endphp

@push('metadata')
	<meta name="robots" content="noindex">
	<meta name="googlebot" content="noindex">
@endpush

<div>
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li><a href="{{ route('schedule.view', $schedule->slug) }}">{{ $schedule->title }}</a></li>
		</ul>
	</nav>

	<h2 class="pico-color-{{ get_color_theme() }}-400">{{ $title }}</h2>

	<form method="post" wire:submit="register">
		<article>
			<details open>
				<summary>
					<strong>@lang('event.schedule_packages')</strong>
				</summary>

				<fieldset>
					<div class="grid">
						@foreach($schedule->packages as $package)
							<article wire:key="{{ $package->id }}">
								<label>
									<input @if($package->is_sold) disabled @endif type="radio" name="package" value="{{ $package->id }}"
												 wire:model.live="package" @error('package') aria-invalid="true" @enderror>
									@if ($package->is_sold)
										<del>{{ $package->title }} - {{ Number::money($package->price) }}</del>
									@else
										{{ $package->title }} - {{ Number::money($package->price) }}
									@endif
								</label>
								@if (!empty($package->description))
									<p>{!! Str::markdown($package->description, ['html_input' => 'strip']) !!}</p>
								@endif
							</article>
					@endforeach
				</fieldset>
			</details>

			<hr/>

			<details open>
				<summary><strong>@lang('event.participant_data')</strong></summary>

				<div class="grid">
					<label>
						@lang('event.participant_id_type') *
						<select wire:model.live="idType" @error('idType') aria-invalid="true" @enderror>
							<option selected disabled value="">@lang('event.participant_select_id_type')</option>
							@foreach (IdentityType::options() as $value => $label)
								<option value="{{ $value }}">{{ $label }}</option>
							@endforeach
						</select>
						@error('idType')
						<small>{{ $message }}</small>
						@enderror
					</label>

					<label>
						@lang('event.participant_id_number') *
						<input type="text" wire:model.blur="idNumber" @error('idNumber') aria-invalid="true" @enderror>
						@error('idNumber')
						<small>{{ $message }}</small>
						@enderror
					</label>
				</div>

				<label>
					@lang('event.participant_name') *
					<input type="text" wire:model.blur="name" @error('name') aria-invalid="true" @enderror>
					@error('name')
					<small>{{ $message }}</small>
					@enderror
				</label>

				<fieldset>
					<legend>@lang('event.participant_gender') *</legend>
					@foreach(Gender::options() as $value => $label)
						<label wire:key="{{ $value }}">
							<input type="radio" value="{{ $value }}" wire:model.live="gender"
										 @error('gender') aria-invalid="true" @enderror>
							{{ $label }}
						</label>
					@endforeach
				</fieldset>

				<label>
					@lang('event.participant_birthdate') *
					<input type="date" wire:model.blur="birthdate" @error('birthdate') aria-invalid="true" @enderror>
					@error('birthdate')
					<small>{{ $message }}</small>
					@enderror
				</label>

				<fieldset>
					<legend>@lang('event.participant_blood_type') *</legend>
					@foreach(BloodType::options() as $value => $label)
						<label wire:key="{{ $value }}">
							<input type="radio" value="{{ $value }}" wire:model.live="bloodType"
										 @error('bloodType') aria-invalid="true" @enderror>
							{{ $label }}
						</label>
					@endforeach
				</fieldset>

				<div class="grid">
					<label>
						@lang('event.participant_phone') *
						<input type="tel" wire:model.blur="phone" @error('phone') aria-invalid="true" @enderror>
						@error('phone')
						<small>{{ $message }}</small>
						@enderror
					</label>

					<label>
						@lang('event.participant_email')
						<input type="email" wire:model.blur="email" @error('email') aria-invalid="true" @enderror>
						@error('email')
						<small>{{ $message }}</small>
						@enderror
					</label>
				</div>
			</details>

			<hr/>

			<details open>
				<summary><strong>@lang('event.participant_emergency_contact')</strong></summary>

				<label>
					@lang('event.participant_emergency_name') *
					<input type="text" wire:model.blur="emergencyName" @error('emergencyName') aria-invalid="true" @enderror>
					@error('emergencyName')
					<small>{{ $message }}</small>
					@enderror
				</label>

				<label>
					@lang('event.participant_emergency_phone') *
					<input type="tel" wire:model.blur="emergencyPhone" @error('emergencyPhone') aria-invalid="true" @enderror>
					@error('emergencyPhone')
					<small>{{ $message }}</small>
					@enderror
				</label>

				<label>
					@lang('event.participant_emergency_relation') *
					<select wire:model.live="emergencyRelation" @error('emergencyRelation') aria-invalid="true" @enderror>
						<option selected disabled value="">@lang('event.participant_select_emergency_relation')</option>
						@foreach (Relation::cases() as $relation)
							<option value="{{ $relation->value }}">{{ $relation->label() }}</option>
						@endforeach
					</select>
					@error('emergencyRelation')
					<small>{{ $message }}</small>
					@enderror
				</label>
			</details>

			<hr/>

			<details>
				<summary>
					<strong>@lang('event.schedule_tos')</strong>
				</summary>
				{!! Str::markdown($schedule->metadata['tos'] ?? '') !!}
			</details>

			<fieldset>
				<label>
					<input type="checkbox" wire:model.live="accepted" value="1" @error('accepted') aria-invalid="true" @enderror>
					Saya menyetujui syarat dan ketentuan yang berlaku
				</label>
			</fieldset>

			<button type="submit">@lang('event.btn_participant_register')</button>
		</article>
	</form>
</div>
