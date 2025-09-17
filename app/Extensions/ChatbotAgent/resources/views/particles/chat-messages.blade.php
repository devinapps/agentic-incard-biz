<template x-for="(message, index) in chatsList.find(c => c.id == activeChat)?.histories">
    <div
        class="lqd-ext-chatbot-history-message flex gap-2"
        data-sender="message.role"
        :class="{ 'flex-row-reverse': message.role === 'assistant' }"
    >
        <template x-if="message.role === 'user'">
            <svg
                class="flex-shrink-0"
                width="32"
                height="31"
                viewBox="0 0 32 31"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
            >
                <path
                    d="M16 0C7.44048 0 0.5 6.93943 0.5 15.5C0.5 24.0606 7.4398 31 16 31C24.5609 31 31.5 24.0606 31.5 15.5C31.5 6.93943 24.5609 0 16 0ZM16 4.63468C18.8323 4.63468 21.1274 6.93057 21.1274 9.76163C21.1274 12.5934 18.8323 14.8886 16 14.8886C13.1691 14.8886 10.874 12.5934 10.874 9.76163C10.874 6.93057 13.1691 4.63468 16 4.63468ZM15.9966 26.9475C13.1718 26.9475 10.5846 25.9187 8.58906 24.2158C8.10294 23.8012 7.82243 23.1931 7.82243 22.5552C7.82243 19.6839 10.1461 17.386 13.0179 17.386H18.9834C21.8559 17.386 24.1708 19.6839 24.1708 22.5552C24.1708 23.1938 23.8916 23.8005 23.4048 24.2151C21.41 25.9187 18.8221 26.9475 15.9966 26.9475Z"
                    fill="#879EC4"
                />
            </svg>
        </template>
        <div class="lqd-ext-chatbot-history-message-content-wrap space-y-1 lg:max-w-[75%]">
            <div
                class="lqd-ext-chatbot-history-message-content w-fit rounded-xl p-3"
                :class="{
                    'bg-heading-foreground/5 text-heading-foreground': message.role === 'user',
                    'bg-secondary text-secondary-foreground ms-auto dark:bg-zinc-700 dark:text-primary-foreground': message.role === 'assistant'
                }"
            >
                <pre
                    class="prose m-0 w-full whitespace-normal font-body text-sm text-current dark:prose-invert"
                    :class="{ 'text-heading-foreground': message.role === 'user', 'text-black': message.role === 'assistant' }"
                    x-html="getFormattedString(message.message)"
                ></pre>
            </div>
            <div class="lqd-ext-chatbot-history-message-time text-[11px] opacity-50">
                <p
                    class="m-0"
                    :class="message.role === 'user' ? '' : 'text-end'"
                >
                    <span
                        x-text="getDiffHumanTime(Math.floor((new Date() - new Date(message.created_at)) / 1000))"
                    ></span>
                    <span x-show="message.role != 'user'">, {{ __('Agent Response') }}</span>
                </p>
            </div>
        </div>
    </div>
</template>
