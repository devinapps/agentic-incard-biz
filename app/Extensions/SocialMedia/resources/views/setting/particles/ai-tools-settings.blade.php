@php use App\Domains\Engine\Enums\EngineEnum; @endphp

<div class="col-md-12 mb-4">
	<div class="mb-3">
		@php
			$items = [
				'openai' => trans('DALL-E'),
				'gpt-image-1' => trans('GPT Image 1'),
				'stable_diffusion' => trans('Stable Diffusion'),
				'midjourney' => trans('Midjourney'),
				'flux-pro' => trans('Flux-pro'),
				'ideogram' => trans('Ideogram'),
				'flux-pro-kontext' => 'Flux-pro Kontext',
			];
		@endphp
		<div class="mb-3">
			<x-card
				class="w-full"
				size="sm"
			>
				<label class="form-label">{{ __('Social Media Image Model') }}</label>
				<select
					class="form-select"
					id="social_media_image_model"
					name="social_media_image_model"
				>
					@foreach($items as $key => $label)
						<option
							value="{{ $key }}"
							{{ setting('social_media_image_model', 'dall-e') === $key ? 'selected' : null }}
						>
							{{ $label }}
						</option>
					@endforeach
				</select>
			</x-card>
		</div>
	</div>
</div>
