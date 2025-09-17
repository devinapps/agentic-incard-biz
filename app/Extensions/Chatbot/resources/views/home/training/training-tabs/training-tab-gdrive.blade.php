<div
    class="col-start-1 col-end-1 row-start-1 row-end-1"
    x-show="activeTab === 'gdrive'"
    x-transition.opacity.150ms
>
    <form
        class="flex flex-col gap-5"
        @submit.prevent="addUrl"
        action="{{ route('dashboard.chatbot.train.gdrive-url') }}"
    >
        @csrf

        <x-form-step
            step="1"
            label="{{ __('Add Folder Google Drive URL') }}"
        />

        <div class="relative">
            <x-forms.input
                id="url"
                size="lg"
                name="url"
                placeholder="https://example.com"
            />
            <x-button
                class="group absolute end-2 top-1/2 size-11 -translate-y-1/2 text-primary hover:-translate-y-1/2 hover:rotate-45 hover:scale-110 focus-visible:-translate-y-1/2 focus-visible:rotate-45 focus-visible:scale-110"
                variant="link"
                size="none"
                type="submit"
                ::disabled="fetching"
            >
                <x-tabler-refresh
                    class="size-5"
                    stroke-width="2.2"
                    ::class="{ 'animate-spin': fetching }"
                />
                <span class="sr-only">
                    @lang('Fetch')
                </span>
            </x-button>
        </div>
    </form>

    <form
        class="mt-14"
        action="{{ route('dashboard.chatbot.train.generate.embedding') }}"
        @submit.prevent="trainEmbeddings"
    >
        @csrf

        <x-form-step
            class="mb-6"
            step="2"
            label="{{ __('Select Pages') }}"
            x-show="embeddings.filter(e => e.type === 'gdrive').length"
        >
            <button
                class="group ms-auto text-2xs font-semibold"
                type="button"
                @click="toggleSelectAll"
            >
                <span class="group-[&.has-selected]:hidden">
                    @lang('Select All')
                </span>
                <span class="hidden group-[&.has-selected]:block">
                    @lang('Deselect All')
                </span>
            </button>
        </x-form-step>

        <div class="space-y-4">
            <template
                x-for="embedding in embeddings.filter(e => e.type === 'gdrive')"
                :key="'website-item-' + embedding.id"
            >
                <div class="flex items-center justify-between rounded-lg border p-1.5">
                    <x-forms.input
                        class:container="grow"
                        class:custom-wrap="size-7"
                        class:label="text-foreground"
                        data-type="website"
                        ::id="'train-website-item-' + embedding.id"
                        name="embedding-item"
                        type="checkbox"
                        ::value="embedding.id"
                        ::checked="embedding.status === 'Trained'"
                        x-init="$el.closest('label').setAttribute('for', 'train-website-item-' + embedding.id);"
                        custom
                    >
                        <x-slot:label>
                            <span x-text="embedding.title || embedding.url"></span>
                        </x-slot:label>
                    </x-forms.input>

                    <div class="flex items-center justify-between gap-1">
                        <x-badge
                            class="whitespace-nowrap text-2xs hover:translate-y-0 hover:shadow-none"
                            ::class="{ '!bg-green-500/15': embedding.status === 'Trained', '!text-green-500': embedding.status === 'Trained' }"
                        >
                            <span x-text="embedding.status"></span>
                        </x-badge>
                        <x-button
                            class="inline-flex items-center justify-center text-red-600"
                            variant="link"
                            size="none"
                            @click.prevent="deleteEmbedding(embedding.id)"
                            ::disabled="fetching"
                        >
                            <x-tabler-circle-minus
                                class="size-7"
                                stroke-width="1.5"
                            />
                            <span class="sr-only">
                                {{ __('Delete') }}
                            </span>
                        </x-button>
                    </div>
                </div>
            </template>

            <x-button
                class="!mt-8 w-full"
                size="lg"
                type="submit"
                x-show="embeddings.filter(e => e.type === 'website').length"
                ::disabled="fetching"
            >
                @lang('Train GPT')
            </x-button>

        </div>
    </form>
</div>
