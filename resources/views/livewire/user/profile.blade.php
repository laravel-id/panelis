<div>
	<nav aria-label="breadcrumb">
		<ul>
			<li><a href="{{ route('index') }}">@lang('navigation.home')</a></li>
			<li>@lang('user.profile')</li>
		</ul>
	</nav>

	<hgroup>
		<h2>@lang('user.my_profile')</h2>
	</hgroup>

	<form wire:submit="update">
		<article>

			<details open>
				<summary>
					<strong>@lang('user.personal_data')</strong>
				</summary>

				<label>
					@lang('user.name') *
					<input type="text" wire:model.blur="name">
				</label>

				<label>
					@lang('user.email') *
					<input type="email" wire:model.blur="email" disabled>
					<small>@lang('user.you_cant_change_email')</small>
				</label>
			</details>

			<hr/>

			<details open>
				<summary>
					<strong>@lang('user.password')</strong>
				</summary>

				<label>
					@lang('user.new_password')
					<input type="password" wire:model.blur="password" @error('password') aria-invalid="true" @enderror>
					@error('password')
						<small>{{ $message }}</small>
					@enderror
				</label>
			</details>

			<footer>
				<button type="submit">@lang('user.btn_update_profile')</button>
			</footer>
		</article>
	</form>
</div>
