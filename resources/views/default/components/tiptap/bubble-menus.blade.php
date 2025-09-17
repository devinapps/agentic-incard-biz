{{-- Bubble menus --}}
<div
    class="tiptap-bubble-menu grid grid-cols-6 gap-px rounded-lg bg-foreground px-px md:grid-cols-11 [&_.is-active]:!bg-background/40"
    x-cloak
>
    {{-- bold button --}}
    <button
        class="rounded-md bg-transparent p-2 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleBold()'
        :class="$store.tiptapEditor.isActive('bold', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-bold class="size-4" />
    </button>

    {{-- italic button --}}
    <button
        class="rounded-md bg-transparent p-2 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleItalic()'
        :class="$store.tiptapEditor.isActive('italic', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-italic class="size-4" />
    </button>

    {{-- highlight button --}}
    <button
        class="rounded-md bg-transparent p-2.5 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleHighlight()'
        :class="$store.tiptapEditor.isActive('highlight', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-highlight class="size-4" />
    </button>

    {{-- code button --}}
    <button
        class="rounded-md bg-transparent p-2.5 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleCode()'
        :class="$store.tiptapEditor.isActive('code', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-code class="size-4" />
    </button>

    {{-- strike button --}}
    <button
        class="rounded-md bg-transparent p-2.5 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleStrike()'
        :class="$store.tiptapEditor.isActive('strike', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-strikethrough class="size-4" />
    </button>

    {{-- underline button --}}
    <button
        class="rounded-md bg-transparent p-2.5 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleUnderline()'
        :class="$store.tiptapEditor.isActive('underline', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-underline class="size-4" />
    </button>

    {{-- blockquote button --}}
    <button
        class="rounded-md bg-transparent p-2 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleBlockquote()'
        :class="$store.tiptapEditor.isActive('blockquote', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-blockquote class="size-4" />
    </button>

    {{-- ordered list --}}
    <button
        class="rounded-md bg-transparent p-2 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleOrderedList()'
        :class="$store.tiptapEditor.isActive('orderedList', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-list-numbers class="size-4" />
    </button>

    {{-- bullet list --}}
    <button
        class="rounded-md bg-transparent p-2 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleBulletList()'
        :class="$store.tiptapEditor.isActive('bulletList', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-list class="size-4" />
    </button>

    {{-- code block --}}
    <button
        class="rounded-md bg-transparent p-2 leading-none text-background"
        @click.prevent='$store.tiptapEditor.toggleCodeBlock()'
        :class="$store.tiptapEditor.isActive('codeBlock', $store.tiptapEditor._updated_at) ? 'is-active' : ''"
    >
        <x-tabler-brackets-angle class="size-4" />
    </button>

    {{-- heading --}}
    <x-dropdown.dropdown>
        <x-slot:trigger>
            <button class="rounded-md bg-transparent p-2 leading-none text-background hover:bg-primary">
                <x-tabler-heading class="size-4" />
            </button>
        </x-slot:trigger>
        <x-slot:dropdown>
            <div class="flex flex-col">
                <button
                    class="group w-full rounded-md bg-transparent p-2 leading-none text-background hover:bg-primary"
                    @click.prevent='$store.tiptapEditor.toggleHeading({level: 1})'
                    :class="$store.tiptapEditor.isActive('heading', { level: 1 }) ? 'is-active' : ''"
                >
                    <h1 class="mb-0 text-start group-hover:text-white">H1</h1>
                </button>
                <button
                    class="group w-full rounded-md bg-transparent p-2 leading-none text-background hover:bg-primary"
                    @click.prevent='$store.tiptapEditor.toggleHeading({level: 2})'
                    :class="$store.tiptapEditor.isActive('heading', { level: 2 }) ? 'is-active' : ''"
                >
                    <h2 class="mb-0 text-start group-hover:text-white">H2</h2>
                </button>
                <button
                    class="group w-full rounded-md bg-transparent p-2 leading-none text-background hover:bg-primary"
                    @click.prevent='$store.tiptapEditor.toggleHeading({level: 3})'
                    :class="$store.tiptapEditor.isActive('heading', { level: 3 }) ? 'is-active' : ''"
                >
                    <h3 class="mb-0 text-start group-hover:text-white">H3</h3>
                </button>
                <button
                    class="group w-full rounded-md bg-transparent p-2 leading-none text-background hover:bg-primary"
                    @click.prevent='$store.tiptapEditor.toggleHeading({level: 4})'
                    :class="$store.tiptapEditor.isActive('heading', { level: 4 }) ? 'is-active' : ''"
                >
                    <h4 class="mb-0 text-start group-hover:text-white">H4</h4>
                </button>
                <button
                    class="group w-full rounded-md bg-transparent p-2 leading-none text-background hover:bg-primary"
                    @click.prevent='$store.tiptapEditor.toggleHeading({level: 5})'
                    :class="$store.tiptapEditor.isActive('heading', { level: 5 }) ? 'is-active' : ''"
                >
                    <h5 class="mb-0 text-start group-hover:text-white">H5</h5>
                </button>
                <button
                    class="group w-full rounded-md bg-transparent p-2 leading-none text-background hover:bg-primary"
                    @click.prevent='$store.tiptapEditor.toggleHeading({level: 6})'
                    :class="$store.tiptapEditor.isActive('heading', { level: 6 }) ? 'is-active' : ''"
                >
                    <h6 class="mb-0 text-start group-hover:text-white">H6</h6>
                </button>
            </div>
        </x-slot:dropdown>
    </x-dropdown.dropdown>
</div>
