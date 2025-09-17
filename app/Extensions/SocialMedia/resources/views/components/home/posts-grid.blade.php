@php
	use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
@endphp

<div class="lqd-social-media-posts-grid grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-4">
	<h3 class="col-span-2 self-center">
		@lang('Social Media Posts')
	</h3>

	<x-button
		class="col-span-2 place-self-end self-center text-2xs"
		variant="link"
		href="{{ route('dashboard.user.social-media.post.index') }}"
	>
		@lang('View All')
		<x-tabler-chevron-right class="size-4" />
	</x-button>

	@if (filled($posts))
		@foreach ($posts as $post)
			@php
				$image = 'vendor/social-media/icons/' . $post['platform']?->value . '.svg';
				$image_dark_version = 'vendor/social-media/icons/' . $post['platform']?->value . '-light.svg';
			@endphp
			<x-card
				class="lqd-social-media-post hover:scale-105 hover:shadow-lg hover:shadow-black/5"
				class:body="pt-4 px-5"
			>
				<x-slot:head
					class="flex items-center justify-between gap-3 border-none px-5 pb-0 pt-4"
				>
					<figure class="w-7">
						<img
							@class([
								'w-full h-auto',
								'dark:hidden' => file_exists($image_dark_version),
							])
							src="{{ asset($image) }}"
							alt="{{ $post['platform']?->name }}"
						/>
						@if (file_exists($image_dark_version))
							<img
								class="hidden h-auto w-full dark:block"
								src="{{ asset($image_dark_version) }}"
								alt="{{ $post['platform']?->name }}"
							/>
						@endif
					</figure>

					{{-- <x-dropdown.dropdown
					anchor="end"
					offsetY="15px"
				>
					<x-slot:trigger>
						<span class="sr-only">
							@lang('Actions')
						</span>
						<x-tabler-dots-vertical class="size-4" />
					</x-slot:trigger>

					<x-slot:dropdown
						class="p-2"
					>
						<x-button
							class="w-full justify-start rounded-md px-3 py-2 text-start text-2xs hover:bg-heading-foreground/5 hover:no-underline"
							variant="link"
						>
							<x-tabler-pencil class="size-4" />
							@lang('Edit')
						</x-button>
						<x-button
							class="w-full justify-start rounded-md px-3 py-2 text-start text-2xs hover:bg-heading-foreground/5 hover:no-underline"
							variant="link"
						>
							<x-tabler-trash class="size-4" />
							@lang('Delete')
						</x-button>
					</x-slot:dropdown>
				</x-dropdown.dropdown> --}}
				</x-slot:head>

				<div class="lqd-social-media-post-details font-medium text-heading-foreground">
					<div
						class="lqd-social-media-post-details-masked mb-3 max-h-36 overflow-hidden"
						style="mask-image: linear-gradient(to bottom, black 70%, transparent)"
					>
						@if (isset($post['image']))
							<figure class="lqd-social-media-post-fig mb-4 aspect-[1/0.5] w-full overflow-hidden rounded-lg shadow-sm">
								<img
									class="lqd-social-media-post-img h-full w-full object-cover object-center"
									src="{{ $post['image'] }}"
									alt="@lang('Social Media Post')"
								/>
							</figure>
						@endif
						@if (isset($post['content']))
							<p class="lqd-social-media-post-content text-2xs/4">
								{{ $post['content'] }}
							</p>
						@endif
					</div>
					<p class="lqd-social-media-post-date mb-2.5 text-xs opacity-70">
						{{ $post['created_at']->diffForHumans() }}
					</p>
					<div class="lqd-social-media-post-status text-[12px] leading-none">
                        <span @class([
                            'lqd-social-media-post-status-pill inline-flex items-center gap-1.5 border rounded-full px-2',
                            'text-green-500' =>
                                $post['status'] ===
                                \App\Extensions\SocialMedia\System\Enums\StatusEnum::published,
                            'text-foreground' =>
                                $post['status'] ===
                                \App\Extensions\SocialMedia\System\Enums\StatusEnum::scheduled,
                        ])>
                            @if ($post['status'] === \App\Extensions\SocialMedia\System\Enums\StatusEnum::published)
								<x-tabler-check class="w-4" />
							@elseif ($post['status'] === \App\Extensions\SocialMedia\System\Enums\StatusEnum::scheduled)
								<x-tabler-clock class="w-4" />
							@else
								<x-tabler-circle-dashed class="w-4" />
							@endif
							{{ str()->title($post['status']->value) }}
                        </span>
					</div>
				</div>

				<a
					class="absolute inset-0 z-0"
					href="{{ route('dashboard.user.social-media.post.index', ['show' => $post['id']]) }}"
				></a>
			</x-card>
		@endforeach
	@else
		<h4 class="col-span-full text-lg">
			@lang('No posts have been added yet.')
		</h4>
	@endif
</div>
