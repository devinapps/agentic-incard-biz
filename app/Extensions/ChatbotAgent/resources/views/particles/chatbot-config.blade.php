<div>
    <x-forms.input
        class:label="text-heading-foreground"
        label="{{ __('Interaction type') }}"
        name="interaction_type"
        size="lg"
        type="select"
        x-model="activeChatbot.interaction_type"
    >
        <option value="{{ \App\Extensions\Chatbot\System\Enums\InteractionType::SMART_SWITCH->value }}">@lang('AI & Live Chat')</option>
        <option value="{{ \App\Extensions\Chatbot\System\Enums\InteractionType::AUTOMATIC_RESPONSE->value }}">@lang('Only AI')</option>
        <option value="{{ \App\Extensions\Chatbot\System\Enums\InteractionType::HUMAN_SUPPORT->value }}">@lang('Only Live Chat')</option>
    </x-forms.input>
    <template
        x-for="(error, index) in formErrors.interaction_type"
        :key="'error-' + index"
    >
        <div class="mt-2 text-2xs/5 font-medium text-red-500">
            <p x-text="error"></p>
        </div>
    </template>
</div>

<div>
    <x-forms.input
        class:label="text-heading-foreground"
        label="{{ __('Connect Message') }}"
        placeholder="{{ __('I’ve forwarded your request to a human agent. An agent will connect with you as soon as possible.') }}"
        name="connect_message"
        size="lg"
        x-model="activeChatbot.connect_message"
        @input.throttle.250ms="externalChatbot && externalChatbot.toggleWindowState('close')"
    />

    <template
        x-for="(error, index) in formErrors.connect_message"
        :key="'error-' + index"
    >
        <div class="mt-2 text-2xs/5 font-medium text-red-500">
            <p x-text="error"></p>
        </div>
    </template>
</div>
