@php
    $test_commands = ['Explain an Image', 'Summarize a book for research', 'Translate a book'];
    $disable_actions = $app_is_demo && (isset($category) && ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf' || $category->slug == 'ai_chat_image'));

    $example_prompts = collect([
        ['name' => 'Transcribe my class notes', 'prompt' => 'Transcribe my class notes'],
        ['name' => 'Morning Productivity Plan', 'prompt' => 'Morning Productivity Plan'],
        ['name' => 'Cold Email', 'prompt' => 'Cold Email'],
        ['name' => 'Newsletter', 'prompt' => 'Newsletter'],
        ['name' => 'Summarize', 'prompt' => 'Summarize'],
        ['name' => 'Study Vocabulary', 'prompt' => 'Study Vocabulary'],
        ['name' => 'Create a workout plan', 'prompt' => 'Create a workout plan'],
        ['name' => 'Translate This Book', 'prompt' => 'Translate This Book'],
        ['name' => 'Generate a cute panda image', 'prompt' => 'Generate a cute panda image'],
        ['name' => 'Plan a 3 day trip to Rome', 'prompt' => 'Plan a 3 day trip to Rome'],
        ['name' => 'Pick an outfit', 'prompt' => 'Pick an outfit'],
        ['name' => 'How can I learn coding?', 'prompt' => 'How can I learn coding?'],
        ['name' => 'Experience Tokyo', 'prompt' => 'Experience Tokyo'],
        ['name' => 'Create a 4 course menu', 'prompt' => 'Create a 4 course menu'],
        ['name' => 'Help me write a story', 'prompt' => 'Help me write a story'],
        ['name' => 'Translate', 'prompt' => 'Translate'],
    ])
        ->map(fn($item) => (object) $item)
        ->toArray();
    $example_prompts_json = json_encode($example_prompts, JSON_THROW_ON_ERROR);
    $example_prompts = json_decode(setting('ai_chat_pro_suggestions', $example_prompts_json), false, 512, JSON_THROW_ON_ERROR);
@endphp

<div
    class="conversation-area flex h-[inherit] grow flex-col justify-between overflow-y-auto rounded-b-[inherit] rounded-t-[inherit] max-md:max-h-full"
    id="chat_area_to_hide"
>

    @if (view()->hasSection('chat_head'))
        @yield('chat_head')
    @else
        @include('panel.user.openai_chat.components.chat_head')
    @endif

    <div class="relative flex grow flex-col">

        <div @class([
            'grid place-items-center w-full overflow-x-hidden',
            'h-full' => $category->slug != 'ai_vision',
        ])>
            <div
                class="pointer-events-none invisible col-start-1 col-end-1 row-start-1 row-end-1 flex w-full scale-[1.1] flex-col items-center overflow-hidden py-10 opacity-0 transition-all group-[&.conversation-not-started]/chats-wrap:pointer-events-auto group-[&.conversation-not-started]/chats-wrap:visible group-[&.conversation-not-started]/chats-wrap:scale-100 group-[&.conversation-not-started]/chats-wrap:opacity-100">
                <h2 class="mb-8 text-center text-3xl font-bold leading-[1.2em] md:text-[34px] lg:text-[clamp(34px,2vw,40px)]">
                    <span class="text-[0.647em] opacity-50">
                        {{ __("I'm") }} {{ $category->name }}
                    </span>
                    <br>
                    {{ __('Ask me anything') }}
                </h2>

                <div
                    class="flex w-full gap-4 [--mask-from:7rem] [--mask-to:calc(100%-7rem)]"
                    style="mask-image: linear-gradient(to right, transparent, black var(--mask-from), black var(--mask-to), transparent);"
                    x-data="marquee({ pauseOnHover: true })"
                >
                    <div class="lqd-marquee-viewport relative flex w-full overflow-hidden">
                        <div class="lqd-marquee-slider flex w-full gap-4 py-2 lg:px-14">
                            @foreach ($example_prompts ?? [] as $prompt)
                                <button
                                    class="lqd-marquee-cell inline-flex shrink-0 items-center justify-center whitespace-nowrap rounded-xl bg-surface-background px-2.5 py-3 text-base font-semibold leading-[1.15em] transition-all hover:-translate-y-1 hover:shadow dark:bg-heading-foreground/5 dark:hover:bg-white lg:text-[1.2vw]"
                                    data-prompt="{{ __($prompt?->prompt) }}"
                                    type="button"
                                    @click.prevent="prompt = $event.currentTarget.getAttribute('data-prompt'); $nextTick(() => { $refs.prompt.focus() })"
                                >
                                    <span class="bg-gradient-to-r from-gradient-from via-gradient-via to-gradient-to bg-clip-text text-transparent">
                                        {{ __($prompt?->name) }}
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div @class([
                'chats-container text-xs p-8 max-md:p-4 overflow-x-hidden col-start-1 col-end-1 row-start-1 row-end-1 w-full transition-all group-[&.conversation-not-started]/chats-wrap:scale-95 group-[&.conversation-not-started]/chats-wrap:opacity-0 group-[&.conversation-not-started]/chats-wrap:invisible group-[&.conversation-not-started]/chats-wrap:pointer-events-none',
                'md:mb-auto md:pb-6 relative z-10' => $category->slug == 'ai_vision',
                'h-full' => $category->slug != 'ai_vision',
            ])>

                @if (view()->hasSection('chat_area'))
                    @yield('chat_area')
                @else
                    @include('panel.user.openai_chat.components.chat_area')
                @endif

                @if ($category->slug == 'ai_vision' && ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
                    <div
                        class="flex flex-col items-center justify-center gap-y-3"
                        id="sugg"
                    >
                        <div class="flex flex-wrap items-center gap-2 text-2xs font-medium leading-relaxed text-heading-foreground">
                            {{ __('Upload an image and ask me anything') }}
                            <x-tabler-chevron-down class="size-4" />
                        </div>

                        @foreach ($test_commands as $command)
                            <x-button
                                class="font-normal"
                                tag="button"
                                variant="secondary"
                                onclick="addText('{{ __($command) }}');"
                            >
                                {{ __($command) }}
                            </x-button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if ($category->slug == 'ai_vision' && ((isset($lastThreeMessage) && $lastThreeMessage->count() == 0) || !isset($lastThreeMessage)))
            <div
                class="relative z-10 mt-auto flex items-center justify-center px-4 pb-5 md:px-8"
                id="mainupscale_src"
                ondrop="dropHandler(event, 'upscale_src');"
                ondragover="dragOverHandler(event);"
            >
                <label
                    class="flex w-full cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed border-foreground/10 bg-background px-4 py-8 transition-colors hover:bg-foreground/10"
                    for="upscale_src"
                >
                    <div class="flex flex-col items-center justify-center">
                        <x-tabler-cloud-upload
                            class="mb-4 size-11"
                            stroke-width="1.5"
                        />

                        <span class="mb-1 block text-sm font-semibold">
                            {{ __('Drop your image here or browse') }}
                        </span>

                        <span class="file-name mb-0 block text-2xs">
                            @if ($category->slug != 'ai_vision' && $category->slug != 'ai_pdf')
                                {{ __('(Only jpg, png, webp will be accepted)') }}
                            @else
                                {{ __('(Only jpg, png and webp will be accepted)') }}
                            @endif
                        </span>
                    </div>
                    <input
                        class="hidden"
                        id="upscale_src"
                        type="file"
                        accept="@if ($category->slug == 'ai_vision' || $category->slug == 'ai_pdf') .png, .jpg, .jpeg, .pdf @else .png, .jpg, .jpeg @endif"
                        onchange="handleFileSelect('upscale_src')"
                    />
                </label>
            </div>
        @endif
    </div>

    {{-- @if ($category->slug == 'ai_realtime_voice_chat')
        @includeIf('openai-realtime-chat::chat-button', ['compact' => false, 'category_slug' => $category->slug, 'messages' => $chat->messages])
    @endif --}}

    @if (setting('realtime_voice_chat', 0))
        <div
            class="lqd-audio-vis-wrap group/audio-vis pointer-events-none invisible absolute start-0 top-0 z-2 flex h-full w-full flex-col items-center justify-between gap-y-5 overflow-hidden bg-background/10 px-5 py-28 opacity-0 backdrop-blur-lg transition-all [&.active]:visible [&.active]:opacity-100"
            data-state="idle"
        >
            <div></div>
            <div
                class="invisible relative grid w-full scale-110 place-content-center place-items-center opacity-0 blur-lg transition-all duration-300 group-[&.active]/audio-vis:visible group-[&.active]/audio-vis:scale-100 group-[&.active]/audio-vis:opacity-100 group-[&.active]/audio-vis:blur-0">
                <div class="lqd-audio-vis-circ absolute left-1/2 top-1/2 col-start-1 col-end-1 row-start-1 row-end-1 -translate-x-1/2 -translate-y-1/2">
                    <div
                        class="inline-flex size-40 animate-spin rounded-full bg-gradient-to-b from-[#C13CFF] to-[#00BFFF] opacity-50 blur-3xl [animation-duration:2s] lg:size-[200px]">
                    </div>
                </div>
                <div
                    class="lqd-audio-vis-bars col-start-1 col-end-1 row-start-1 row-end-1 flex h-8 scale-75 items-center gap-[3px] text-heading-foreground opacity-0 transition-all group-[&[data-state=playing]]/audio-vis:scale-100 group-[&[data-state=playing]]/audio-vis:opacity-100">
                    <div class="lqd-audio-vis-bar inline-flex min-h-[7px] w-[7px] origin-center rounded-full bg-current"></div>
                    <div class="lqd-audio-vis-bar inline-flex min-h-[7px] w-[7px] origin-center rounded-full bg-current"></div>
                    <div class="lqd-audio-vis-bar inline-flex min-h-[7px] w-[7px] origin-center rounded-full bg-current"></div>
                    <div class="lqd-audio-vis-bar inline-flex min-h-[7px] w-[7px] origin-center rounded-full bg-current"></div>
                    <div class="lqd-audio-vis-bar inline-flex min-h-[7px] w-[7px] origin-center rounded-full bg-current"></div>
                </div>
                <div
                    class="lqd-audio-vis-dot-wrap col-start-1 col-end-1 row-start-1 row-end-1 flex scale-75 animate-bounce items-center gap-[3px] text-heading-foreground opacity-0 transition-all group-[&[data-state=idle]]/audio-vis:scale-100 group-[&[data-state=recording]]/audio-vis:scale-100 group-[&[data-state=idle]]/audio-vis:opacity-100 group-[&[data-state=recording]]/audio-vis:opacity-100 group-[&[data-state=recording]]/audio-vis:[animation-play-state:paused]">
                    <div class="lqd-audio-vis-dot inline-flex size-4 origin-center rounded-full bg-current">
                    </div>
                </div>
                <div
                    class="lqd-audio-vis-loader active col-start-1 col-end-1 row-start-1 row-end-1 flex scale-75 items-center text-heading-foreground opacity-0 transition-all group-[&[data-state=waiting]]/audio-vis:scale-100 group-[&[data-state=waiting]]/audio-vis:opacity-100">
                    <x-tabler-loader-2 class="size-4 animate-spin" />
                </div>
            </div>
            <x-button
                class="pointer-events-auto size-[50px] shrink-0 border border-heading-foreground/5 bg-transparent backdrop-blur-md backdrop-contrast-125 hover:bg-red-500 hover:text-white"
                variant="ghost-shadow"
                size="none"
                @click.prevent="$dispatch('audio-vis', { action: 'stop' })"
                x-data="{}"
            >
                <span class="sr-only">
                    {{ __('Stop') }}
                </span>
                <x-tabler-x class="size-4" />
            </x-button>
        </div>
    @endif

    @if (view()->hasSection('chat_form'))
        @yield('chat_form')
    @else
        @include('ai-chat-pro::includes.chat_form')
    @endif
</div>
