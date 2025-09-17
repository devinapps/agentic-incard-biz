<div class="lqd-social-media-cards-grid grid grid-cols-1 gap-5 lg:grid-cols-2">
    @foreach ($platforms as $platform)
        @php
            $image = 'vendor/social-media/icons/' . $platform->value . '.svg';
            $image_dark_version = 'vendor/social-media/icons/' . $platform->value . '-light.svg';
            $is_connected = $platform->platform()?->isConnected();
        @endphp
        <x-card
            class:body="flex flex-col items-center justify-center"
            @class([
                'lqd-social-media-card flex flex-col justify-center text-center text-heading-foreground transition-all hover:scale-105 hover:border-heading-foreground/10 hover:shadow-lg hover:shadow-black/5',
                'pointer-events-none saturate-0' => !$is_connected,
            ])
        >
            <figure class="mx-auto mb-8 w-9 transition-all group-hover/card:scale-125">
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
            <x-button
                class="relative opacity-70 before:absolute before:inset-0 before:top-full before:h-px before:origin-right before:scale-x-0 before:bg-current before:transition-transform group-hover/card:opacity-100 group-hover/card:before:origin-left group-hover/card:before:scale-x-100"
                variant="link"
                href="{{ route('dashboard.user.social-media.post.create', ['platform' => $platform->name]) }}"
                tabindex="-1"
            >
                @lang('Add +')
            </x-button>
            <a
                class="absolute inset-0 z-2 inline-block"
                href="{{ route('dashboard.user.social-media.post.create', ['platform' => $platform->name]) }}"
            ></a>
        </x-card>
    @endforeach
</div>
