@php
    use App\Extensions\SocialMedia\System\Enums\PlatformEnum;
    use App\Helpers\Classes\Helper;

    $url_show_query = request()->query('show');
@endphp

<div
    class="lqd-posts-item lqd-social-media-post-item relative grid w-full items-center gap-4 border-b p-3 text-2xs font-medium transition-all last:border-b-0 hover:bg-foreground/5 group-[&[data-view-mode=grid]]:min-h-48 group-[&[data-view-mode=grid]]:gap-0 group-[&[data-view-mode=grid]]:bg-card-background group-[&[data-view-mode=grid]]:pb-1 max-lg:block max-lg:!min-w-0 max-lg:space-y-3">
    <div
        class="lqd-posts-item-content sort-name grid grid-flow-col-dense flex-wrap items-center justify-start gap-3 text-sm transition-border group-[&[data-view-mode=grid]]:mb-1 group-[&[data-view-mode=grid]]:block group-[&[data-view-mode=grid]]:h-28 group-[&[data-view-mode=grid]]:items-start group-[&[data-view-mode=grid]]:overflow-hidden group-[&[data-view-mode=grid]]:border-b group-[&[data-view-mode=grid]]:pb-3 group-[&[data-view-mode=grid]]:pt-3 group-[&[data-view-mode=grid]]:text-2xs max-lg:group-[&[data-view-mode=list]]:!flex">
        <span class="mb-1 block w-full text-4xs font-medium uppercase leading-tight tracking-wider text-foreground/50 group-[&[data-view-mode=grid]]:hidden lg:hidden">
            {{ __('Content') }}
        </span>

        @if (filled($post['image']))
            <img
                class="lqd-posts-item-img h-8 w-[38px] shrink-0 rounded-[4px] object-cover object-center shadow group-[&[data-view-mode=grid]]:mb-2 group-[&[data-view-mode=grid]]:aspect-video group-[&[data-view-mode=grid]]:h-auto group-[&[data-view-mode=grid]]:w-full group-[&[data-view-mode=grid]]:rounded-md"
                src="{{ ThumbImage(custom_theme_url($post['image'])) }}"
                alt="{{ __('Post image') }}"
                loading="lazy"
            />
        @endif
        <div class="lqd-posts-item-content-inner grow overflow-hidden group-[&[data-view-mode=grid]]:h-full">
            <p
                class="lqd-posts-item-title m-0 overflow-hidden overflow-ellipsis whitespace-nowrap group-[&[data-view-mode=grid]]:h-full group-[&[data-view-mode=grid]]:whitespace-normal">
                {{ str()->limit(strip_tags($post['content']), 30) }}
            </p>
        </div>
    </div>

    <span class="mb-1 block w-full text-4xs font-medium uppercase leading-tight tracking-wider text-foreground/50 group-[&[data-view-mode=grid]]:hidden lg:hidden">
        {{ __('Status') }}
    </span>
    <p @class([
        'lqd-posts-item-type sort-file inline-flex w-auto m-0 items-center gap-1.5 justify-self-start whitespace-nowrap rounded-full border px-2 py-1 text-[12px] font-medium leading-none',
        'text-green-500' => $post['status'] === 'published',
        'text-yellow-700' => $post['status'] === 'scheduled',
    ])>
        @if ($post['status'] === \App\Extensions\SocialMedia\System\Enums\StatusEnum::published)
            <x-tabler-check class="size-4" />
        @elseif ($post['status'] === \App\Extensions\SocialMedia\System\Enums\StatusEnum::scheduled)
            <x-tabler-clock class="size-4" />
        @else
            <x-tabler-circle-dashed class="size-4" />
        @endif
        @lang(str()->title($post->status->value))
    </p>

    <span class="mb-1 block w-full text-4xs font-medium uppercase leading-tight tracking-wider text-foreground/50 group-[&[data-view-mode=grid]]:hidden lg:hidden">
        {{ __('Publish Date') }}
    </span>

    <p class="lqd-posts-item-date sort-date m-0 group-[&[data-view-mode=list]]:font-normal">
        {{ date('M j Y', strtotime($post->scheduled_at)) }}
        <span class="opacity-50 group-[&[data-view-mode=grid]]:hidden">
            , {{ date('H:i', strtotime($post->scheduled_at)) }}
        </span>
    </p>

    <p class="lqd-posts-item-likes sort-likes m-0 group-[&[data-view-mode=grid]]:text-end group-[&[data-view-mode=list]]:font-normal">
        <span class="mb-1 block w-full text-4xs font-medium uppercase leading-tight tracking-wider text-foreground/50 group-[&[data-view-mode=grid]]:hidden lg:hidden">
            {{ __('Like Count') }}
        </span>

        {{ data_get($post->post_metrics, 'like_count', Helper::generateNumberForDemo()) ?: Helper::generateNumberForDemo() }}
    </p>

    @php
        $image = 'vendor/social-media/icons/' . $post->platform?->platform . '.svg';
        $image_dark_version = 'vendor/social-media/icons/' . $post->platform?->platform . '-light.svg';
    @endphp
    <figure class="lqd-posts-item-cost sort-cost">
        <span class="mb-1 block w-full text-4xs font-medium uppercase leading-tight tracking-wider text-foreground/50 group-[&[data-view-mode=grid]]:hidden lg:hidden">
            {{ __('Platform') }}
        </span>

        <img
            @class([
                'w-8 h-auto',
                'dark:hidden' => file_exists($image_dark_version),
            ])
            src="{{ asset($image) }}"
            alt="{{ $post->platform?->platform }}"
        />
        @if (file_exists($image_dark_version))
            <img
                class="hidden h-auto w-8 dark:block"
                src="{{ asset($image_dark_version) }}"
                alt="{{ $post->platform?->platform }}"
            />
        @endif
    </figure>

    <div class="lqd-posts-item-actions flex flex-wrap items-center gap-2 font-normal lg:flex-nowrap lg:justify-end">
        <span class="mb-1 block w-full text-4xs font-medium uppercase leading-tight tracking-wider text-foreground/50 group-[&[data-view-mode=grid]]:hidden lg:hidden">
            {{ __('Actions') }}
        </span>

        {{-- Duplicate Modal --}}
        <x-modal
            class:modal-head="border-b-0"
            class:modal-body="pt-3"
            class:modal-container="max-w-[540px] lg:w-[540px]"
        >
            <x-slot:trigger
                class="z-10 size-9 group-[&[data-view-mode=grid]]:hidden"
                size="none"
                variant="ghost-shadow"
                href="#"
                title="{{ __('Duplicate') }}"
            >
                <x-tabler-copy
                    class="size-4"
                    stroke-width="2.5"
                />
            </x-slot:trigger>

            <x-slot:modal>
                <form
                    action="#"
                    x-ref="form"
                    x-on:submit.prevent="submitDuplicate"
                >
                    <h3 class="mb-3.5">
                        @lang('Duplicate Post')
                    </h3>
                    <p class="mb-7 text-heading-foreground/60">
                        @lang('Choose the platform to duplicate the post.')
                    </p>
                    <input
                        type="hidden"
                        name="route"
                        value="{{ route('dashboard.user.social-media.post.duplicate', $post->id) }}"
                    >

                    <x-forms.input
                        type="select"
                        size="lg"
                        name="platform_id"
                        label="{{ __('Platform') }}"
                    >
                        @foreach (PlatformEnum::cases() as $platform)
                            <option
                                {{ $platform->platform()?->isConnected() ? '' : 'disabled' }}
                                value="{{ $platform->platform()?->id }}"
                            >
                                {{ str()->title($platform->value) }} ({{ $platform->platform()?->isConnected() ? __('Connected') : __('Not Connected') }})
                            </option>
                        @endforeach
                    </x-forms.input>

                    <div class="flex justify-end space-x-2 pt-7">
                        <x-button
                            variant="ghost-shadow"
                            type="button"
                            title="{{ __('Cancel') }}"
                            @click.prevent="modalOpen = false"
                        >
                            @lang('Cancel')
                        </x-button>
                        <x-button
                            variant="primary"
                            type="button"
                            @click="submitDuplicate"
                            title="{{ __('Duplicate') }}"
                        >
                            @lang('Duplicate')
                        </x-button>
                    </div>
                </form>
            </x-slot:modal>
        </x-modal>

        @if ($post['status'] === 'published')
            <x-button
                class="z-10 size-9 group-[&[data-view-mode=grid]]:hidden"
                size="none"
                variant="ghost-shadow"
                href="#"
                title="{{ __('Copy Link') }}"
                @click.prevent="navigator.clipboard.writeText('{{ $post['link'] }}'); toastr.success('{{ __('Link copied to clipboard.') }}')"
            >
                <x-tabler-link class="size-5" />
            </x-button>
        @endif

        <x-button
            class="z-10 size-9 group-[&[data-view-mode=grid]]:hidden"
            size="none"
            variant="ghost-shadow"
            title="{{ __('View') }}"
            href="{{ route('dashboard.user.social-media.post.show', $post['id']) }}"
            x-target="lqd-social-media-post-content"
            @ajax:before="onAjaxBefore()"
            @ajax:success="onAjaxSuccess()"
            @ajax:error="onAjaxError()"
            x-ref="viewBtn-{{ $post['id'] }}"
            x-init="{{ $url_show_query ? 'true' : 'false' }} && '{{ $url_show_query ? $url_show_query : '' }}' === '{{ $post['id'] }}' && $nextTick(() => $el.click())"
        >
            <x-tabler-eye class="size-5" />
        </x-button>

        @if ($post['status'] !== 'published')
            <x-dropdown.dropdown
                class:dropdown-dropdown="group-[&[data-view-mode=grid]]:top-auto group-[&[data-view-mode=grid]]:bottom-full"
                anchor="end"
                offsetY="5px"
                triggerType="click"
            >
                <x-slot:trigger
                    class="before:-star[5%]-0 z-10 size-9 p-0 text-foreground/50 before:absolute before:-top-[5%] before:h-[120%] before:w-[120%] hover:bg-background group-[&[data-view-mode=grid]]:-me-3 group-[&[data-view-mode=grid]]:text-base group-[&[data-view-mode=grid]]:text-foreground"
                    variant="ghost"
                    size="xs"
                >
                    <x-tabler-dots-vertical class="size-5 group-[&[data-view-mode=grid]]:h-4 group-[&[data-view-mode=grid]]:w-4" />
                </x-slot:trigger>

                <x-slot:dropdown
                    class="overflow-hidden p-1 text-2xs font-medium group-[&[data-view-mode=grid]]:-me-3"
                >
                    <x-button
                        class="w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit group-[&[data-view-mode=grid]]:flex"
                        size="none"
                        variant="ghost-shadow"
                        hover-variant="danger"
                        href="{{ route('dashboard.user.social-media.post.edit', $post['id']) }}"
                    >
                        <x-tabler-pencil class="size-5" />
                        @lang('Edit')
                    </x-button>

                    @if ($app_is_demo)
                        <x-button
                            class="w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit group-[&[data-view-mode=grid]]:flex"
                            size="none"
                            variant="ghost-shadow"
                            hover-variant="danger"
                            onclick="return toastr.info('This feature is not available in demo mode.')"
                        >
                            <x-tabler-circle-minus class="size-4 text-red-600" />
                            @lang('Delete')
                        </x-button>
                    @else
                        <x-button
                            class="w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit group-[&[data-view-mode=grid]]:flex"
                            size="none"
                            variant="ghost-shadow"
                            hover-variant="danger"
                            href="{{ route('dashboard.user.social-media.post.delete', $post['id']) }}"
                            onclick="return confirm('Are you sure?')"
                        >
                            <x-tabler-circle-minus class="size-4 text-red-600" />
                            @lang('Delete')
                        </x-button>
                    @endif

                </x-slot:dropdown>
            </x-dropdown.dropdown>
        @endif
    </div>
</div>
