<div class="mt-10 lqd-social-media-cards-grid  gap-5 flex justify-between ">
	@foreach ($platforms as $platform)
		@php
			$image = 'vendor/social-media/icons/' . $platform->value . '.svg';
			$image_dark_version = 'vendor/social-media/icons/' . $platform->value . '-light.svg';

		@endphp
		<x-card
			class="lqd-social-media-card flex flex-col  text-heading-foreground transition-all hover:scale-105 hover:border-heading-foreground/10 hover:shadow-lg hover:shadow-black/5"
			class:body="flex flex-col "
		>
			<figure class="mb-8 w-9 transition-all group-hover/card:scale-125">
				<img
					@class([
						'w-full h-auto',
						'dark:hidden' => file_exists($image_dark_version),
					])
					src="{{ asset($image) }}"
					alt="{{ $platform->name }}"
				/>
				@if (file_exists($image_dark_version))
					<img
						class="hidden h-auto w-full dark:block"
						src="{{ asset($image_dark_version) }}"
						alt="{{ $platform->name }}"
					/>
				@endif
			</figure>
			<h4 class="mb-2 text-lg text-inherit">
				{{ str($platform->name)->title() }}
			</h4>
			<a
				style="z-index: 100000"
				target="_blank"
				class="relative opacity-70 "
				href="{{ route('social-media.oauth.connect.'.$platform->name) }}"
			>@lang('Add New Account')</a>

			<a
				target="_blank"
				class="absolute inset-0 z-2 inline-block"
				href="{{ route('social-media.oauth.connect.'.$platform->name) }}"
			></a>
		</x-card>
	@endforeach
</div>
