@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('MagicBots'))
@section('titlebar_actions', '')
@section('content')
    <div class="py-4">
        <div class="lqd-ext-chatbot-history lqd-open invisible fixed end-0 start-0 top-0 z-10 flex h-screen bg-background opacity-0 transition-all max-sm:block lg:start-[--navbar-width] [&.lqd-open]:visible [&.lqd-open]:opacity-100"
            x-data="externalChatbotHistory" @keydown.window.escape="setOpen(false)">
            <div class="lqd-ext-chatbot-history-sidebar group/history-sidebar relative w-full shrink-0 space-y-5 border-e px-6 py-6 sm:h-full sm:w-[440px] sm:overflow-y-auto"
                :class="{ 'mobile-dropdown-open': mobileDropdownOpen }">
                <div class="flex w-full items-center justify-between py-1 sm:hidden">
                    <div class="flex items-center gap-2">
                        <x-button class="text-2xs font-medium opacity-65 hover:opacity-100 sm:hidden" variant=link
                            @click.prevent="setOpen(false)">
                            <x-tabler-chevron-left class="size-4" />
                        </x-button>
                        <h3 class="mb-0">
                            @lang('Messages')
                        </h3>
                        <span
                            class="ms-auto flex size-5 items-center justify-center rounded-full bg-[#969696] text-xs text-white"
                            x-show="getAllUnreadMessages()" x-text="getAllUnreadMessages()"></span>
                    </div>
                    <div class="flex items-center gap-3">
                        <x-button variant="link" @click="mobileDropdownOpen = !mobileDropdownOpen">
                            <x-tabler-chevron-down class="ms-auto size-4" />
                        </x-button>
                        <x-dropdown.dropdown class:dropdown-dropdown="max-lg:end-auto max-lg:start-0 max-sm:-left-20"
                            anchor="end" triggerType="click" offsetY="0px">
                            <x-slot:trigger class="size-9" variant="none" title="{{ __('Filter') }}">
                                <svg class="flex-shrink-0 cursor-pointer" width="14" height="10" viewBox="0 0 14 10"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path class="fill-[#0F0F0F] dark:fill-white"
                                        d="M5.58333 9.25V7.83333H8.41667V9.25H5.58333ZM2.75 5.70833V4.29167H11.25V5.70833H2.75ZM0.625 2.16667V0.75H13.375V2.16667H0.625Z" />
                                </svg>
                            </x-slot:trigger>
                            <x-slot:dropdown class="min-w-32 text-xs font-medium">
                                <ul>
                                    <li>
                                        <a class="flex items-center justify-center gap-2 rounded-md px-3 py-2 text-center transition-colors hover:bg-primary hover:text-primary-foreground"
                                            :class="!filterConversation ? 'bg-primary text-primary-foreground' : ''"
                                            href="#" @click="fetchChats(false)">
                                            @lang('AI Agent')
                                        </a>
                                    </li>
                                    <li>
                                        <a class="hover: flex items-center justify-center gap-2 rounded-md px-3 py-2 text-center text-primary-foreground transition-colors hover:text-primary-foreground"
                                            :class="filterConversation ? 'bg-primary text-primary-foreground' : ''"
                                            href="#" @click="fetchChats(true)">
                                            @lang('Human Agent')
                                        </a>
                                    </li>
                                </ul>
                            </x-slot:dropdown>
                        </x-dropdown.dropdown>
                    </div>
                </div>
                <div
                    class="!mt-0 space-y-5 max-sm:absolute max-sm:inset-x-0 max-sm:top-full max-sm:z-2 max-sm:!m-0 max-sm:hidden max-sm:h-[60vh] max-sm:overflow-y-auto max-sm:bg-background max-sm:px-5 max-sm:pb-5 max-sm:shadow-xl max-sm:group-[&.mobile-dropdown-open]/history-sidebar:block">
                    <div class="flex items-center justify-between max-sm:hidden">
                        <div class="flex items-center gap-2">
                            <h3 class="mb-0 max-sm:hidden">
                                @lang('Messages')
                            </h3>
                            <span
                                class="ms-auto flex size-5 items-center justify-center rounded-full bg-[#969696] text-xs text-white"
                                x-show="getAllUnreadMessages()" x-text="getAllUnreadMessages()"></span>
                        </div>
                        <x-dropdown.dropdown class:dropdown-dropdown="max-lg:end-auto max-lg:start-0 max-sm:-left-20"
                            anchor="end" triggerType="click" offsetY="0px">
                            <x-slot:trigger class="size-9" variant="none" title="{{ __('Filter') }}">
                                <svg class="flex-shrink-0 cursor-pointer" width="14" height="10" viewBox="0 0 14 10"
                                    fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path class="fill-[#0F0F0F] dark:fill-white"
                                        d="M5.58333 9.25V7.83333H8.41667V9.25H5.58333ZM2.75 5.70833V4.29167H11.25V5.70833H2.75ZM0.625 2.16667V0.75H13.375V2.16667H0.625Z" />
                                </svg>
                            </x-slot:trigger>
                            <x-slot:dropdown class="min-w-32 text-xs font-medium">
                                <ul>
                                    <li>
                                        <a class="flex items-center justify-center gap-2 rounded-md px-3 py-2 text-center transition-colors hover:bg-primary hover:text-primary-foreground"
                                            :class="!filterConversation ? 'bg-primary text-primary-foreground' : ''"
                                            href="#" @click="fetchChats(false)">
                                            @lang('AI Agent')
                                        </a>
                                    </li>
                                    <li>
                                        <a class="flex items-center justify-center gap-2 rounded-md px-3 py-2 text-center transition-colors hover:bg-primary hover:text-primary-foreground"
                                            :class="filterConversation ? 'bg-primary text-primary-foreground' : ''"
                                            href="#" @click="fetchChats(true)">
                                            @lang('Human Agent')
                                        </a>
                                    </li>
                                </ul>
                            </x-slot:dropdown>
                        </x-dropdown.dropdown>
                    </div>
					@includeIf('chatbot-agent::particles.filter-input')
					<form action="#" @submit.prevent="handleSearch">

                        <x-forms.input class="w-full rounded-full ps-10 text-2xs font-medium mt-4" type="search" name="search"
                            placeholder="{{ __('Search for chats...') }}" x-ref="historySearchInput">
                            <x-tabler-search class="absolute start-4 top-1/2 size-4 -translate-y-1/2" />
                        </x-forms.input>
                    </form>
                    @include('chatbot::home.chats-history.chats-list')
                    <div class="lqd-ext-chatbot-history-load-wrap grid place-items-center font-medium text-heading-foreground"
                        x-ref="loadMoreWrap">
                        <x-button
                            class="lqd-ext-chatbot-history-load-more col-start-1 col-end-1 row-start-1 row-end-1 w-full"
                            variant="link"
                            href="{{ route('dashboard.chatbot-agent.conversations.with.paginate', ['page' => 1]) }}"
                            x-ref="loadMore" x-show="!allLoaded && !fetching">
                            {{ __('Load More...') }}
                        </x-button>
                        <span
                            class="lqd-ext-chatbot-history-loading col-start-1 col-end-1 row-start-1 row-end-1 inline-flex gap-2"
                            x-show="!allLoaded && fetching" x-ref="loading">
                            {{ __('Loading') }}
                            <x-tabler-refresh class="size-4 animate-spin" />
                        </span>
                        <span
                            class="lqd-ext-chatbot-history-all-loaded col-start-1 col-end-1 row-start-1 row-end-1 inline-flex gap-2"
                            x-ref="allLoaded" x-show="allLoaded">
                            {{ __('All Items Loaded') }}
                            <x-tabler-check class="size-4" />
                        </span>
                    </div>
                </div>
            </div>

            <div class="lqd-ext-chatbot-history-content-wrap flex grow flex-col max-sm:h-[80vh]">
                <div
                    class="lqd-ext-chatbot-history-head relative flex h-[--header-height] w-full justify-between gap-4 border-b bg-background px-8 py-5">
                    <div class="group relative items-center gap-3" x-data="{ showEdit: false }"
                        :class="{
                            'edit-mode': showEdit,
                            'flex': activeChat,
                            'hidden': !activeChat
                        }">
                        <x-forms.input
                            class="pointer-events-none border-none bg-transparent bg-none p-1 font-heading text-base font-semibold focus:ring-gray-800 group-[&.edit-mode]:pointer-events-auto"
                            type="text" name="title" placeholder="{{ __('Update Chat Title') }}"
                            placeholder="{{ __('Update Chat Title') }}" x-ref="historyChatTitleInput"
                            x-bind:value="getActiveConversationName()" />
                        <span
                            class="chat-list-item-actions absolute -end-1 top-1/2 flex -translate-y-1/2 gap-1 opacity-0 transition-opacity before:pointer-events-none before:absolute before:-inset-9 before:z-0 before:opacity-0 before:transition-all focus-within:opacity-100 group-hover:opacity-100 group-hover:before:opacity-90 group-[&.edit-mode]:opacity-100 max-md:opacity-100">
                            <button
                                class="chat-item-update-title relative z-1 flex size-7 items-center justify-center rounded-full border bg-background transition-all group-[&.edit-mode]:border-emerald-500 group-[&.edit-mode]:bg-emerald-500 group-[&.edit-mode]:text-white dark:border-primary dark:bg-primary dark:text-primary-foreground">
                                <x-tabler-pencil class="size-4 group-[&.edit-mode]:hidden" @click="showEdit=true" />
                                <x-tabler-check class="hidden size-4 group-[&.edit-mode]:block"
                                    @click="() => {showEdit=false; nameUpdate($refs.historyChatTitleInput)}" />
                            </button>
                        </span>
                    </div>
                    <div class="ms-auto flex grow items-center justify-end gap-1">
                        <div class="flex flex-nowrap items-center justify-center gap-2 px-1 py-2 max-sm:hidden">
                            <span class="text-nowrap" x-text="getActiveChatIpAddress()"></span>
                            <svg width="25" height="24" viewBox="0 0 25 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path class="dark:fill-white"
                                    d="M3.8125 12C3.8125 13.1819 4.04529 14.3522 4.49758 15.4442C4.94988 16.5361 5.61281 17.5282 6.44854 18.364C7.28427 19.1997 8.27642 19.8626 9.36835 20.3149C10.4603 20.7672 11.6306 21 12.8125 21C13.9944 21 15.1647 20.7672 16.2567 20.3149C17.3486 19.8626 18.3407 19.1997 19.1765 18.364C20.0122 17.5282 20.6751 16.5361 21.1274 15.4442C21.5797 14.3522 21.8125 13.1819 21.8125 12C21.8125 9.61305 20.8643 7.32387 19.1765 5.63604C17.4886 3.94821 15.1994 3 12.8125 3C10.4256 3 8.13637 3.94821 6.44854 5.63604C4.76071 7.32387 3.8125 9.61305 3.8125 12Z"
                                    stroke="#3A3A3A" stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path class="dark:fill-white" d="M4.41406 9H21.2141" stroke="#3A3A3A"
                                    stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path class="dark:fill-white" d="M4.41406 15H21.2141" stroke="#3A3A3A"
                                    stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path class="dark:fill-white"
                                    d="M12.3122 3C10.6275 5.69961 9.73438 8.81787 9.73438 12C9.73438 15.1821 10.6275 18.3004 12.3122 21"
                                    stroke="#3A3A3A" stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path class="dark:fill-white"
                                    d="M13.3125 3C14.9972 5.69961 15.8903 8.81787 15.8903 12C15.8903 15.1821 14.9972 18.3004 13.3125 21"
                                    stroke="#3A3A3A" stroke-opacity="0.5" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <span class="text-nowrap px-1 py-2 max-sm:hidden" x-data="{ date: new Date() }"
                            x-text="date.toLocaleString('en-US', {month: 'short', day: 'numeric', year: 'numeric'})"></span>
                        <x-dropdown.dropdown class:dropdown-dropdown="max-lg:end-auto max-lg:start-0 max-sm:-left-20"
                            class:dropdown-dropdown="max-lg:end-auto max-lg:start-0 max-sm:-left-20" anchor="end"
                            triggerType="click" offsetY="0px">
                            <x-slot:trigger class="size-9" variant="none" title="{{ __('More Options') }}">
                                <svg width="3" height="14" viewBox="0 0 3 14" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M3 12C3 12.8 2.3 13.5 1.5 13.5C0.7 13.5 0 12.8 0 12C0 11.2 0.7 10.5 1.5 10.5C2.3 10.5 3 11.2 3 12ZM3 7C3 7.8 2.3 8.5 1.5 8.5C0.7 8.5 0 7.8 0 7C0 6.2 0.7 5.5 1.5 5.5C2.3 5.5 3 6.2 3 7ZM3 2C3 2.8 2.3 3.5 1.5 3.5C0.7 3.5 0 2.8 0 2C0 1.2 0.7 0.5 1.5 0.5C2.3 0.5 3 1.2 3 2Z" />
                                </svg>
                            </x-slot:trigger>
                            <x-slot:dropdown class="min-w-32 text-xs font-medium"
                                class="min-w-32 text-xs font-medium">
                                <ul>
                                    <li>
                                        <a class="flex items-center gap-2 rounded-md px-3 py-2 transition-colors hover:bg-red-500 hover:text-white"
                                            href="#" @click.prevent="handleDelete">
                                            <x-tabler-trash class="size-4" />
                                            @lang('Delete')
                                        </a>
                                    </li>
                                </ul>
                            </x-slot:dropdown>
                        </x-dropdown.dropdown>
                    </div>
                    <div
                        class="absolute -bottom-[19px] left-[47%] z-1 rounded-full bg-white p-2 dark:bg-[#171b21] max-sm:hidden">
						<template x-if="getActiveChatbotChannel() == 'whatsapp'">
							<svg width="32" height="31" viewBox="0 0 32 31" fill="none" xmlns="http://www.w3.org/2000/svg">
								<rect x="0.5" width="31" height="31" rx="15.5" fill="#25D366"/>
								<path d="M21.7891 9.63672C23.3477 11.1953 24.3125 13.2363 24.3125 15.4629C24.3125 19.9902 20.5273 23.7012 15.9629 23.7012C14.5898 23.7012 13.2539 23.3301 12.0293 22.6992L7.6875 23.8125L8.83789 19.5449C8.13281 18.3203 7.72461 16.9102 7.72461 15.4258C7.72461 10.8984 11.4355 7.1875 15.9629 7.1875C18.1895 7.1875 20.2676 8.07812 21.7891 9.63672ZM15.9629 22.291C19.748 22.291 22.9023 19.2109 22.9023 15.4629C22.9023 13.6074 22.123 11.9004 20.8242 10.6016C19.5254 9.30273 17.8184 8.59766 16 8.59766C12.2148 8.59766 9.13477 11.6777 9.13477 15.4258C9.13477 16.7246 9.50586 17.9863 10.1738 19.0996L10.3594 19.3594L9.6543 21.8828L12.252 21.1777L12.4746 21.3262C13.5508 21.957 14.7383 22.291 15.9629 22.291ZM19.748 17.1699C19.9336 17.2812 20.082 17.3184 20.1191 17.4297C20.1934 17.5039 20.1934 17.9121 20.0078 18.3945C19.8223 18.877 19.0059 19.3223 18.6348 19.3594C17.9668 19.4707 17.4473 19.4336 16.1484 18.8398C14.0703 17.9492 12.7344 15.8711 12.623 15.7598C12.5117 15.6113 11.8066 14.6465 11.8066 13.6074C11.8066 12.6055 12.3262 12.123 12.5117 11.9004C12.6973 11.6777 12.9199 11.6406 13.0684 11.6406C13.1797 11.6406 13.3281 11.6406 13.4395 11.6406C13.5879 11.6406 13.7363 11.6035 13.9219 12.0117C14.0703 12.4199 14.5156 13.4219 14.5527 13.5332C14.5898 13.6445 14.627 13.7559 14.5527 13.9043C14.1816 14.6836 13.7363 14.6465 13.959 15.0176C14.7754 16.3906 15.5547 16.873 16.7793 17.4668C16.9648 17.5781 17.0762 17.541 17.2246 17.4297C17.3359 17.2812 17.7441 16.7988 17.8555 16.6133C18.0039 16.3906 18.1523 16.4277 18.3379 16.502C18.5234 16.5762 19.5254 17.0586 19.748 17.1699Z" fill="white"/>
							</svg>
						</template>
						<template  x-if="getActiveChatbotChannel() == 'frame'">
							<svg class="flex-shrink-0" width="32" height="31" viewBox="0 0 32 31" fill="none"
								 xmlns="http://www.w3.org/2000/svg">
								<path
									d="M16 0C7.44048 0 0.5 6.93943 0.5 15.5C0.5 24.0606 7.4398 31 16 31C24.5609 31 31.5 24.0606 31.5 15.5C31.5 6.93943 24.5609 0 16 0ZM16 4.63468C18.8323 4.63468 21.1274 6.93057 21.1274 9.76163C21.1274 12.5934 18.8323 14.8886 16 14.8886C13.1691 14.8886 10.874 12.5934 10.874 9.76163C10.874 6.93057 13.1691 4.63468 16 4.63468ZM15.9966 26.9475C13.1718 26.9475 10.5846 25.9187 8.58906 24.2158C8.10294 23.8012 7.82243 23.1931 7.82243 22.5552C7.82243 19.6839 10.1461 17.386 13.0179 17.386H18.9834C21.8559 17.386 24.1708 19.6839 24.1708 22.5552C24.1708 23.1938 23.8916 23.8005 23.4048 24.2151C21.41 25.9187 18.8221 26.9475 15.9966 26.9475Z"
									:fill="getActiveChatColor()"
								/>
							</svg>
						</template>
                    </div>
                </div>

                <div class="lqd-ext-chatbot-history-messages-wrap relative grow overflow-hidden">
                    <div class="lqd-ext-chatbot-history-messages relative flex h-full flex-col gap-2 overflow-y-auto pt-10"
                        x-ref="historyMessages">
                        <div class="mt-auto space-y-2 px-10">
                            @include('chatbot-agent::particles.chat-messages')
                        </div>
                        <form
                            class="lqd-chat-form sticky bottom-0 flex w-full items-end gap-3 self-end rounded-ee-[inherit] bg-background/95 p-8 py-6 backdrop-blur-lg backdrop-saturate-150 max-md:items-end max-md:p-4 max-sm:p-3"
                            id="chat_form" @submit.prevent="onSendMessage" x-transition>
                            <div
                                class="lqd-chat-form-inputs-container flex min-h-[52px] w-full flex-col rounded-[26px] border border-input-border max-md:min-h-[45px]">
                                <hr class="split_line border-1 mb-2.5 hidden w-full">
                                <div class="relative flex grow items-center">
                                    <div class="lqd-input-container relative w-full">
                                        <textarea
                                            class="lqd-input lqd-input-size-none peer m-0 block w-full rounded-lg border border-none border-input-border bg-transparent px-4 py-3 text-base text-heading-foreground ring-offset-0 transition-colors focus:border-secondary focus:outline-none focus:outline-0 focus:ring-0 focus:ring-secondary dark:focus:ring-foreground/10 max-md:max-h-[200px] max-md:text-[16px] sm:text-2xs"
                                            id="message" rows="1" name="message" @keydown.enter.prevent="onMessageFieldHitEnter"
                                            @input="onMessageFieldInput" @input.throttle.50ms="$el.scrollTop = $el.scrollHeight" x-ref="message"
                                            placeholder="Type a message"></textarea>
                                    </div>
                                </div>
                            </div>
                            <button
                                class="lqd-btn lqd-btn-primary lqd-btn-size-none lqd-btn-hover-none lqd-chat-send-btn group inline-flex aspect-square size-[52px] shrink-0 items-center justify-center gap-1.5 rounded-full bg-primary text-xs font-medium text-primary-foreground transition-all hover:-translate-y-0.5 hover:bg-primary/90 hover:shadow-xl hover:shadow-primary/10 focus-visible:bg-primary/90 focus-visible:shadow-primary/10 disabled:pointer-events-none disabled:bg-foreground/10 disabled:text-foreground/35 max-md:size-10 max-md:min-h-[45px] max-md:min-w-[45px]"
                                type="submit" x-ref="submitBtn" :disabled="!$refs.message.value.trim()">
                                <svg class="size-6 rtl:-scale-x-100" stroke-width="1.5"
                                    xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M4.698 4.034l16.302 7.966l-16.302 7.966a.503 .503 0 0 1 -.546 -.124a.555 .555 0 0 1 -.12 -.568l2.468 -7.274l-2.468 -7.274a.555 .555 0 0 1 .12 -.568a.503 .503 0 0 1 .546 -.124z">
                                    </path>
                                    <path d="M6.5 12h14.5"></path>
                                </svg>
                            </button>
                        </form>
                    </div>

                </div>
                <div class="absolute inset-0 z-50 grid place-items-center backdrop-blur-md transition-all"
                    :class="{ 'opacity-0': !firstLoading, 'invisible': !firstLoading }">
                    <x-tabler-loader-2 class="size-8 animate-spin text-primary" />
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/markdown-it.min.js') }}"></script>
    @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-agent'))
        <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    @endif

    <script src="https://cdn.ably.com/lib/ably.min-1.js" type="text/javascript"></script>
    <script>
        (() => {
            document.addEventListener('alpine:init', () => {
                Alpine.data('externalChatbotHistory', () => ({
					chatbot_channel: 'all',
					humanAgentFilter: true,
                    open: true,
                    chatsList: [],
                    activeChat: null,
                    activeChatIpAddress: null,
                    fetching: false,
                    histories: [],
                    lastTimeFetch: '{{ now()->timestamp }}',
                    currentPage: 1,
                    allLoaded: false,
                    /**
                     * @type {IntersectionObserver}
                     */
                    loadMoreIO: null,
                    mobileDropdownOpen: false,
                    firstLoading: true,
                    filterConversation: true,
                    messageTime: null,
                    init() {
                        this.onSendMessage = this.onSendMessage.bind(this);
                        this.setActiveChat = this.setActiveChat.bind(this);

                        this.fetchChats();

                        this.initAbly();

                        Alpine.store('externalChatbotHistory', this);
                    },
                    setupLoadMoreIO() {
                        const load = async (entry) => {
                            this.currentPage += 1;
                            this.$refs.loadMore.href = this.$refs.loadMore.href.replace(
                                /page=\d+/, `page=${this.currentPage}`);

                            await this.fetchChats();
                        };

                        this.loadMoreIO = new IntersectionObserver(async ([entry], observer) => {
                            if (entry.isIntersecting && !this.fetching && !this
                                .allLoaded) {
                                await load();

                                if (entry.isIntersecting && !this.fetching && !this
                                    .allLoaded) {
                                    await load();
                                }
                            }
                        });

                        this.loadMoreIO.observe(this.$refs.loadMoreWrap);
                    },
                    async nameUpdate(element) {
						@if($app_is_demo)
							toastr.error('This feature is disabled in Demo version.');
							return;
						@endif
                        //TODO: Implement name update logic new design for MOHSEN
                        let conversation_name = element.value.trim();

                        if (conversation_name == '') {
                            alert("Conversation Name cant' be empty!");
                            return;
                        }

                        const activeChatItem = this.chatsList.find(element => element.id == this.activeChat);

                        activeChatItem.conversation_name = conversation_name;

                        const res = await fetch(
                            '{{ route('dashboard.chatbot-agent.conversations.name.update') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    conversation_id: this.activeChat,
                                    conversation_name: conversation_name,
                                }),
                            });
                    },
                    async initAbly() {
                        const realtime = new Ably.Realtime.Promise(
                            "{{ setting('ably_public_key') }}");

                        const channel = realtime.channels.get(
                            "panel-conversation-{{ \Illuminate\Support\Facades\Auth::id() }}"
                        );

                        await channel.subscribe("conversation", (message) => {
                            let chatbotConversation = message.data.chatbotConversation;

							chatbotConversation.histories = this.fetchHistories(chatbotConversation.id);

							const index = this.chatsList.findIndex(chat => chat.id === chatbotConversation.id);

							if (index !== -1) {
                                this.chatsList.splice(index, 1);
                                this.chatsList = [chatbotConversation, ...this.chatsList
                                ];
                            } else {
                                this.chatsList = [chatbotConversation, ...this.chatsList];
                                this.activeChat = chatbotConversation.id;
                            }

                            this.scrollMessagesToBottom();
                        });
                    },
                    async onSendMessage() {
						@if($app_is_demo)
							toastr.error('This feature is disabled in Demo version.');
							return;
						@endif
                        const messageString = this.$refs.message.value.trim();

                        if (!messageString) return;

                        this.$refs.message.value = '';
                        this.$refs.submitBtn.setAttribute('disabled', 'disabled');

                        const newUserMessage = {
                            id: new Date().getTime(),
                            message: messageString,
                            role: 'assistant',
                            user: true,
                            created_at: new Date().toLocaleString()
                        };

                        let chatIndex = this.chatsList.findIndex(chat => chat.id == this.activeChat);

						if (chatIndex !== -1) {
							const histories = await Promise.resolve(this.chatsList[chatIndex].histories); // resolve et
							if (!Array.isArray(histories)) {
								this.chatsList[chatIndex].histories = [];
							} else {
								this.chatsList[chatIndex].histories = histories;
							}

							this.chatsList[chatIndex].histories.push(newUserMessage);
						}

                        this.scrollMessagesToBottom();

                        const res = await fetch(
                            '{{ route('dashboard.chatbot-agent.history') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    conversation_id: this.activeChat,
                                    message: messageString,
                                }),
                            });
                    },
                    scrollMessagesToBottom(smooth = false) {
                        this.$nextTick(() => {
                            this.$refs.historyMessages.scrollTo({
                                top: this.$refs.historyMessages.scrollHeight,
                                behavior: smooth ? 'smooth' : 'auto'
                            });
                        })
                    },
                    onMessageFieldHitEnter(event) {
                        if (!event.shiftKey) {
                            this.onSendMessage();
                        } else {
                            event.target.value += '\n';
                            event.target.scrollTop = event.target.scrollHeight
                        };
                    },
                    onMessageFieldInput(event) {
                        const messageString = this.$refs.message.value.trim();

                        if (messageString) {
                            this.$refs.submitBtn.removeAttribute('disabled');
                        } else {
                            this.$refs.submitBtn.setAttribute('disabled', 'disabled');
                        }
                    },
                    async setOpen(open) {
                        if (this.open === open) return;

                        const topNoticeBar = document.querySelector('.top-notice-bar');
                        const navbar = document.querySelector('.lqd-navbar');
                        const pageContentWrap = document.querySelector(
                            '.lqd-page-content-wrap');
                        const navbarExpander = document.querySelector('.lqd-navbar-expander');

                        this.open = open;

                        document.documentElement.style.overflow = this.open ? 'hidden' : '';

                        if (navbar) {
                            navbar.style.position = this.open ? 'fixed' : '';
                        }

                        if (pageContentWrap) {
                            pageContentWrap.style.paddingInlineStart = this.open ?
                                'var(--navbar-width)' : '';
                        }

                        if (topNoticeBar) {
                            topNoticeBar.style.visibility = this.open ? 'hidden' : '';
                        }

                        if (navbarExpander) {
                            navbarExpander.style.visibility = this.open ? 'hidden' : '';
                            navbarExpander.style.opacity = this.open ? 0 : 1;
                        }

                        if (this.open) {
                            await this.fetchChats();
                            this.setupLoadMoreIO();
                        }
                    },
                    async fetchHistories(id) {
						const res = await fetch(
                            '{{ route('dashboard.chatbot-agent.history') }}?conversation_id=' + id, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                            });

						const data = await res.json();

						return data.data;
                    },
                    async fetchChats(filter = null) {
                        if (this.allLoaded && filter == null) {
                            return this.fetching = false;
                        }

                        if (filter != null) {
							this.humanAgentFilter = filter;
                            this.chatsList = [];
                            this.firstLoading = true;
                            this.filterConversation = filter;
                            this.activeChat = null;
                        }

                        this.fetching = true;

						console.log(this.filterConversation)

                        const res = await fetch(this.$refs.loadMore.href + `&agentFilter=${this.filterConversation}&chatbot_channel=${this.chatbot_channel}`, {
                                method: 'GET',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                            });
                        const data = await res.json();
                        const {
                            data: conversations
                        } = data;

                        if (!res.ok || !conversations) {
                            if (data.message) {
                                toastr.error(data.message);
                            }
                            return;
                        }

                        this.lastTimeFetch = new Date().getTime();

                        this.chatsList.push(...conversations);

                        if (this.currentPage >= data.meta.last_page) {
                            this.allLoaded = true;
                        }

                        if (!this.activeChat && conversations.length) {
                            this.activeChat = conversations[0].id;
                            this.activeChatIpAddress = this.chatsList.find(value => value.id ==
                                this.activeChat)?.ip_address;

                            this.histories = conversations[0].histories || [];

                            // this.fetchHistories(conversations[0].id)
                        }

                        this.fetching = false;
                        this.firstLoading = false;

                        this.scrollMessagesToBottom();
                    },
                    async handleSearch() {
                        const query = this.$refs.historySearchInput?.value?.trim();
                        this.fetching = true;

                        const res = await fetch(
                            '{{ route('dashboard.chatbot-agent.conversations.search') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    search: query,
                                }),
                            });
                        const data = await res.json();
                        const {
                            data: conversations
                        } = data;

                        if (!res.ok || !conversations) {
                            if (data.message) {
                                toastr.error(data.message);
                            }
                            return;
                        }

                        this.lastTimeFetch = new Date().getTime();
                        this.chatsList = conversations;
                        this.allLoaded = true;

                        if (conversations.length) {
                            this.activeChat = conversations[0].id;
                            this.histories = conversations[0].histories || [];
                            // this.fetchHistories(conversations[0].id)
                        } else {
                            this.activeChat = null;
                        }

                        this.fetching = false;
                        this.scrollMessagesToBottom();
                    },
                    async handleChangeTitle() {
                        // TODO: Implement change title logic
                    },
                    async handleDelete() {
                        if (!this.activeChat) {
                            alert('Please select the conversation which you want delete');
                            return;
                        }

                        // TODO: Implement delete logic
                        if (!confirm('Do you want delete this conversation history?')) {
                            return;
                        }

                        const res = await fetch(
                            '{{ route('dashboard.chatbot-agent.destroy') }}', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    conversation_id: this.activeChat,
                                }),
                            });
                        const responseData = await res.json();

                        if (responseData.status == 'success') {
                            this.chatsList = this.chatsList.filter((element) => {
                                return element.id != this.activeChat;
                            })

                            this.activeChat = this.chatsList[0]?.id;
                        }
                    },
                    async handleSummarize() {
                        // TODO: Implement delete logic
                    },
                    async setActiveChat(event) {
                        const triggerEl = event.currentTarget;
                        const chatId = triggerEl.getAttribute('data-id');
                        const triggerParent = triggerEl.closest('li');

                        if (!chatId) return;

						let histories = await this.fetchHistories(chatId);

                        if (this.chatsList.length) {
                            this.chatsList = this.chatsList.map(chat => {
                                if (chat.id == chatId) {

									chat.histories = histories.map(history => {
                                        if (history.role == 'user' && history
                                            .read_at == null) {
                                            history.read_at = new Date()
                                                .toISOString();
                                        }
                                        return history;
                                    });

                                    chat.lastMessage.read_at = new Date().toISOString();
                                }
                                return chat;
                            });
                        }


						// console.log(await this.fetchHistories(chatId))


						this.activeChat = chatId;
                        this.activeChatIpAddress = this.chatsList.find(value => value.id == this.activeChat)?.ip_address;
                        this.mobileDropdownOpen = false;
                    },
                    getFormattedString(string) {
                        if (!('markdownit' in window) || !string) return string;

                        string
                            .replace(/>(\s*\r?\n\s*)</g, '><')
                            .replace(/\n(?!.*\n)/, '');

                        const renderer = window.markdownit({
                            breaks: true,
                            highlight: (str, lang) => {
                                const language = lang && lang !== '' ? lang : 'md';
                                // const codeString = str.replace(/&/g, '&amp;').replace(/</g, '&lt;');
                                const codeString = str;

                                if (Prism.languages[language]) {
                                    const highlighted = Prism.highlight(codeString,
                                        Prism.languages[language], language);
                                    return `<pre class="language-${language}"><code data-lang="${language}" class="language-${language}">${highlighted}</code></pre>`;
                                }

                                return codeString;
                            }
                        });

                        renderer.use(function(md) {
                            md.core.ruler.after('inline', 'convert_links', function(state) {
                                state.tokens.forEach(function(blockToken) {
                                    if (blockToken.type !== 'inline')
                                        return;
                                    blockToken.children.forEach(function(
                                        token, idx) {
                                        const {
                                            content
                                        } = token;
                                        if (content.includes(
                                                '<a ')) {
                                            const linkRegex =
                                                /(.*)(<a\s+[^>]*\s+href="([^"]+)"[^>]*>([^<]*)<\/a>?)(.*)/;
                                            const linkMatch =
                                                content.match(
                                                    linkRegex);

                                            if (linkMatch) {
                                                const [, before, ,
                                                    href, text,
                                                    after
                                                ] = linkMatch;

                                                const beforeToken =
                                                    new state.Token(
                                                        'text', '',
                                                        0);
                                                beforeToken
                                                    .content =
                                                    before;

                                                const newToken =
                                                    new state.Token(
                                                        'link_open',
                                                        'a', 1);
                                                newToken.attrs = [
                                                    ['href',
                                                        href
                                                    ],
                                                    ['target',
                                                        '_blank'
                                                    ]
                                                ];
                                                const textToken =
                                                    new state.Token(
                                                        'text', '',
                                                        0);
                                                textToken.content =
                                                    text;
                                                const closingToken =
                                                    new state.Token(
                                                        'link_close',
                                                        'a', -1);

                                                const afterToken =
                                                    new state.Token(
                                                        'text', '',
                                                        0);
                                                afterToken.content =
                                                    after;

                                                blockToken.children
                                                    .splice(idx, 1,
                                                        beforeToken,
                                                        newToken,
                                                        textToken,
                                                        closingToken,
                                                        afterToken);
                                            }
                                        }
                                    });
                                });
                            });
                        });

                        return renderer.render(renderer.utils.unescapeAll(string));
                    },
                    getUnreadMessages(chatItem) {
                        let chat = chatItem;
						try {
							if (typeof(chatItem) == 'number' || typeof(chatItem) == 'string') {
								chat = this.chatsList?.find((element) => {
									return element.id == chatItem
								});

								if (chatItem == this.activeChat) {
									chat?.histories?.forEach(element => {
										element.read_at = new Date();
									});
									// this.fetchHistories(this.activeChat);
									return 0;
								}
							} else if (chat.id == this.activeChat) {
								chat?.histories?.forEach(element => {
									element.read_at = new Date();
								});
								// this.fetchHistories(this.activeChat);
								return 0;
							}

							return chat?.histories?.filter((history) => {
								return history.role == 'user' && history.read_at == null
							})?.length;
						} catch (e) {
							return 0
						}
                    },
                    getAllUnreadMessages() {
                        const unreadMessages = this.chatsList?.reduce((previousValue, element) => {
                            return previousValue + this.getUnreadMessages(element) ?? 0;
                        }, 0);

                        return unreadMessages;
                    },
                    getActiveConversationName() {
                        return this.chatsList?.find(element => element.id == this.activeChat)
                            ?.conversation_name || '';
                    },
                    getActiveChatIpAddress() {
                        return this.chatsList.find((value) => {
                            return value.id == this.activeChat
                        })?.ip_address;
                    },
					getActiveChatbotChannel() {
						return this.chatsList.find((value) => {
							return value.id == this.activeChat
						})?.chatbot_channel ?? 'frame';
					},
					getActiveChatColor() {
						return this.chatsList.find((value) => {
							return value.id == this.activeChat
						})?.color ?? '#e633ec';
					},
                    getDiffHumanTime(diff) {
                        return diff < 60 ? " {{ __('Just now') }}" :
                            diff < 3600 ? (Math.floor(diff / 60) === 1 ?
                                "1 {{ __('minute ago') }}" : Math.floor(diff / 60) +
                                " {{ __('minutes ago') }}") :
                            diff < 86400 ? (Math.floor(diff / 3600) === 1 ?
                                "1 {{ __('hour ago') }}" : Math.floor(diff / 3600) +
                                " {{ __('hours ago') }}") :
                            Math.floor(diff / 86400) === 1 ? "1 {{ __('day ago') }}" : Math.floor(
                                diff / 86400) + " {{ __('days ago') }}"
                    },
                    getShortDiffHumanTime(diff) {
                        return diff < 60 ? '{{ __('Just now') }}' :
                            diff < 3600 ? Math.floor(diff / 60) + '{{ __('m') }}' :
                            diff < 86400 ? Math.floor(diff / 3600) + '{{ __('h') }}' :
                            Math.floor(diff / 86400) + '{{ __('d') }}'
                    }
                }));
            });
        })();
    </script>
@endpush
