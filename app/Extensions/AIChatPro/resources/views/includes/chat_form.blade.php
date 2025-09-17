@php
    $prompt_filters = [
        'all' => __('All'),
        'favorite' => __('Favorite'),
    ];
@endphp

<div
    class="lqd-chat-form-wrap sticky bottom-0 z-9 rounded-b-[inherit] bg-background md:before:pointer-events-none md:before:absolute md:before:inset-x-0 md:before:-top-14 md:before:bottom-0 md:before:z-0 md:before:bg-background md:before:transition-colors md:before:[mask-image:linear-gradient(to_bottom,transparent,black_30%)] lg:pb-8">
    {{-- using form element cause issues in webchat after analyzing a website --}}
    <div
        class="lqd-chat-form flex w-full flex-wrap items-end gap-3 self-end rounded-ee-[inherit] px-8 py-5 max-md:items-end max-md:p-4 max-sm:p-3 md:relative md:z-1 md:px-5 lg:mx-auto lg:w-full lg:max-w-[820px] lg:p-0"
        id="chat_form"
    >
        @csrf

		@includeIf('chat-pro-temp-chat::temp-desc', ['compact' => true, 'tempChat' => $tempChat])

        <div
            class="lqd-chat-form-inputs-container flex min-h-[52px] w-full flex-col rounded-[26px] border border-solid border-heading-foreground/5 bg-background transition-colors lg:rounded-[35px] lg:border-none lg:bg-surface-background lg:shadow-[0_4px_60px_rgba(0,0,0,0.035)] dark:lg:bg-heading-foreground/15">
            <div
                class="hidden max-h-32 w-full grid-cols-3 gap-5 overflow-y-auto p-2.5 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 [&.active]:grid"
                id="chat_images"
            ></div>

            <div
                class="hidden max-h-32 w-full grid-cols-3 gap-5 overflow-y-auto p-2.5 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 2xl:grid-cols-8 [&.active]:grid"
                id="chat_pdfs"
            ></div>

            <div class="relative flex grow items-center max-md:px-2">
                <input
                    id="selectImageInput"
                    type="file"
                    style="display: none;"
                    @if ($category->slug != 'ai_vision' && $category->slug != 'ai_pdf') accept="image/*" @endif
                />

                <x-button
                    class="lqd-chat-mobile-options-trigger size-8 shrink-0 origin-center border transition-transform md:hidden [&.active]:rotate-45"
                    @click.prevent="toggleMobileOptions()"
                    @click.outside="mobileOptionsShow && (mobileOptionsShow = false)"
                    ::class="{ 'active': mobileOptionsShow }"
                    size="none"
                    variant="ghost"
                    tag="button"
                >
                    <x-tabler-plus class="size-4" />
                    <span class="sr-only">{{ __('Options') }}</span>
                </x-button>

                <x-forms.input
                    id="prompt"
                    @class([
                        'm-0 max-h-32 w-full border-none bg-transparent py-2 pe-[240px] text-sm text-heading-foreground placeholder:text-heading-foreground focus:outline-none focus:ring-0 max-lg:pe-[200px] max-md:max-h-[200px] max-md:pe-2 max-md:ps-1 max-md:text-[16px] lg:min-h-[70px] lg:py-6',
                        'lg:ps-20 ps-14' => $category->slug !== 'ai_pdf',
                    ])
                    container-class="w-full"
                    type="textarea"
                    placeholder="{{ __('Type a message') }}"
                    name="prompt"
                    rows="1"
                    x-model="prompt"
                    x-ref="prompt"
                    ::bind="prompt"
                />

                <div
                    class="pointer-events-none absolute bottom-0 end-2 start-2 flex items-end justify-between text-sm max-md:static md:bottom-1 md:gap-1.5 lg:bottom-3.5 lg:end-5 lg:start-5 lg:gap-2.5">
                    <div
                        class="flex grow items-center justify-between max-md:invisible max-md:absolute max-md:-start-1 max-md:bottom-full max-md:end-0 max-md:mb-3 max-md:translate-y-1 max-md:scale-95 max-md:flex-col max-md:items-start max-md:gap-4 max-md:rounded-xl max-md:bg-background max-md:px-4 max-md:py-0 max-md:opacity-0 max-md:shadow-lg max-md:transition-all md:flex md:h-full md:gap-1.5 lg:gap-2.5 max-md:[&.active]:visible max-md:[&.active]:translate-y-0 max-md:[&.active]:scale-100 max-md:[&.active]:opacity-100"
                        id="chat-options"
                        :class="{ 'active': mobileOptionsShow }"
                    >

                        <div @class([
                            'pointer-events-auto max-md:pt-4',
                            'flex items-center' => $category->slug !== 'ai_pdf',
                            'hidden' => $category->slug === 'ai_pdf',
                            'max-md:pb-4' => !Auth::check(),
                        ])>
                            <button
                                class="lqd-chat-attach max-md:!text-heading flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 rounded-full border-heading-foreground/5 border-heading-foreground/5 text-heading-foreground transition-all max-md:size-auto max-md:bg-transparent max-md:text-heading-foreground md:border md:hover:bg-primary md:hover:text-primary-foreground lg:size-11"
                                type="button"
                                @if ($app_is_demo) onclick='return toastr.info("@lang('This feature is disabled in Demo version.')")'
								@elseif(!auth()->check()) onclick='return toastr.warning("@lang('Login to upload a document or image.')")'
								@else id="chat_add_image" @endif
                            >
                                <x-tabler-plus
                                    class="size-5"
                                    stroke-width="1.5"
                                />
                                <span class="md:hidden">{{ __('Upload a document or image') }}</span>
                            </button>
                        </div>

                        <div class="max-md:hidden md:ms-auto"></div>

						@includeIf('chat-pro-temp-chat::temp_chat_button', ['compact' => true, 'tempChat' => $tempChat, 'category' => $category])

						@includeIf('openai-realtime-chat::chat-button', ['compact' => true, 'category_slug' => $category->slug, 'messages' => $chat->messages])

                        @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('canvas') && (bool) setting('ai_chat_pro_canvas', 1))
                            @includeIf('canvas::includes.canvas-button')
                        @endif

                        @if (setting('user_prompt_library') == null || setting('user_prompt_library'))
                            <div @class([
                                'pointer-events-auto flex items-center max-md:flex-col max-md:items-start max-md:gap-4',
                                'max-md:pt-4' => $category->slug === 'ai_pdf',
                            ])>
                                <x-button
                                    class="lqd-chat-templates-trigger flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 border-heading-foreground/5 text-heading-foreground transition-all max-md:size-auto max-md:bg-transparent max-md:hover:bg-transparent max-md:hover:shadow-none md:border md:hover:border-primary md:hover:bg-primary md:hover:text-primary-foreground lg:size-11"
                                    type="button"
                                    variant="ghost"
                                    size="none"
                                    @click.prevent="{{ auth()->check() ? 'togglePromptLibraryShow()' : 'toastr.warning(\'' . __('Login to access prompt library.') . '\')' }}"
                                >
                                    <svg
                                        width="17"
                                        height="14"
                                        viewBox="0 0 17 14"
                                        fill="currentColor"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M2.24902 14.0001C1.76777 14.0001 1.35579 13.8287 1.01309 13.486C0.670378 13.1433 0.499023 12.7313 0.499023 12.2501C0.499023 11.7688 0.670378 11.3568 1.01309 11.0141C1.35579 10.6714 1.76777 10.5001 2.24902 10.5001C2.73027 10.5001 3.14225 10.6714 3.48496 11.0141C3.82767 11.3568 3.99902 11.7688 3.99902 12.2501C3.99902 12.7313 3.82767 13.1433 3.48496 13.486C3.14225 13.8287 2.73027 14.0001 2.24902 14.0001ZM2.24902 8.75006C1.76777 8.75006 1.35579 8.57871 1.01309 8.236C0.670378 7.89329 0.499023 7.48131 0.499023 7.00006C0.499023 6.51881 0.670378 6.10683 1.01309 5.76412C1.35579 5.42141 1.76777 5.25006 2.24902 5.25006C2.73027 5.25006 3.14225 5.42141 3.48496 5.76412C3.82767 6.10683 3.99902 6.51881 3.99902 7.00006C3.99902 7.48131 3.82767 7.89329 3.48496 8.236C3.14225 8.57871 2.73027 8.75006 2.24902 8.75006ZM2.24902 3.50006C1.76777 3.50006 1.35579 3.32871 1.01309 2.986C0.670378 2.64329 0.499023 2.23131 0.499023 1.75006C0.499023 1.26881 0.670378 0.856832 1.01309 0.514124C1.35579 0.171415 1.76777 6.10352e-05 2.24902 6.10352e-05C2.73027 6.10352e-05 3.14225 0.171415 3.48496 0.514124C3.82767 0.856832 3.99902 1.26881 3.99902 1.75006C3.99902 2.23131 3.82767 2.64329 3.48496 2.986C3.14225 3.32871 2.73027 3.50006 2.24902 3.50006ZM7.49902 3.50006C7.01777 3.50006 6.60579 3.32871 6.26309 2.986C5.92038 2.64329 5.74902 2.23131 5.74902 1.75006C5.74902 1.26881 5.92038 0.856832 6.26309 0.514124C6.60579 0.171415 7.01777 6.10352e-05 7.49902 6.10352e-05C7.98027 6.10352e-05 8.39225 0.171415 8.73496 0.514124C9.07767 0.856832 9.24902 1.26881 9.24902 1.75006C9.24902 2.23131 9.07767 2.64329 8.73496 2.986C8.39225 3.32871 7.98027 3.50006 7.49902 3.50006ZM12.749 3.50006C12.2678 3.50006 11.8558 3.32871 11.5131 2.986C11.1704 2.64329 10.999 2.23131 10.999 1.75006C10.999 1.26881 11.1704 0.856832 11.5131 0.514124C11.8558 0.171415 12.2678 6.10352e-05 12.749 6.10352e-05C13.2303 6.10352e-05 13.6423 0.171415 13.985 0.514124C14.3277 0.856832 14.499 1.26881 14.499 1.75006C14.499 2.23131 14.3277 2.64329 13.985 2.986C13.6423 3.32871 13.2303 3.50006 12.749 3.50006ZM7.49902 8.75006C7.01777 8.75006 6.60579 8.57871 6.26309 8.236C5.92038 7.89329 5.74902 7.48131 5.74902 7.00006C5.74902 6.51881 5.92038 6.10683 6.26309 5.76412C6.60579 5.42141 7.01777 5.25006 7.49902 5.25006C7.98027 5.25006 8.39225 5.42141 8.73496 5.76412C9.07767 6.10683 9.24902 6.51881 9.24902 7.00006C9.24902 7.48131 9.07767 7.89329 8.73496 8.236C8.39225 8.57871 7.98027 8.75006 7.49902 8.75006ZM8.37402 14.0001V11.3094L13.2084 6.49694C13.3396 6.36569 13.4855 6.27089 13.6459 6.21256C13.8063 6.15423 13.9667 6.12506 14.1271 6.12506C14.3021 6.12506 14.4699 6.15787 14.6303 6.2235C14.7907 6.28912 14.9365 6.38756 15.0678 6.51881L15.8771 7.32819C15.9938 7.45944 16.085 7.60527 16.1506 7.76569C16.2162 7.9261 16.249 8.08652 16.249 8.24694C16.249 8.40735 16.2199 8.57141 16.1615 8.73912C16.1032 8.90683 16.0084 9.05631 15.8771 9.18756L11.0646 14.0001H8.37402ZM9.68652 12.6876H10.5178L13.1646 10.0188L12.7709 9.60319L12.3553 9.20944L9.68652 11.8563V12.6876ZM12.7709 9.60319L12.3553 9.20944L13.1646 10.0188L12.7709 9.60319Z"
                                        />
                                    </svg>
                                    <span class="md:hidden">
                                        {{ __('Browse prompt library') }}
                                    </span>
                                </x-button>
                            </div>
                            @auth
                                @include('panel.user.openai_chat.components.prompt_library_modal')
                            @endauth
                        @endif
                        {{-- Brand Voice --}}
                        <div class="pointer-events-auto flex items-center max-md:flex-col max-md:items-start max-md:gap-4 max-md:pb-4">
                            <x-modal
                                class="lqd-chat-brand-voice"
                                id="brandVoiceModal"
                                title="{{ __('Brand Voice') }}"
                                @click.prevent="{{ auth()->check() ? null : 'toastr.warning(\'' . __('Login to include your brand voice in the chat.') . '\')' }}"
                            >
                                <x-slot:trigger
                                    class="lqd-chat-brand-voice-trigger flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 border-heading-foreground/5 p-0 text-heading-foreground transition-all max-md:size-auto max-md:bg-transparent max-md:hover:bg-transparent max-md:hover:shadow-none md:border md:hover:border-primary md:hover:bg-primary md:hover:text-primary-foreground lg:size-11"
                                    variant="none"
                                >
                                    <x-tabler-focus-2
                                        class="size-[23px]"
                                        stroke-width="1.5"
                                    />
                                    <span class="md:hidden">
                                        {{ __('Brand Voice') }}
                                    </span>
                                </x-slot:trigger>
                                @auth
                                    <x-slot:modal
                                        x-data="{}"
                                    >
                                        <div class="flex flex-col gap-6">
                                            <x-forms.input
                                                id="chat_brand_voice"
                                                type="select"
                                                size="lg"
                                                name="chat_brand_voice"
                                                label="{{ __('Select Company') }}"
                                                onchange="getProductByBrand(this.value)"
                                            >
                                                <option value="">
                                                    {{ __('Select Company') }}
                                                </option>
                                                @foreach (auth()->user()?->getCompanies() ?? [] as $company)
                                                    <option
                                                        data-tone_of_voice="{{ $company->tone_of_voice }}"
                                                        value="{{ $company->id }}"
                                                    >
                                                        {{ $company->name }}
                                                    </option>
                                                @endforeach
                                            </x-forms.input>

                                            <x-forms.input
                                                id="brand_voice_prod"
                                                type="select"
                                                size="lg"
                                                name="brand_voice_prod"
                                                label="{{ __('Select Product / Service') }}"
                                            >
                                                <option value="">{{ __('Select Product') }}</option>
                                            </x-forms.input>

                                            <div class="border-t pt-3 text-end">
                                                <x-button
                                                    @click.prevent="modalOpen = false"
                                                    type="button"
                                                    variant="outline"
                                                >
                                                    {{ __('Cancel') }}
                                                </x-button>

                                                <x-button
                                                    type="button"
                                                    @click.prevent="modalOpen = false"
                                                    onclick="setBrandVoice()"
                                                >
                                                    {{ __('Done') }}
                                                </x-button>
                                            </div>
                                        </div>
                                    </x-slot:modal>
                                @endauth
                            </x-modal>
                        </div>
                        @auth
                            {{-- Record Audio --}}
                            <div
                                class="pointer-events-auto !hidden group-[&.prompt-filled]/chats-wrap:hidden max-md:absolute max-md:bottom-2.5 max-md:end-14 max-md:group-[&:not(.prompt-filled)]/chats-wrap:end-3">
                                <x-button
                                    class="lqd-chat-record-trigger flex size-10 shrink-0 cursor-pointer items-center justify-center gap-2 text-heading-foreground transition-all max-md:size-auto max-md:bg-transparent max-md:hover:bg-transparent max-md:hover:shadow-none md:border md:hover:border-primary md:hover:bg-primary md:hover:text-primary-foreground lg:size-11 [&.inactive]:hidden"
                                    id="voice_record_button"
                                    type="button"
                                    variant="none"
                                    size="none"
                                    title="{{ __('Record audio') }}"
                                >
                                    <x-tabler-microphone
                                        class="size-[23px]"
                                        stroke-width="1.5"
                                    />
                                </x-button>
                                <x-button
                                    class="lqd-chat-record-stop-trigger hidden size-10 shrink-0 cursor-pointer items-center justify-center gap-2 text-heading-foreground transition-all max-md:size-auto max-md:bg-transparent max-md:hover:bg-transparent max-md:hover:shadow-none md:border md:hover:border-primary md:hover:bg-primary md:hover:text-primary-foreground lg:size-11 [&.active]:flex"
                                    id="voice_record_stop_button"
                                    type="button"
                                    variant="none"
                                    size="none"
                                    title="{{ __('Stop recording') }}"
                                >
                                    <x-tabler-player-pause-filled
                                        class="size-[23px]"
                                        stroke-width="1.5"
                                    />
                                </x-button>
                            </div>
                        @endauth
                    </div>
                    {{-- Send button --}}
                    <div class="pointer-events-auto max-md:absolute max-md:bottom-1 max-md:end-2">
                        <x-button
                            class="lqd-chat-send-btn aspect-square size-10 shrink-0 bg-heading-foreground/5 text-heading-foreground hover:bg-primary hover:text-primary-foreground lg:size-11"
                            id="{{ $category->slug == 'ai_vision' && $app_is_demo ? '' : 'send_message_button' }}"
                            size="none"
                            tag="button"
                            onclick="{!! $category->slug == 'ai_vision' && $app_is_demo ? 'return toastr.info(\'{{ __('This feature is disabled in Demo version.') }}\')' : '' !!}"
                            type="submit"
                        >
                            <svg
                                class="size-4 rtl:-scale-x-100"
                                width="15"
                                height="12"
                                viewBox="0 0 15 12"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path d="M0.25 12V7.5L6.25 6L0.25 4.5V0L14.5 6L0.25 12Z" />
                            </svg>
                        </x-button>
                        <x-button
                            class="lqd-chat-stop-btn hidden aspect-square size-10 shrink-0 bg-rose-500 lg:size-11 [&.active]:flex"
                            id="stop_button"
                            size="none"
                            tag="button"
                        >
                            <x-tabler-hand-stop
                                class="size-6"
                                stroke-width="1.5"
                            />
                        </x-button>
                    </div>
                </div>
            </div>
        </div>

        <input
            id="chatType"
            type="hidden"
            value="chatPro"
        />
        <input
            id="assistant"
            type="hidden"
            value="{{ $category->assistant }}"
        />
        <input
            id="chatbot_id"
            type="hidden"
            value="{{ $category->chatbot_id }}"
        />
        <input
            id="category_id"
            type="hidden"
            value="{{ $category->id }}"
        />
        <input
            id="chat_id"
            type="hidden"
            value="{{ isset($chat) ? $chat->id : null }}"
        />

    </div>

    @guest
        <p class="relative z-2 mx-auto text-center text-2xs text-heading-foreground/30 max-lg:px-6 lg:mb-0 lg:mt-6 lg:max-w-screen-md">
            {{ __(setting('guest_user_bottom_text', 'Login to save your current session. © MagicAI 2025. All rights reserved.')) }}
            <br>
            <x-button
                class="text-4xs uppercase tracking-widest text-heading-foreground/50 hover:text-heading-foreground"
                href="{{ url('/terms') }}"
                variant="link"
            >
                {{ __('Terms Of Use') }}
            </x-button>
        </p>
    @endguest
</div>
