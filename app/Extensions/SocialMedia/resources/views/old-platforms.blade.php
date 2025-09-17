@extends('panel.layout.settings', ['disable_tblr' => true, 'disable_titlebar' => true])
@section('title', __('Connect Social Media Accounts'))

@section('settings')
    <div class="py-10">
        <h2 class="mb-4">
            @lang('Connect Social Media Accounts')
        </h2>
        <p class="mb-8">
            @lang('Connect your social media accounts to seamlessly manage, post, and publish content across multiple platforms.')
        </p>

		<div class="flex gap-2 justify-between mb-4">

			@foreach($platforms as $platform)
				@php
					$image = 'vendor/social-media/icons/' . $platform->value . '.svg';
					$image_dark_version = 'vendor/social-media/icons/' . $platform->value . '-light.svg';
				@endphp
				<x-card
					class="group text-sm font-medium text-heading-foreground transition-all hover:scale-105 hover:shadow-xl hover:shadow-black/5"
					class:body="flex items-center justify-center"
					size="sm"
				>
					<a class="w-8 transition-all group-hover:scale-125">
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
					</a>
				</x-card>
			@endforeach
		</div>

		<h4 class="mt-4 mb-4">Connected Accounts</h4>

        <div class="space-y-5">
            @foreach ($platforms as $platform)
                @php
                    $image = 'vendor/social-media/icons/' . $platform->value . '.svg';
                    $image_dark_version = 'vendor/social-media/icons/' . $platform->value . '-light.svg';

                    $is_connected = $platform->platform()?->isConnected();

					$userPlatform = $platform->platform();
//					dd($userPlatform);
                @endphp

				@if(! $is_connected)
					@continue
				@endif
                <x-card
                    class="group text-sm font-medium text-heading-foreground transition-all hover:scale-105 hover:shadow-xl hover:shadow-black/5"
                    class:body="flex items-center gap-2.5 py-3.5"
                    size="sm"
                >
                    <figure class="w-8 transition-all group-hover:scale-125">
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

                    {{ str($platform->name)->title() }}

                    @if ($is_connected)
                        <span class="ms-auto flex items-center gap-1.5 text-[12px]">
                            <span class="inline-block size-2.5 rounded-full bg-green-500"></span>
                            @lang('Connected')
							@if($userPlatform)
								<a
									href="{{ route('dashboard.user.social-media.platforms.disconnect', $userPlatform->getKey()) }}"
									style="z-index: 1000000"
									onclick="return confirm('{{ __('Are you sure? This is permanent..') }}')"
								> <x-tabler-x/> </a>
							@endif
                        </span>
                    @endif
                    <a
                        class="absolute inset-0 z-1"
                        href="{{ url('social-media/oauth/redirect/' . $platform->value) }}"
                    ></a>
                </x-card>
            @endforeach
        </div>
    </div>
@endsection
@push('script')
	<script>
		function disconnect()
		{
			console.log('salam men burdayam');
		}
	</script>
@endpush
