@extends('panel.layout.settings')
@section('title', __('AI Social Media Suite Settings'))

@section('settings')

	<div class="row">
		@foreach($platforms as $platform)
			<form
				enctype="multipart/form-data"
				method="post"
				action="{{ route('dashboard.admin.social-media.setting.update', $platform->value) }}"
				class="bg-white border p-4 rounded mb-4">

				<div class="header border-b mb-2">
					<h4 class="text-[20px] ">{{ ucfirst($platform->name) . ' ' . __('settings') }}</h4>
				</div>
				@foreach($platform->credentials() as $key => $value)
					<div
						class="mb-3">
						<label class="form-label">{{ str($key)->upper()->replace('_', ' ') }}</label>
						<input
							class="form-control @error($key) is-invalid @enderror"
							id="{{ strtoupper($key) }}"
							type="text"
							name="{{ strtoupper($key) }}"
							value="{{ $app_is_demo ? '*********************' : old($key, $value) }}"
							required
						>
						@error($key)
						<small class="text-red-500">{{ $message }}</small>
						@enderror
					</div>
				@endforeach

				@if($platform === \App\Extensions\SocialMedia\System\Enums\PlatformEnum::tiktok)
					<div
						class="mb-3">
						<label class="form-label">{{ __(strtoupper('Verification file')) }}</label>
						<input
							class="form-control @error('tiktok_verification_file') is-invalid @enderror"
							id="{{ strtoupper('tiktok_verification_file') }}"
							type="file"
							name="{{ strtoupper('tiktok_verification_file') }}"
						>
						@error('verification_file')
						<small class="text-red-500">{{ $message }}</small>
						@enderror
					</div>
				@endif

				@if($platform === \App\Extensions\SocialMedia\System\Enums\PlatformEnum::instagram)
					<div
						class="mb-3">
						<label class="form-label">{{ __('WEBHOOK URI') }}</label>
						<input
							disabled
							class="form-control"
							type="text"
							value="{{ url('/social-media/webhook/' . $platform->value) }}"
							required
						>
					</div>
				@endif

				<div
					class="mb-3">
					<label class="form-label">{{ __('REDIRECT URI') }}</label>
					<input
						disabled
						class="form-control"
						type="text"
						value="{{ url('/social-media/oauth/callback/' . $platform->value) }}"
						required
					>
				</div>

				<button
					class="btn btn-primary w-full"
				>
					{{ __('Save') }}
				</button>
			</form>
		@endforeach
	</div>
@endsection

