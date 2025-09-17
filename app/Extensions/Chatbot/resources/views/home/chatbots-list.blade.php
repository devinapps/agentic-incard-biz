{{-- Chatbots List --}}
<div class="py-14">
    <h2 class="mb-9">
        @lang('Active Chatbots')
    </h2>

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
        {{-- Newly added items --}}
        <template
            x-for="chatbot in chatbots.data.filter(c => c.id !== 'new_chatbot')"
            :key="chatbot.id"
        >
            <x-card size="md">
                <x-slot:head
                    class="flex items-center justify-between gap-4 border-none px-5 py-[18px]"
                >
                    <figure>
                        <img
                            class="size-10 rounded-full object-cover object-center"
                            width="40"
                            height="40"
                            :src="`${window.location.origin}/${chatbot.avatar}`"
                            :alt="chatbot.title"
                        />
                    </figure>

                    <x-dropdown.dropdown
                        class:dropdown-dropdown="max-lg:end-0 max-lg:start-auto"
                        anchor="end"
                    >
                        <x-slot:trigger
                            class="size-10"
                        >
                            <svg
                                width="3"
                                height="13"
                                viewBox="0 0 3 13"
                                fill="currentColor"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M3 11.5C3 12.3 2.3 13 1.5 13C0.7 13 0 12.3 0 11.5C0 10.7 0.7 10 1.5 10C2.3 10 3 10.7 3 11.5ZM3 6.5C3 7.3 2.3 8 1.5 8C0.7 8 0 7.3 0 6.5C0 5.7 0.7 5 1.5 5C2.3 5 3 5.7 3 6.5ZM3 1.5C3 2.3 2.3 3 1.5 3C0.7 3 0 2.3 0 1.5C0 0.7 0.7 0 1.5 0C2.3 0 3 0.7 3 1.5Z"
                                />
                            </svg>
                            <span class="sr-only">
                                @lang('Chatbot Options')
                            </span>
                        </x-slot:trigger>
                        <x-slot:dropdown
                            class="min-w-[170px]"
                        >
                            @php
                                $dropdown_items = [
                                    [
                                        'label' => __('Edit'),
                                        'link' => '#',
                                        'attrs' => [
                                            '@click.prevent' => 'setActiveChatbot(chatbot.id, 1, true);',
                                        ],
                                    ],
                                    [
                                        'label' => __('Customize'),
                                        'link' => '#',
                                        'attrs' => [
                                            '@click.prevent' => 'setActiveChatbot(chatbot.id, 2, true);',
                                        ],
                                    ],
                                    [
                                        'label' => __('Train'),
                                        'link' => '#',
                                        'attrs' => [
                                            '@click.prevent' => 'setActiveChatbot(chatbot.id, 3);',
                                        ],
                                    ],
                                    [
                                        'label' => __('Test & Embed'),
                                        'link' => '#',
                                        'attrs' => [
                                            '@click.prevent' => 'setActiveChatbot(chatbot.id, 4, true);',
                                        ],
                                    ]
                                ];

								if (\App\Extensions\Chatbot\System\Helpers\ChatbotHelper::existChannels()){
									$dropdown_items[] = [
										'label' => __('Channel'),
                                        'link' => '#',
                                        'attrs' => [
                                            '@click.prevent' => 'setActiveChatbot(chatbot.id, 5, true);',
                                        ],
									];
								}

                            @endphp
                            <ul class="py-1 text-xs font-medium">
                                @foreach ($dropdown_items as $dropdown_item)
                                    <li>
                                        <a
                                            class="flex px-5 py-2 text-heading-foreground transition-colors hover:bg-heading-foreground/[3%]"
                                            href="{{ $dropdown_item['link'] }}"
                                            @foreach ($dropdown_item['attrs'] as $attr => $value)
                                            {{ $attr }}="{{ $value }}" @endforeach
                                        >
                                            @lang($dropdown_item['label'])
                                        </a>
                                    </li>
                                @endforeach
                                <li :class="{ 'opacity-50': submittingData, 'pointer-events-none': submittingData }">
                                    <x-forms.input
                                        class="h-[18px] w-[34px] [background-size:0.625rem]"
                                        class:label="py-2 px-5 flex-row-reverse justify-between text-xs font-medium text-heading-foreground hover:bg-heading-foreground/[3%]"
                                        label="{{ __('Activate') }}"
                                        type="checkbox"
                                        switcher
                                        ::id="`active-chatbot-${chatbot.id}`"
                                        ::checked="chatbot.active"
                                        @change="toggleChatbotActivation(chatbot.id);"
                                        x-model="chatbot.active"
                                        x-init="$el.closest('label').setAttribute('for', `active-chatbot-${chatbot.id}`)"
                                    />
                                </li>
                                <li :class="{ 'opacity-50': submittingData, 'pointer-events-none': submittingData }">
                                    <form
                                        action="{{ route('dashboard.chatbot.delete') }}"
                                        @submit.prevent="deleteChatbot"
                                    >
                                        <input
                                            type="hidden"
                                            :value="chatbot.id"
                                            name="id"
                                        >
                                        <x-button
                                            class="w-full justify-between rounded-none px-5 py-2 text-start text-xs font-medium text-heading-foreground hover:translate-y-0"
                                            variant="ghost"
                                            hover-variant="danger"
                                            type="submit"
                                        >
                                            @lang('Delete')
                                            <x-tabler-trash
                                                class="size-4"
                                                aria-hidden="true"
                                            />
                                        </x-button>
                                    </form>
                                </li>
                            </ul>
                        </x-slot:dropdown>
                    </x-dropdown.dropdown>
                </x-slot:head>

                <h3
                    class="mb-2.5"
                    x-text="chatbot.title"
                ></h3>
                <p
                    class="mb-2.5 text-sm font-medium text-heading-foreground/50"
                    x-data="{ diff: Math.floor((new Date() - new Date(chatbot.created_at)) / 1000) }"
                    x-init="if (Math.floor((new Date() - new Date(chatbot.created_at)) / 1000) < 60) { setInterval(() => { diff = Math.floor((new Date() - new Date(chatbot.created_at)) / 1000); }, 1000); }"
                >
                    @lang('Created')
                    <span
                        x-text="
                            diff < 60 ? diff + ' {{ __('seconds ago') }}' :
                            diff < 3600 ? (Math.floor(diff / 60) === 1 ? '1 {{ __('minute ago') }}' : Math.floor(diff / 60) + ' {{ __('minutes ago') }}') :
                            diff < 86400 ? (Math.floor(diff / 3600) === 1 ? '1 {{ __('hour ago') }}' : Math.floor(diff / 3600) + ' {{ __('hours ago') }}') :
                            Math.floor(diff / 86400) === 1 ? '1 {{ __('day ago') }}' : Math.floor(diff / 86400) + ' {{ __('days ago') }}'
                        "
                    ></span>
                </p>

				<div class="flex justify-between">
					<div
						class="inline-flex items-center gap-1.5 rounded-full border px-1.5 py-1 text-[12px] font-medium leading-none transition-all [&.lqd-active]:text-green-500 [&.lqd-passive]:bg-heading-foreground/5 [&.lqd-passive]:text-heading-foreground"
						:class="{
                        'lqd-active': chatbot.active,
                        'lqd-passive': !chatbot.active
                    }"
					>
						<x-tabler-check
							class="size-4"
							::class="{ hidden: !chatbot.active }"
						/>
						<span
							class="inline-flex min-h-4 items-center"
							:class="{ hidden: !chatbot.active }"
						>
                        @lang('Active')
						</span>
							<span
								class="inline-flex min-h-4 items-center"
								:class="{ hidden: chatbot.active }"
							>
							@lang('Passive')
						</span>
					</div>
					<div class="flex gap-1">
						<template x-if="chatbot?.channels?.some(c => c.channel === 'whatsapp')">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M3 21L4.65 17.2C3.38766 15.408 2.82267 13.217 3.06104 11.0381C3.29942 8.85915 4.32479 6.84211 5.94471 5.36549C7.56463 3.88887 9.66775 3.05418 11.8594 3.01807C14.051 2.98195 16.1805 3.7469 17.8482 5.16934C19.5159 6.59179 20.6071 8.57395 20.9172 10.7438C21.2272 12.9137 20.7347 15.1222 19.5321 16.9547C18.3295 18.7873 16.4994 20.118 14.3854 20.6971C12.2713 21.2762 10.0186 21.0639 8.05 20.1L3 21Z" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M9 10C9 10.1326 9.05268 10.2598 9.14645 10.3536C9.24021 10.4473 9.36739 10.5 9.5 10.5C9.63261 10.5 9.75979 10.4473 9.85355 10.3536C9.94732 10.2598 10 10.1326 10 10V9C10 8.86739 9.94732 8.74021 9.85355 8.64645C9.75979 8.55268 9.63261 8.5 9.5 8.5C9.36739 8.5 9.24021 8.55268 9.14645 8.64645C9.05268 8.74021 9 8.86739 9 9V10ZM9 10C9 11.3261 9.52678 12.5979 10.4645 13.5355C11.4021 14.4732 12.6739 15 14 15M14 15H15C15.1326 15 15.2598 14.9473 15.3536 14.8536C15.4473 14.7598 15.5 14.6326 15.5 14.5C15.5 14.3674 15.4473 14.2402 15.3536 14.1464C15.2598 14.0527 15.1326 14 15 14H14C13.8674 14 13.7402 14.0527 13.6464 14.1464C13.5527 14.2402 13.5 14.3674 13.5 14.5C13.5 14.6326 13.5527 14.7598 13.6464 14.8536C13.7402 14.9473 13.8674 15 14 15Z" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</template>
						<template x-if="chatbot?.channels?.some(c => c.channel === 'telegram')">
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M15 10L11 14L17 20L21 4L3 11L7 13L9 19L12 15" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</template>
						<span>
							<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M21 12C21 10.22 20.4722 8.47991 19.4832 6.99987C18.4943 5.51983 17.0887 4.36628 15.4442 3.68509C13.7996 3.0039 11.99 2.82567 10.2442 3.17294C8.49836 3.5202 6.89472 4.37737 5.63604 5.63604C4.37737 6.89472 3.5202 8.49836 3.17294 10.2442C2.82567 11.99 3.0039 13.7996 3.68509 15.4442C4.36628 17.0887 5.51983 18.4943 6.99987 19.4832C8.47991 20.4722 10.22 21 12 21" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M3.60156 9H20.4016" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M3.60156 15H12.0016" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M11.5778 3C9.89314 5.69961 9 8.81787 9 12C9 15.1821 9.89314 18.3004 11.5778 21" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M12.5 3C14.219 5.755 15 8.876 15 12" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								<path d="M18 21V14M18 14L21 17M18 14L15 17" stroke="#41444A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</span>
					</div>
				</div>
            </x-card>
        </template>
    </div>
</div>
