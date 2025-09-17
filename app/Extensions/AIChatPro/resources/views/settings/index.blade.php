@php
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
@endphp

@extends('panel.layout.settings')
@section('title', __('AI Chat Pro Settings'))
@section('titlebar_actions', '')

@section('additional_css')
@endsection

@section('settings')
    <form
        method="post"
        enctype="multipart/form-data"
        action=""
    >
        @csrf
        <x-form-step
            step="1"
            label="{{ __('AI Chat Pro Models Features') }}"
        >
        </x-form-step>
        <x-card
            class="mb-2 max-md:text-center"
            szie="lg"
        >
            @if (setting('default_ai_engine') === \App\Domains\Engine\Enums\EngineEnum::OPEN_AI->slug())
                <x-form.group class="grid grid-cols-2 gap-1">
                    <!-- Image ability -->
                    <x-form.checkbox
                        class="border-input rounded-input border !px-2.5 !py-3"
                        name="ai_chat_pro_image_generation_feature"
                        label="{{ __('Image Generation') }}"
                        checked="{{ (bool) setting('ai_chat_pro_image_generation_feature', '0') }}"
                        tooltip="{{ __('AI Chat Pro OpenAI models can generate images.') }}"
                    />
                    <!-- Video ability -->
                    {{-- <x-form.checkbox --}}
                    {{-- 	class="border-input rounded-input border !px-2.5 !py-3" --}}
                    {{-- 	value="ai_chat_pro_video_generation_feature" --}}
                    {{-- 	label="{{ __('Video Generation') }}" --}}
                    {{-- 	tooltip="{{ __('AI Chat Pro OpenAI models can generate videos.') }}" --}}
                    {{-- /> --}}
                    <!-- Audio ability -->
                    {{-- <x-form.checkbox --}}
                    {{-- 	class="border-input rounded-input border !px-2.5 !py-3" --}}
                    {{-- 	value="ai_chat_pro_voice_generation_feature" --}}
                    {{-- 	label="{{ __('Audio Generation') }}" --}}
                    {{-- 	tooltip="{{ __('AI Chat Pro OpenAI models can generate audio.') }}" --}}
                    {{-- /> --}}
                </x-form.group>
            @else
                <x-alert
                    class="my-2"
                    type="warning"
                >
                    @lang('AI Chat Pro features are only available for OpenAI models. Please select OpenAI as the default engine from the settings page to activate these features.')
                </x-alert>
            @endif
        </x-card>

        <x-form-step
            step="2"
            label="{{ __('AI Chat Pro Settings') }}"
        >
        </x-form-step>
        <x-card
            class="mb-2 max-md:text-center"
            szie="lg"
        >
            <div class="col-md-12">
                <label class="form-label">{{ __('AI Chat Pro Display Type') }}</label>
                <x-alert class="my-2">
                    <p>
                        @lang('If you want to make AI Chat Pro as a separate menu option choose menu option, and if you want \'AI Chat\' to be in pro edition then choose AI Chat option. If you want both then choose both option.')
                    </p>
                </x-alert>
                <select
                    class="form-select"
                    id="ai_chat_display_type"
                    name="ai_chat_display_type"
                >
                    <option
                        value="menu"
                        {{ setting('ai_chat_display_type', 'menu') === 'menu' ? 'selected' : '' }}
                    >
                        {{ __('Dashboard Side Menu') }}
                    </option>
                    <option
                        value="ai_chat"
                        {{ setting('ai_chat_display_type', 'menu') === 'ai_chat' ? 'selected' : '' }}
                    >
                        {{ __('AI Chat') }}
                    </option>
                    <option
                        value="frontend"
                        {{ setting('ai_chat_display_type', 'menu') === 'frontend' ? 'selected' : '' }}
                    >
                        {{ __('Site Front End') }}
                    </option>
                    <option
                        value="both_fm"
                        {{ setting('ai_chat_display_type', 'menu') === 'both_fm' ? 'selected' : '' }}
                    >
                        {{ __('Site Front End & Dashboard Side Menu') }}
                    </option>
                </select>
            </div>
        </x-card>
        <x-card
            class="mb-2 max-md:text-center"
            szie="lg"
        >
            <div class="col-md-12">
                <label class="form-label">{{ __('Daily Message Limit Count for Guest User') }}</label>
                <x-alert class="my-2">
                    <p>
                        @lang('This is the daily message limit count for unlogged in users.')
                    </p>
                </x-alert>
                <input
                    class="form-control"
                    type="number"
                    name="guest_user_daily_message_limit"
                    value="{{ setting('guest_user_daily_message_limit', '10') }}"
                />
            </div>
        </x-card>
        @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered('canvas'))
            <x-form.group class="mb-2 flex w-full gap-1">
                <!-- Canvas ability -->
                <x-form.checkbox
                    class="border-input rounded-input border !px-2.5 !py-3"
                    name="ai_chat_pro_canvas"
                    label="{{ __('Canvas Status') }}"
                    checked="{{ (bool) setting('ai_chat_pro_canvas', '1') }}"
                    tooltip="{{ __('AI Chat Pro enable Canvas.') }}"
                />
            </x-form.group>
        @endif

		@includeIf('chat-pro-temp-chat::settings.allow-button')

        <x-card
            class="mb-2 max-md:text-center"
            szie="lg"
        >
            <div class="col-md-12">
                <x-forms.input
                    class="form-control"
                    label="{{ __('Bottom Text') }}"
                    type="text"
                    name="guest_user_bottom_text"
                    tooltip="{{ __('This is the text that will be shown at the bottom of the chat for guest users.') }}"
                    value="{{ setting('guest_user_bottom_text', 'Login to save your current session. © MagicAI 2025. All rights reserved.') }}"
                />
            </div>
        </x-card>
        <x-form-step
            step="3"
            label="{{ __('Scrolling (suggestion) Texts/Prompt') }}"
        >
            <x-button
                class="add-more ms-auto inline-flex size-8 items-center justify-center rounded-full bg-background text-foreground transition-all"
                size="none"
                type="button"
                variant="ghost-shadow"
            >
                <x-tabler-plus class="size-5" />
            </x-button>
        </x-form-step>
        <x-card class:body="flex flex-col gap-5">
            @forelse(json_decode(setting('ai_chat_pro_suggestions', $example_prompts_json), false, 512, JSON_THROW_ON_ERROR) as $suggestion)
                <x-card
                    class="user-input-group relative"
                    class:body="flex flex-col gap-5"
                    data-input-name="{{ $suggestion?->name }}"
                    data-inputs-id="{{ $loop->index + 1 }}"
                >
                    <x-forms.input
                        class="input_name"
                        size="lg"
                        label="{{ __('Name') }}"
                        name="input_name[]"
                        tooltip="{{ __('The primary text will shown in the chat box.') }}"
                        value="{{ $suggestion?->name }}"
                    />

                    <x-forms.input
                        class="input_prompt"
                        type="textarea"
                        size="lg"
                        rows="1"
                        name="input_prompt[]"
                        label="{{ __('Prompt') }}"
                        tooltip="{{ __('The prompt will be shown in the input box.') }}"
                    >{{ $suggestion?->prompt }}</x-forms.input>

                    <x-button
                        class="remove-inputs-group absolute -end-3 -top-3 size-6"
                        size="none"
                        variant="danger"
                        type="button"
                    >
                        <x-tabler-minus class="size-4" />
                    </x-button>
                </x-card>
            @empty
                <x-card
                    class="user-input-group relative"
                    class:body="flex flex-col gap-5"
                    data-inputs-id="1"
                >
                    <x-forms.input
                        class="input_name"
                        size="lg"
                        name="input_name[]"
                        label="{{ __('Name') }}"
                        tooltip="{{ __('The primary text will shown in the chat box.') }}"
                    />

                    <x-forms.input
                        class="input_prompt"
                        type="textarea"
                        size="lg"
                        rows="3"
                        name="input_prompt[]"
                        label="{{ __('Prompt') }}"
                        tooltip="{{ __('The prompt will be shown in the input box.') }}"
                    ></x-forms.input>

                    <x-button
                        class="remove-inputs-group absolute -end-3 -top-3 size-6"
                        size="none"
                        variant="danger"
                        type="button"
                    >
                        <x-tabler-minus class="size-4" />
                    </x-button>
                </x-card>
            @endforelse
            <div class="add-more-placeholder"></div>
        </x-card>

        <button class="btn btn-primary mt-5 w-full">
            {{ __('Save') }}
        </button>
    </form>

    <template id="user-input-company">
        <x-card
            class="user-input-group relative"
            class:body="flex flex-col gap-5"
            data-inputs-id="1"
        >
            <x-forms.input
                class="input_name"
                size="lg"
                name="input_name[]"
                label="{{ __('Name') }}"
                tooltip="{{ __('The primary text will shown in the chat box.') }}"
            />

            <x-forms.input
                class="input_prompt"
                type="textarea"
                size="lg"
                rows="3"
                name="input_prompt[]"
                label="{{ __('Prompt') }}"
                tooltip="{{ __('The prompt will be shown in the input box.') }}"
            ></x-forms.input>

            <x-button
                class="remove-inputs-group absolute -end-3 -top-3 size-6"
                size="none"
                variant="danger"
                type="button"
            >
                <x-tabler-minus class="size-4" />
            </x-button>
        </x-card>
    </template>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            "use strict";
            const slugify = str =>
                `**${str.toLowerCase().trim().replace(/[^\w\s-]/g, '').replace(/[\s_-]+/g, '-').replace(/^-+|-+$/g, '')}** `;
            /** @type {HTMLTemplateElement} */
            const userInputTemplate = document.querySelector('#user-input-company');
            const addMorePlaceholder = document.querySelector('.add-more-placeholder');
            let currentInputGroupts = document.querySelectorAll('.user-input-group');
            let lastInputsParent = [...currentInputGroupts].at(-1);
            let lastInpusGroupId = lastInputsParent ? parseInt(lastInputsParent.getAttribute('data-inputs-id'),
                10) : 0;

            $(".add-more").click(function() {
                const button = this;
                const currentInputs = document.querySelectorAll(
                    '.input_name, .input_prompt');
                let anInputIsEmpty = false;
                currentInputs.forEach(input => {
                    const {
                        value
                    } = input;
                    if (!value || value.length === 0 || value.replace(/\s/g, '') === '') {
                        return anInputIsEmpty = true;
                    }
                });
                if (anInputIsEmpty) {
                    return toastr.error('Please fill all fields in User Group Input areas.');
                }
                const newInputsMarkup = userInputTemplate.content.cloneNode(true);
                const newInputsWrapper = newInputsMarkup.firstElementChild;
                newInputsWrapper.dataset.inputsId = lastInpusGroupId + 1;
                addMorePlaceholder.before(newInputsMarkup);
                currentInputGroupts = document.querySelectorAll('.user-input-group');
                lastInputsParent = [...currentInputGroupts].at(-1);
                if (currentInputGroupts.length > 1) {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.removeAttribute(
                        'disabled'));
                }
                lastInpusGroupId++;
                const timeout = setTimeout(() => {
                    newInputsWrapper.querySelector('.input_name').focus();
                    clearTimeout(timeout);
                }, 100);
                return;
            });

            $("body").on("click", ".remove-inputs-group", function() {
                const button = $(this);
                const parent = button.closest('.user-input-group');
                const inputsId = parent.attr('data-inputs-id');

                parent.remove();

                currentInputGroupts = document.querySelectorAll('.user-input-group');
                if (currentInputGroupts.length === 1) {
                    document.querySelectorAll('.remove-inputs-group').forEach(el => el.setAttribute('disabled', true));
                }
            });

            $('body').on('input', '.input_name', ev => {
                const input = ev.currentTarget;
                const parent = input.closest('.user-input-group');
                const parentId = parent.getAttribute('data-inputs-id');
                const inputName = slugify(input.value);
                let button = document.querySelector(`button[data-inputs-id="${parentId}"]`);

                if (!button) {
                    button = document.createElement('button');
                    button.className =
                        'bg-[#EFEFEF] text-black cursor-pointer py-[0.15rem] px-[0.5rem] border-none rounded-full transition-all duration-300 hover:bg-black hover:!text-white';
                    button.dataset.inputsId = parentId;
                    button.type = 'button';
                }

                parent.dataset.inputName = inputName;
                button.dataset.inputName = inputName;
                button.innerText = inputName;
            });
        });
    </script>
@endpush
