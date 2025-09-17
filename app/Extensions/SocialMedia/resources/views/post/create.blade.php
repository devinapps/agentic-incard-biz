@php
    $default_platform = $currentPlatform->value;
    $default_platform_id = 0;
    $current_platform = $default_platform;
    $tones = [
        'default' => 'Default',
        'informative' => 'Informative',
        'humorous' => 'Humorous',
        'emphatic' => 'Emphatic',
        'engaging' => 'Engaging',
        'promotional' => 'Promotional',
        'educational' => 'Educational',
        'celebratory' => 'Celebratory',
        'urgent' => 'Urgent/Time-Sensitive',
        'professional' => 'Professional',
        'excited' => 'Excited',
    ];
    $company_id = '';
    $product_id = '';
    $campaign_id = '';
    $is_personalized_content = '';
    $tone = 'default';
    $content = '';
    $link = '';
    $postImage = '';
    $video = '';
    $is_repeated = false;
    $repeat_period = '';
    $repeat_start_date = '';
    $repeat_time = '';
    $status = '';
    $scheduled_at = '';
    $social_media_platform_id = $currentPlatform->platform()?->getKey();

    $companies_list = $companies->pluck('name', 'id')->toArray();
    $campaigns_list = $campaigns->pluck('name', 'id')->toArray();

    $credentials = $currentPlatform->platform()?->credentials;

    $platformUsername = $credentials['name'] ?? '';
    $platformPicture = $credentials['picture'] ?? '';
@endphp

@extends('panel.layout.app', ['disable_tblr' => true, 'disable_titlebar' => true, 'layout_wide' => true])
@section('title', __('Create New Post'))

@push('css')
    <link
        href="{{ custom_theme_url('/assets/libs/datepicker/air-datepicker.css') }}"
        rel="stylesheet"
    />
    <style>
        .lqd-social-media-post-create-datepicker .air-datepicker {
            --adp-background-color: hsl(var(--background));
            --adp-day-name-color: hsl(var(--heading-foreground) / 50%);
            --adp-color-other-month: hsl(var(--heading-foreground) / 50%);
            --adp-color: hsl(var(--heading-foreground));
            --adp-accent-color: hsl(var(--primary));
            --adp-border-color: hsl(var(--border));
            --adp-border-color-inner: hsl(var(--border));
            --adp-cell-background-color-selected-hover: hsl(var(--primary));
            --adp-cell-background-color-selected: hsl(var(--primary));
            --adp-color-current-date: #4eb5e6;
            --adp-cell-background-color-hover: hsl(var(--heading-foreground) / 15%);
            --adp-background-color-hover: hsl(var(--heading-foreground) / 15%);
            width: 100%;
            border-radius: 0.625rem;
            border-color: hsl(var(--input-border));
        }

        @media(min-width: 992px) {
            .lqd-header {
                display: none !important;
            }
        }
    </style>
@endpush

@section('content')
    <div
        class="lqd-social-media-post-create"
        x-data="socialMediaPostCreate"
    >
        <div class="lqd-social-media-post-create-header border-b py-5">
            <div class="container">
                @php
                    $grid_cols = count($platforms);
                    $grid_cols_classname = 'lg:grid-cols-' . $grid_cols;
                @endphp

                <div @class([
                    'grid',
                    $grid_cols_classname,
                    'grid-cols-1 gap-x-7 gap-y-3 md:grid-cols-2',
                ])>
                    @foreach (\App\Extensions\SocialMedia\System\Enums\PlatformEnum::cases() as $platform)
                        @php
                            $is_connected = $platform->platform()?->isConnected();
                            $name = $platform->platform()?->credentials['name'] ?? 'Jhon Doe';

                            $profileImage =
                                $platform->platform()?->credentials['picture'] ??
                                custom_theme_url('/assets/img/avatars/avatar-1.jpg');
                        @endphp

                        <x-button
                            @class([
                                'text-sm font-medium [&.active]:bg-secondary [&.active]:text-secondary-foreground [&.active]:outline-secondary',
                                'active' => $is_connected && $current_platform === $platform->name,
                            ])
                            type="button"
                            variant="outline"
                            ::class="{ active: currentPlatform === '{{ $platform->name }}' && {{ $is_connected ? 1 : 0 }} }"
                            @click.prevent="currentPlatform = '{{ $platform->name }}'; platformUsername = '{{ $name }}'; platformPicture = '{!! $profileImage !!}';socialMediaPlatformId = '{{ $platform->platform()?->id }}';"
                            :disabled="!$is_connected"
                        >
                            @php
                                $image = 'vendor/social-media/icons/' . $platform->value . '.svg';
                                $image_dark_version = 'vendor/social-media/icons/' . $platform->value . '-light.svg';
                            @endphp
                            <img
                                @class([
                                    'w-7 h-auto',
                                    'dark:hidden' => file_exists($image_dark_version),
                                ])
                                src="{{ asset($image) }}"
                                alt="{{ $platform->name }}"
                            />
                            @if (file_exists($image_dark_version))
                                <img
                                    class="hidden h-auto w-7 dark:block"
                                    src="{{ asset($image_dark_version) }}"
                                    alt="{{ $platform->name }}"
                                />
                            @endif
                            {{ str($platform->name)->title() }}

                            <span
                                @class([
                                    'ms-2 inline-grid size-6 place-items-center rounded-full bg-background text-heading-foreground shadow-xl',
                                    'hidden' => $current_platform !== $platform->name,
                                ])
                                :class="{ hidden: currentPlatform !== '{{ $platform->name }}' }"
                            >
                                <x-tabler-check class="size-4" />
                            </span>
                        </x-button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="lqd-social-media-post-create-content-wrap py-8">
            <div class="container">
                <div class="flex flex-wrap justify-between gap-y-5">
                    <div class="w-full lg:w-5/12">
                        <h2 class="mb-3.5">
                            @lang('Create New Post')
                        </h2>
                        <p class="mb-8 text-xs/5 font-medium opacity-60 lg:w-2/3">
                            @lang('Instantly create engaging, tailored posts to captivate your audience and save time.')
                        </p>

                        <form
                            class="space-y-7"
                            method="post"
                            action="{{ route('dashboard.user.social-media.post.store') }}"
                            x-ref="form"
                        >
                            <input
                                name="company_id"
                                type="hidden"
                                x-model="selectedCompany"
                            />
                            <input
                                name="product_id"
                                type="hidden"
                                x-model="selectedProduct"
                            />
                            <input
                                type="hidden"
                                name="campaign_id"
                                x-model="selectedCampaign"
                            >
                            <input
                                type="hidden"
                                name="scheduled_at"
                                x-model="scheduledAt"
                            >
                            <input
                                type="hidden"
                                name="is_repeated"
                                x-model="isRepeated"
                            >
                            <input
                                type="hidden"
                                name="repeat_period"
                                x-model="repeatPeriod"
                            >
                            <input
                                type="hidden"
                                name="repeat_start_date"
                                x-model="repeatStartDate"
                            >
                            <input
                                type="hidden"
                                name="repeat_time"
                                x-model="repeatTime"
                            >
                            <input
                                type="hidden"
                                name="social_media_platform_id"
                                x-model="socialMediaPlatformId"
                            >
                            <input
                                type="hidden"
                                name="image"
                                x-model="image"
                            >
                            <input
                                type="hidden"
                                name="video"
                                x-model="video"
                            >

                            {{-- Personalized content checkbox and modals --}}
                            <div class="space-y-5">
                                <x-forms.input
                                    class:label="text-heading-foreground flex-row-reverse justify-between"
                                    type="checkbox"
                                    name="is_personalized_content"
                                    label="{{ __('Personalized Content') }}"
                                    switcher
                                    ::checked="personalizedContent"
                                    x-model="personalizedContent"
                                    :checked="filled($is_personalized_content)"
                                />

                                <div
                                    class="grid grid-cols-1 gap-5 md:grid-cols-2"
                                    @if (!$is_personalized_content) x-cloak @endif
                                    x-show="personalizedContent"
                                    x-transition
                                >
                                    {{-- Company Modal --}}
                                    <x-modal
                                        class:modal-head="border-b-0"
                                        class:modal-body="pt-3"
                                        class:modal-container="max-w-[600px]"
                                    >
                                        <x-slot:trigger
                                            class="w-full flex-wrap rounded-xl"
                                            ::class="{ 'bg-primary text-primary-foreground outline-primary': selectedCompany &&
                                                    selectedProduct }"
                                            variant="outline"
                                            size="lg"
                                            type="button"
                                        >
                                            @lang('Company')
                                            <span
                                                class="ms-[-0.5ch] opacity-70"
                                                @if (!filled($company_id) || !filled($product_id)) x-cloak @endif
                                                x-show="selectedCompany && selectedProduct"
                                                x-text="': ' + companies[selectedCompany]"
                                            >
                                                @if (filled($company_id) && filled($product_id))
                                                    : {{ $companies_list[$company_id] }}
                                                @endif
                                            </span>
                                            <x-tabler-chevron-right
                                                @class([
                                                    'size-4',
                                                    'hidden' => filled($company_id) && filled($product_id),
                                                ])
                                                ::class="{ hidden: selectedCompany && selectedProduct }"
                                            />
                                            <span
                                                @class([
                                                    'size-5 place-items-center rounded-full bg-background text-heading-foreground shadow-xl shrink-0',
                                                    'hidden' => !filled($company_id) || !filled($product_id),
                                                    'inline-grid' => filled($company_id) && filled($product_id),
                                                ])
                                                :class="{ hidden: !selectedCompany || !
                                                    selectedProduct, 'inline-grid': selectedCompany && selectedProduct }"
                                                aria-hidden="true"
                                            >
                                                <x-tabler-check class="size-4" />
                                            </span>
                                        </x-slot:trigger>

                                        <x-slot:modal>
                                            <h3 class="mb-3.5">
                                                @lang('Company Info')
                                            </h3>
                                            <p class="mb-7 text-heading-foreground/60">
                                                @lang('Start by selecting a company or create a new one at BrandCenter in a few clicks.')
                                            </p>

                                            <div class="flex flex-col gap-y-7">
                                                <x-forms.input
                                                    class:label="text-heading-foreground"
                                                    size="lg"
                                                    type="select"
                                                    label="{{ __('Select a Company') }}"
                                                    x-model="selectedCompany"
                                                    @change="selectedProduct = null"
                                                >
                                                    <option value="">
                                                        {{ __('None') }}
                                                    </option>
                                                    @foreach ($companies as $company)
                                                        <option value="{{ $company['id'] }}">
                                                            {{ $company['name'] }}
                                                        </option>
                                                    @endforeach
                                                </x-forms.input>

                                                <div
                                                    class="grid place-items-center"
                                                    x-show="selectedCompany"
                                                >
                                                    @foreach ($companies as $company)
                                                        <div
                                                            class="col-start-1 col-end-1 row-start-1 row-end-1 w-full"
                                                            x-show="selectedCompany == '{{ $company['id'] }}'"
                                                            x-transition
                                                        >
                                                            <x-forms.input
                                                                class:label="text-heading-foreground"
                                                                size="lg"
                                                                type="select"
                                                                label="{{ __('Select a Product') }}"
                                                                x-model="selectedProduct"
                                                            >
                                                                <option value="">
                                                                    {{ __('None') }}
                                                                </option>
                                                                @foreach ($company->products as $product)
                                                                    <option value="{{ $product['id'] }}">
                                                                        {{ $product['name'] }}
                                                                    </option>
                                                                @endforeach
                                                            </x-forms.input>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <x-button
                                                    class="w-full text-2xs font-semibold"
                                                    variant="secondary"
                                                    type="button"
                                                    @click.prevent="modalOpen = false"
                                                >
                                                    @lang('Next')
                                                    <span
                                                        class="inline-grid size-7 place-items-center rounded-full bg-background text-heading-foreground shadow-xl"
                                                        aria-hidden="true"
                                                    >
                                                        <x-tabler-chevron-right class="size-4" />
                                                    </span>
                                                </x-button>
                                            </div>
                                        </x-slot:modal>
                                    </x-modal>

                                    {{-- Campaign Modal --}}
                                    <x-modal
                                        class:modal-head="border-b-0"
                                        class:modal-body="pt-3"
                                        class:modal-container="max-w-[600px]"
                                    >
                                        <x-slot:trigger
                                            class="w-full flex-wrap rounded-xl"
                                            ::class="{ 'bg-primary text-primary-foreground outline-primary': selectedCampaign }"
                                            variant="outline"
                                            size="lg"
                                            type="button"
                                        >
                                            @lang('Campaign')
                                            <span
                                                class="ms-[-0.5ch] opacity-70"
                                                @if (!filled($campaign_id)) x-cloak @endif
                                                x-show="selectedCampaign"
                                                x-text="': ' + campaigns[selectedCampaign]"
                                            >
                                                @if (filled($campaign_id))
                                                    : {{ $campaigns_list[$campaign_id] }}
                                                @endif
                                            </span>
                                            <x-tabler-chevron-right
                                                class="size-4"
                                                ::class="{ hidden: selectedCampaign }"
                                            />
                                            <span
                                                class="hidden size-5 shrink-0 place-items-center rounded-full bg-background text-heading-foreground shadow-xl"
                                                :class="{ hidden: !selectedCampaign, 'inline-grid': selectedCampaign }"
                                                aria-hidden="true"
                                            >
                                                <x-tabler-check class="size-4" />
                                            </span>
                                        </x-slot:trigger>

                                        <x-slot:modal>
                                            <h3 class="mb-3.5">
                                                @lang('Campaign Info')
                                            </h3>
                                            <p class="mb-7 text-heading-foreground/60">
                                                @lang('Start by selecting a campaign or create a new one on the Campaigns page in a few clicks.')
                                            </p>

                                            <div class="flex flex-col gap-y-7">
                                                <x-forms.input
                                                    class:label="text-heading-foreground"
                                                    size="lg"
                                                    type="select"
                                                    label="{{ __('Select a Campaign') }}"
                                                    x-model="selectedCampaign"
                                                >
                                                    <option value="">
                                                        {{ __('None') }}
                                                    </option>
                                                    @foreach ($campaigns as $campaign)
                                                        <option value="{{ $campaign['id'] }}">
                                                            {{ $campaign['name'] }}
                                                        </option>
                                                    @endforeach
                                                </x-forms.input>

                                                <div
                                                    x-show="selectedCampaign"
                                                    x-transition
                                                >
                                                    <p class="text-2xs font-semibold text-heading-foreground">
                                                        @lang('Target Audience')
                                                    </p>
                                                    @foreach ($campaigns as $campaign)
                                                        <p
                                                            class="m-0 rounded-input border border-input-border p-4"
                                                            x-show="selectedCampaign == '{{ $campaign['id'] }}'"
                                                        >
                                                            {!! $campaign['target_audience'] !!}
                                                        </p>
                                                    @endforeach
                                                </div>

                                                <x-button
                                                    class="w-full text-2xs font-semibold"
                                                    variant="secondary"
                                                    type="button"
                                                    @click.prevent="modalOpen = false"
                                                >
                                                    @lang('Next')
                                                    <span
                                                        class="inline-grid size-7 place-items-center rounded-full bg-background text-heading-foreground shadow-xl"
                                                        aria-hidden="true"
                                                    >
                                                        <x-tabler-chevron-right class="size-4" />
                                                    </span>
                                                </x-button>
                                            </div>
                                        </x-slot:modal>
                                    </x-modal>
                                </div>
                            </div>

                            <x-forms.input
                                class:label="text-heading-foreground"
                                type="select"
                                size="lg"
                                name="selectedUserPlatforms[]"
                                label="{{ __('Select Accounts') }}"
                                x-model="selectedUserPlatforms"
                                multiple=""
                            >
                                <template
                                    x-for="platform in getUserPlatforms()"
                                    :key="platform.id"
                                >
                                    <option
                                        :value="platform.id"
                                        x-text="platform.credentials.name"
                                    ></option>
                                </template>
                            </x-forms.input>

                            {{-- Tone dropdown --}}
                            <x-forms.input
                                class:label="text-heading-foreground"
                                type="select"
                                size="lg"
                                name="tone"
                                label="{{ __('Tone') }}"
                                x-model="tone"
                            >
                                @foreach ($tones as $value => $label)
                                    <option value="{{ $value }}">
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </x-forms.input>

                            {{-- Content textarea --}}
                            <x-forms.input
                                class:label="text-heading-foreground"
                                type="textarea"
                                name="content"
                                label="{{ __('Post Content') }}"
                                placeholder="{!! __('Now’s the perfect time to grab your favorites! 💥 Buy 2, Get 1 Free! 💥 #futureishere') !!}"
                                rows="5"
                                size="lg"
                                x-model="content"
                            >
                                <x-slot:label-extra>
                                    <x-button
                                        class="text-2xs"
                                        type="button"
                                        variant="link"
                                        @click.prevent="generateContent"
                                    >
                                        <span class="me-1 inline-grid place-items-center">
                                            {{-- blade-formatter-disable --}}
											<svg class="col-start-1 col-end-1 row-start-1 row-end-1" :class="{hidden: generatingContent}" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5085 6.34955L15.113 6.63248C14.4408 6.7689 13.8236 7.10033 13.3386 7.58536C12.8536 8.0704 12.5221 8.68757 12.3857 9.35981L12.1028 10.7552C12.0748 10.8948 11.9994 11.0203 11.8893 11.1105C11.7792 11.2007 11.6412 11.25 11.4989 11.25C11.3566 11.25 11.2187 11.2007 11.1086 11.1105C10.9985 11.0203 10.923 10.8948 10.895 10.7552L10.6121 9.35981C10.4758 8.68751 10.1444 8.07027 9.65938 7.58522C9.17432 7.10016 8.55709 6.76878 7.8848 6.63248L6.48937 6.34955C6.35011 6.32107 6.22495 6.24537 6.13507 6.13525C6.04519 6.02513 5.99609 5.88733 5.99609 5.74519C5.99609 5.60304 6.04519 5.46526 6.13507 5.35514C6.22495 5.24502 6.35011 5.16932 6.48937 5.14084L7.8848 4.8579C8.55709 4.7216 9.17432 4.39022 9.65938 3.90516C10.1444 3.42011 10.4758 2.80288 10.6121 2.13058L10.895 0.73517C10.923 0.595627 10.9985 0.470081 11.1086 0.379882C11.2187 0.289682 11.3566 0.240395 11.4989 0.240395C11.6412 0.240395 11.7792 0.289682 11.8893 0.379882C11.9994 0.470081 12.0748 0.595627 12.1028 0.73517L12.3857 2.13058C12.5221 2.80283 12.8536 3.41999 13.3386 3.90503C13.8236 4.39007 14.4408 4.72148 15.113 4.8579L16.5085 5.14084C16.6477 5.16932 16.7729 5.24502 16.8627 5.35514C16.9526 5.46526 17.0017 5.60304 17.0017 5.74519C17.0017 5.88733 16.9526 6.02513 16.8627 6.13525C16.7729 6.24537 16.6477 6.32107 16.5085 6.34955ZM6.30231 13.4219L5.92312 13.4989C5.45558 13.5937 5.02634 13.8242 4.689 14.1616C4.35167 14.4989 4.12118 14.9281 4.02633 15.3957L3.94934 15.7749C3.92805 15.881 3.87064 15.9766 3.78687 16.0452C3.70309 16.1139 3.59813 16.1514 3.48982 16.1514C3.38151 16.1514 3.27654 16.1139 3.19277 16.0452C3.10899 15.9766 3.05157 15.881 3.03029 15.7749L2.9533 15.3957C2.85844 14.9281 2.62796 14.4989 2.29062 14.1616C1.95328 13.8242 1.52404 13.5937 1.0565 13.4989L0.677333 13.4219C0.571137 13.4006 0.475582 13.3432 0.406935 13.2594C0.338287 13.1756 0.300781 13.0707 0.300781 12.9624C0.300781 12.854 0.338287 12.7491 0.406935 12.6653C0.475582 12.5815 0.571137 12.5241 0.677333 12.5028L1.0565 12.4258C1.52404 12.331 1.95328 12.1005 2.29062 11.7632C2.62796 11.4258 2.85844 10.9966 2.9533 10.5291L3.03029 10.1499C3.05157 10.0437 3.10899 9.94813 3.19277 9.87948C3.27654 9.81083 3.38151 9.77334 3.48982 9.77334C3.59813 9.77334 3.70309 9.81083 3.78687 9.87948C3.87064 9.94813 3.92805 10.0437 3.94934 10.1499L4.02633 10.5291C4.12118 10.9966 4.35167 11.4258 4.689 11.7632C5.02634 12.1005 5.45558 12.331 5.92312 12.4258L6.30231 12.5028C6.4085 12.5241 6.50404 12.5815 6.57269 12.6653C6.64134 12.7491 6.67884 12.854 6.67884 12.9624C6.67884 13.0707 6.64134 13.1756 6.57269 13.2594C6.50404 13.3432 6.4085 13.4006 6.30231 13.4219Z" fill="url(#paint0_linear_8906_3722)"/> <defs> <linearGradient id="paint0_linear_8906_3722" x1="17.0017" y1="8.19589" x2="0.137511" y2="6.25241" gradientUnits="userSpaceOnUse"> <stop stop-color="#8D65E9"/> <stop offset="0.483" stop-color="#5391E4"/> <stop offset="1" stop-color="#6BCD94"/> </linearGradient> </defs> </svg>
											{{-- blade-formatter-enable --}}
                                            <x-tabler-refresh
                                                class="col-start-1 col-end-1 row-start-1 row-end-1 hidden size-4 animate-spin"
                                                x-show="generatingContent"
                                                ::class="{ hidden: !generatingContent }"
                                            />
                                        </span>
                                        @lang('Enhance with AI')
                                    </x-button>
                                </x-slot:label-extra>
                            </x-forms.input>

                            {{-- Link input --}}
                            {{--                            <x-forms.input --}}
                            {{--                                class:label="text-heading-foreground" --}}
                            {{--                                type="url" --}}
                            {{--                                label="{{ __('Link') }}" --}}
                            {{--                                size="lg" --}}
                            {{--                                name="link" --}}
                            {{--                                placeholder="{{ __('Add URL') }}" --}}
                            {{--                                x-model="link" --}}
                            {{--                            /> --}}

                            {{-- Image input --}}
                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Select Custom Image') }}"
                                size="lg"
                                name="upload_image"
                                type="file"
                                accept="image/*"
                                x-ref="uploadImage"
                                @change="uploadImage"
                                x-show="currentPlatform != 'tiktok'"
                            >
                                <x-slot:label-extra>
                                    <x-button
                                        class="text-2xs"
                                        type="button"
                                        variant="link"
                                        @click.prevent="generateImage"
                                    >
                                        <span class="me-1 inline-grid place-items-center">
                                            {{-- blade-formatter-disable --}}
											<svg class="col-start-1 col-end-1 row-start-1 row-end-1" :class="{hidden: generatingImage}" width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M16.5085 6.34955L15.113 6.63248C14.4408 6.7689 13.8236 7.10033 13.3386 7.58536C12.8536 8.0704 12.5221 8.68757 12.3857 9.35981L12.1028 10.7552C12.0748 10.8948 11.9994 11.0203 11.8893 11.1105C11.7792 11.2007 11.6412 11.25 11.4989 11.25C11.3566 11.25 11.2187 11.2007 11.1086 11.1105C10.9985 11.0203 10.923 10.8948 10.895 10.7552L10.6121 9.35981C10.4758 8.68751 10.1444 8.07027 9.65938 7.58522C9.17432 7.10016 8.55709 6.76878 7.8848 6.63248L6.48937 6.34955C6.35011 6.32107 6.22495 6.24537 6.13507 6.13525C6.04519 6.02513 5.99609 5.88733 5.99609 5.74519C5.99609 5.60304 6.04519 5.46526 6.13507 5.35514C6.22495 5.24502 6.35011 5.16932 6.48937 5.14084L7.8848 4.8579C8.55709 4.7216 9.17432 4.39022 9.65938 3.90516C10.1444 3.42011 10.4758 2.80288 10.6121 2.13058L10.895 0.73517C10.923 0.595627 10.9985 0.470081 11.1086 0.379882C11.2187 0.289682 11.3566 0.240395 11.4989 0.240395C11.6412 0.240395 11.7792 0.289682 11.8893 0.379882C11.9994 0.470081 12.0748 0.595627 12.1028 0.73517L12.3857 2.13058C12.5221 2.80283 12.8536 3.41999 13.3386 3.90503C13.8236 4.39007 14.4408 4.72148 15.113 4.8579L16.5085 5.14084C16.6477 5.16932 16.7729 5.24502 16.8627 5.35514C16.9526 5.46526 17.0017 5.60304 17.0017 5.74519C17.0017 5.88733 16.9526 6.02513 16.8627 6.13525C16.7729 6.24537 16.6477 6.32107 16.5085 6.34955ZM6.30231 13.4219L5.92312 13.4989C5.45558 13.5937 5.02634 13.8242 4.689 14.1616C4.35167 14.4989 4.12118 14.9281 4.02633 15.3957L3.94934 15.7749C3.92805 15.881 3.87064 15.9766 3.78687 16.0452C3.70309 16.1139 3.59813 16.1514 3.48982 16.1514C3.38151 16.1514 3.27654 16.1139 3.19277 16.0452C3.10899 15.9766 3.05157 15.881 3.03029 15.7749L2.9533 15.3957C2.85844 14.9281 2.62796 14.4989 2.29062 14.1616C1.95328 13.8242 1.52404 13.5937 1.0565 13.4989L0.677333 13.4219C0.571137 13.4006 0.475582 13.3432 0.406935 13.2594C0.338287 13.1756 0.300781 13.0707 0.300781 12.9624C0.300781 12.854 0.338287 12.7491 0.406935 12.6653C0.475582 12.5815 0.571137 12.5241 0.677333 12.5028L1.0565 12.4258C1.52404 12.331 1.95328 12.1005 2.29062 11.7632C2.62796 11.4258 2.85844 10.9966 2.9533 10.5291L3.03029 10.1499C3.05157 10.0437 3.10899 9.94813 3.19277 9.87948C3.27654 9.81083 3.38151 9.77334 3.48982 9.77334C3.59813 9.77334 3.70309 9.81083 3.78687 9.87948C3.87064 9.94813 3.92805 10.0437 3.94934 10.1499L4.02633 10.5291C4.12118 10.9966 4.35167 11.4258 4.689 11.7632C5.02634 12.1005 5.45558 12.331 5.92312 12.4258L6.30231 12.5028C6.4085 12.5241 6.50404 12.5815 6.57269 12.6653C6.64134 12.7491 6.67884 12.854 6.67884 12.9624C6.67884 13.0707 6.64134 13.1756 6.57269 13.2594C6.50404 13.3432 6.4085 13.4006 6.30231 13.4219Z" fill="url(#paint0_linear_8906_3722)"/> <defs> <linearGradient id="paint0_linear_8906_3722" x1="17.0017" y1="8.19589" x2="0.137511" y2="6.25241" gradientUnits="userSpaceOnUse"> <stop stop-color="#8D65E9"/> <stop offset="0.483" stop-color="#5391E4"/> <stop offset="1" stop-color="#6BCD94"/> </linearGradient> </defs> </svg>
											{{-- blade-formatter-enable --}}
                                            <x-tabler-refresh
                                                class="col-start-1 col-end-1 row-start-1 row-end-1 hidden size-4 animate-spin"
                                                x-show="generatingImage"
                                                ::class="{ hidden: !generatingImage }"
                                            />
                                        </span>
                                        @lang('Generate with AI')
                                    </x-button>
                                </x-slot:label-extra>
                            </x-forms.input>

                            <x-forms.input
                                class:label="text-heading-foreground"
                                label="{{ __('Select Custom Video') }}"
                                size="lg"
                                name="upload_video"
                                type="file"
                                accept="video/*"
                                x-ref="uploadVideo"
                                @change="uploadVideo"
                                x-show="currentPlatform == 'tiktok'"
                            >
                                <x-slot:label-extra>
                                    <x-button
                                        class="text-2xs"
                                        type="button"
                                        variant="link"
                                        @click.prevent="generateVideo"
                                    >
                                        <span class="me-1 inline-grid place-items-center">
                                            <svg
                                                class="col-start-1 col-end-1 row-start-1 row-end-1"
                                                :class="{ hidden: generatingVideo }"
                                                width="17"
                                                height="17"
                                                viewBox="0 0 17 17"
                                                fill="none"
                                                xmlns="http://www.w3.org/2000/svg"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    clip-rule="evenodd"
                                                    d="M16.5085 6.34955L15.113 6.63248C14.4408 6.7689 13.8236 7.10033 13.3386 7.58536C12.8536 8.0704 12.5221 8.68757 12.3857 9.35981L12.1028 10.7552C12.0748 10.8948 11.9994 11.0203 11.8893 11.1105C11.7792 11.2007 11.6412 11.25 11.4989 11.25C11.3566 11.25 11.2187 11.2007 11.1086 11.1105C10.9985 11.0203 10.923 10.8948 10.895 10.7552L10.6121 9.35981C10.4758 8.68751 10.1444 8.07027 9.65938 7.58522C9.17432 7.10016 8.55709 6.76878 7.8848 6.63248L6.48937 6.34955C6.35011 6.32107 6.22495 6.24537 6.13507 6.13525C6.04519 6.02513 5.99609 5.88733 5.99609 5.74519C5.99609 5.60304 6.04519 5.46526 6.13507 5.35514C6.22495 5.24502 6.35011 5.16932 6.48937 5.14084L7.8848 4.8579C8.55709 4.7216 9.17432 4.39022 9.65938 3.90516C10.1444 3.42011 10.4758 2.80288 10.6121 2.13058L10.895 0.73517C10.923 0.595627 10.9985 0.470081 11.1086 0.379882C11.2187 0.289682 11.3566 0.240395 11.4989 0.240395C11.6412 0.240395 11.7792 0.289682 11.8893 0.379882C11.9994 0.470081 12.0748 0.595627 12.1028 0.73517L12.3857 2.13058C12.5221 2.80283 12.8536 3.41999 13.3386 3.90503C13.8236 4.39007 14.4408 4.72148 15.113 4.8579L16.5085 5.14084C16.6477 5.16932 16.7729 5.24502 16.8627 5.35514C16.9526 5.46526 17.0017 5.60304 17.0017 5.74519C17.0017 5.88733 16.9526 6.02513 16.8627 6.13525C16.7729 6.24537 16.6477 6.32107 16.5085 6.34955ZM6.30231 13.4219L5.92312 13.4989C5.45558 13.5937 5.02634 13.8242 4.689 14.1616C4.35167 14.4989 4.12118 14.9281 4.02633 15.3957L3.94934 15.7749C3.92805 15.881 3.87064 15.9766 3.78687 16.0452C3.70309 16.1139 3.59813 16.1514 3.48982 16.1514C3.38151 16.1514 3.27654 16.1139 3.19277 16.0452C3.10899 15.9766 3.05157 15.881 3.03029 15.7749L2.9533 15.3957C2.85844 14.9281 2.62796 14.4989 2.29062 14.1616C1.95328 13.8242 1.52404 13.5937 1.0565 13.4989L0.677333 13.4219C0.571137 13.4006 0.475582 13.3432 0.406935 13.2594C0.338287 13.1756 0.300781 13.0707 0.300781 12.9624C0.300781 12.854 0.338287 12.7491 0.406935 12.6653C0.475582 12.5815 0.571137 12.5241 0.677333 12.5028L1.0565 12.4258C1.52404 12.331 1.95328 12.1005 2.29062 11.7632C2.62796 11.4258 2.85844 10.9966 2.9533 10.5291L3.03029 10.1499C3.05157 10.0437 3.10899 9.94813 3.19277 9.87948C3.27654 9.81083 3.38151 9.77334 3.48982 9.77334C3.59813 9.77334 3.70309 9.81083 3.78687 9.87948C3.87064 9.94813 3.92805 10.0437 3.94934 10.1499L4.02633 10.5291C4.12118 10.9966 4.35167 11.4258 4.689 11.7632C5.02634 12.1005 5.45558 12.331 5.92312 12.4258L6.30231 12.5028C6.4085 12.5241 6.50404 12.5815 6.57269 12.6653C6.64134 12.7491 6.67884 12.854 6.67884 12.9624C6.67884 13.0707 6.64134 13.1756 6.57269 13.2594C6.50404 13.3432 6.4085 13.4006 6.30231 13.4219Z"
                                                    fill="url(#paint0_linear_8906_3722)"
                                                />
                                                <defs>
                                                    <linearGradient
                                                        id="paint0_linear_8906_3722"
                                                        x1="17.0017"
                                                        y1="8.19589"
                                                        x2="0.137511"
                                                        y2="6.25241"
                                                        gradientUnits="userSpaceOnUse"
                                                    >
                                                        <stop stop-color="#8D65E9" />
                                                        <stop
                                                            offset="0.483"
                                                            stop-color="#5391E4"
                                                        />
                                                        <stop
                                                            offset="1"
                                                            stop-color="#6BCD94"
                                                        />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                            <x-tabler-refresh
                                                class="col-start-1 col-end-1 row-start-1 row-end-1 hidden size-4 animate-spin"
                                                x-show="generatingVideo"
                                                ::class="{ hidden: !generatingVideo }"
                                            />
                                        </span>
                                        @lang('Generate with AI VIDEO')
                                    </x-button>
                                </x-slot:label-extra>
                            </x-forms.input>

                            {{-- Submit & Schedule modal --}}
                            <div class="space-y-4">
                                <x-button
                                    class="w-full text-2xs font-semibold"
                                    @click="postNow"
                                    variant="secondary"
                                    type="button"
                                >
                                    @lang('Post Now')
                                    <span
                                        class="inline-grid size-7 place-items-center rounded-full bg-background text-heading-foreground shadow-xl"
                                        aria-hidden="true"
                                    >
                                        <x-tabler-chevron-right class="size-4" />
                                    </span>
                                </x-button>

                                {{-- Schedule Modal --}}
                                <x-modal
                                    class:modal-head="border-b-0"
                                    class:modal-body="pt-3"
                                    class:modal-container="max-w-[540px] lg:w-[540px]"
                                >
                                    <x-slot:trigger
                                        class="w-full text-2xs font-semibold"
                                        variant="outline"
                                        type="button"
                                        size="lg"
                                    >
                                        @lang('Schedule')
                                    </x-slot:trigger>

                                    <x-slot:modal>
                                        <div
                                            class="lqd-social-media-post-create-datepicker space-y-7"
                                            x-data="{
                                                datepicker: null,
                                                selectedDate: null,
                                                selectedTime: null,
                                                init() {
                                                    this.datepicker = new AirDatepicker('#social-media-schedule-calendar', {
                                                        selectedDates: [new Date(scheduledAt)],
                                                        inline: true,
                                                        timepicker: true,
                                                        timeFormat: 'HH:mm',
                                                        isMobile: window.innerWidth <= 768,
                                                        autoClose: window.innerWidth <= 768,
                                                        locale: defaultLocale,
                                                        onSelect: ({ formattedDate }) => {
                                                            const dateTime = formattedDate.split(' ');
                                                            const date = dateTime[0];
                                                            const time = dateTime[1];
                                            
                                                            this.selectedDate = date;
                                                            this.selectedTime = time;
                                                            this.scheduledAt = date;
                                                            this.repeatStartDate = date;
                                                            this.repeatTime = time;
                                                        }
                                                    });
                                                },
                                            }"
                                        >
                                            <div class="flex items-center justify-between gap-3">
                                                <x-forms.input
                                                    class:label="text-heading-foreground"
                                                    containerClass="grow"
                                                    type="checkbox"
                                                    size="sm"
                                                    switcher
                                                    label="{{ __('Repeat?') }}"
                                                    x-model="isRepeated"
                                                    ::checked="isRepeated"
                                                    @change="if(!$event.target.checked) repeatPeriod = null"
                                                />

                                                <x-forms.input
                                                    class:label="text-heading-foreground"
                                                    containerClass="grow"
                                                    type="select"
                                                    x-show="isRepeated"
                                                    x-model="repeatPeriod"
                                                >
                                                    <option value="">
                                                        @lang('None')
                                                    </option>
                                                    <option value="day">
                                                        @lang('Every Day')
                                                    </option>
                                                    <option value="week">
                                                        @lang('Every Week')
                                                    </option>
                                                    <option value="month">
                                                        @lang('Every Month')
                                                    </option>
                                                </x-forms.input>
                                            </div>

                                            <input
                                                class="hidden"
                                                id="social-media-schedule-calendar"
                                                type="text"
                                            >

                                            <p class="mb-0 font-medium text-heading-foreground">
                                                @lang('Selected Date'):
                                                <span
                                                    class="opacity-60"
                                                    x-text="selectedDate + ' ' + selectedTime"
                                                    x-show="selectedDate"
                                                ></span>
                                                <span
                                                    class="opacity-60"
                                                    x-show="!selectedDate"
                                                >
                                                    @lang('None')
                                                </span>
                                            </p>

                                            <x-button
                                                class="w-full text-2xs font-semibold"
                                                variant="primary"
                                                @click="schedulePost"
                                                type="button"
                                            >
                                                @lang('Schedule Post')
                                                <span
                                                    class="inline-grid size-7 place-items-center rounded-full bg-background text-heading-foreground shadow-xl"
                                                    aria-hidden="true"
                                                >
                                                    <x-tabler-chevron-right class="size-4" />
                                                </span>
                                            </x-button>
                                        </div>
                                    </x-slot:modal>
                                </x-modal>
                            </div>
                        </form>
                    </div>

                    <div class="hidden w-full rounded-[20px] bg-heading-foreground/5 py-9 lg:block lg:w-6/12">
                        <div class="sticky top-7 mx-auto w-11/12 2xl:w-4/5">
                            @include('social-media::components.post.social-media-card', [
                                'current_platform' => $current_platform,
                                'image' => $image,
                                'video' => $video,
                                'content' => $content,
                                'link' => $link,
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/libs/markdown-it.min.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/js/format-string.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/datepicker/air-datepicker.js') }}"></script>
    <script src="{{ custom_theme_url('/assets/libs/datepicker/locale/en.js') }}"></script>
    <script>
        (() => {
            document.addEventListener('alpine:init', () => {
                Alpine.data('socialMediaPostCreate', () => ({
                    userPlatforms: @json($userPlatforms),
                    selectedUserPlatforms: [],
                    platformUsername: "{{ $platformUsername ?: 'Jhon Doe' }}",
                    platformPicture: "{!! $platformPicture ?: custom_theme_url('/assets/img/avatars/avatar-1.jpg') !!}",
                    currentPlatform: '{{ $current_platform }}',
                    personalizedContent: '{{ $is_personalized_content }}',
                    selectedCompany: '{{ $company_id }}',
                    selectedProduct: '{{ $product_id }}',
                    selectedCampaign: '{{ $campaign_id }}',
                    scheduledAt: '{{ $scheduled_at }}',
                    isRepeated: '{{ $is_repeated }}',
                    repeatPeriod: '{{ $repeat_period }}',
                    repeatStartDate: '{{ $repeat_start_date }}',
                    repeatTime: null,
                    content: `{!! $content !!}`,
                    image: '{{ $postImage }}',
                    video: '{{ $video }}',
                    link: '{{ $link }}',
                    companies: @json($companies_list),
                    campaigns: @json($campaigns_list),
                    tone: '{{ $tone }}',
                    socialMediaPlatformId: '{{ $social_media_platform_id }}',
                    generatingImage: false,
                    generatingVideo: false,
                    generatingContent: false,
                    init() {
                        const pageContentWrap = document.querySelector('.lqd-page-content-wrap');

                        if (pageContentWrap) {
                            pageContentWrap.style.overflow = 'visible';
                        }

                        this.$watch('currentPlatform', this.onPlatformChange);
                    },

                    onPlatformChange(value) {
                        this.currentPlatform = value;
                        window.history.replaceState(null, null, `?platform=${value}`);
                    },

                    getUserPlatforms() {
                        return this.userPlatforms.filter(userPlatform => userPlatform.platform ===
                            this.currentPlatform);
                    },

                    async postNow() {
                        let form = this.$refs.form;
                        let formData = new FormData(
                        form); // Formu serialize etmeden direkt kullanıyoruz
                        formData.append('post_now', 1);
                        formData.append('social_media_platform', this.currentPlatform);
                        try {
                            let response = await fetch(
                                "{{ route('dashboard.user.social-media.post.store') }}", {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        "Accept": "application/json"
                                    },
                                    body: formData // JSON yerine FormData nesnesini direkt gönderiyoruz
                                });

                            let result = await response.json();

                            if (result.status === 'success') {
                                toastr.success(result.message);
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('dashboard.user.social-media.post.index') }}';
                                }, 500);
                            } else {
                                toastr.error(result.message);
                            }
                        } catch (error) {
                            console.error("Hata:", error);
                        }
                    },
                    async schedulePost() {

                        let form = this.$refs.form;
                        let formData = new FormData(
                        form); // Formu serialize etmeden direkt kullanıyoruz
                        formData.append('post_now', 0);
                        formData.append('social_media_platform', this.currentPlatform);
                        try {
                            let response = await fetch(
                                "{{ route('dashboard.user.social-media.post.store') }}", {
                                    method: "POST",
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute(
                                            'content'),
                                        "Accept": "application/json"
                                    },
                                    body: formData // JSON yerine FormData nesnesini direkt gönderiyoruz
                                });

                            let result = await response.json();

                            if (result.status === 'success') {
                                toastr.success(result.message);
                                setTimeout(() => {
                                    window.location.href =
                                        '{{ route('dashboard.user.social-media.post.index') }}';
                                }, 500);
                            } else {
                                toastr.error(result.message);
                            }
                            console.log("Başarılı:", result);
                        } catch (error) {
                            console.error("Hata:", error);
                        }
                    },
                    async uploadVideo(event) {
                        const input = event.target;
                        const file = input.files[0];

                        if (!file) return;

                        let formData = new FormData();
                        formData.append('upload_video', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        try {
                            const response = await fetch(
                                '{{ route('dashboard.user.social-media.upload.video') }}', {
                                    method: 'POST',
                                    body: formData,
                                });
                            const data = await response.json();

                            if (data && data.video_path) {
                                this.video = data.video_path;
                            } else {
                                console.error('Expected data not returned from server', data);
                                toastr.error(
                                    '{{ __('Expected data not returned from server') }}');
                            }
                        } catch (error) {
                            console.error('Error occurred while uploading the image', error);
                            toastr.error(
                                '{{ __('Error occurred while uploading the image') }}');
                        }
                    },
                    async uploadImage(event) {
                        const input = event.target;
                        const file = input.files[0];

                        if (!file) return;

                        let formData = new FormData();
                        formData.append('upload_image', file);
                        formData.append('_token', '{{ csrf_token() }}');

                        try {
                            const response = await fetch(
                                '{{ route('dashboard.user.social-media.upload.image') }}', {
                                    method: 'POST',
                                    body: formData,
                                });
                            const data = await response.json();

                            if (data && data.image_path) {
                                this.image = data.image_path;
                            } else {
                                console.error('Expected data not returned from server', data);
                                toastr.error(
                                    '{{ __('Expected data not returned from server') }}');
                            }
                        } catch (error) {
                            console.error('Error occurred while uploading the image', error);
                            toastr.error(
                                '{{ __('Error occurred while uploading the image') }}');
                        }
                    },
                    async generateVideo() {
                        if (!this.content || !this.content.trim().length) {
                            return toastr.error(
                                '{{ __('Please enter some content before generating an video.') }}'
                                );
                        }

                        const formData = new FormData();

                        const prompt =
                            `{{ __('Create a short, visually captivating vertical video (9:16 format) optimized for TikTok. The video should align with the message: "${this.content}" and reflect the tone, style, and core message to maximize viewer engagement. Make it dynamic, aesthetic, and platform-native — include smooth transitions, energetic or emotional visuals (depending on tone), and scenes that match the storytelling flow. The video should not include any on-screen text, as captions or overlays will be added later. Focus on mood, movement, and storytelling through visuals only.') }}`;
                        formData.append('prompt', prompt);

                        try {
                            this.generatingVideo = true;

                            const response = await fetch(
                                '{{ route('dashboard.user.social-media.video.generate') }}', {
                                    method: 'POST',
                                    body: formData,
                                });
                            const data = await response.json();

                            if (data.status === 'success') {
                                this.getVideoStatus();
                            }
                        } catch (e) {
                            toastr.error(e.message);
                            this.generatingVideo = false;
                        } finally {

                        }
                    },
                    async getVideoStatus() {
                        fetch('{{ route('dashboard.user.social-media.video.status') }}', {
                            method: 'GET',
                            headers: {
                                'Accept': 'application/json'
                            }
                        }).then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw new Error(errorData.message ||
                                        'An unknown error occurred');
                                });
                            }
                            return response.json();
                        }).then(data => {


                            if (data.status === 'error') {
                                throw new Error(data.message);
                            }

                            if (data.status === 'COMPLETED') {
                                this.generatingVideo = false;

                                this.video = data.video_path;

                            } else {
                                setTimeout(() => {
                                    this.getVideoStatus();
                                }, 1000);
                            }
                        }).catch(error => {
                            this.generatingVideo = false;

                            toastr.error(error?.message || error);
                        });
                    },
                    async generateImage() {
                        if (!this.content || !this.content.trim().length) {
                            return toastr.error(
                                '{{ __('Please enter some content before generating an image.') }}'
                                );
                        }

                        const prompt =
                            `{{ __('Generate a visually engaging image for a social media post on ${this.currentPlatform}. The image should align with the following post content: ${this.content}, while being eye-catching, relevant, and optimized for the platform’s recommended dimensions. The image should reflect the tone, style, and message to drive engagement. Do not include any text in the image.') }}`;
                        // const formData = new FormData();

                        @include('social-media::post.includes.image-script')


                        try {
                            this.generatingImage = true;

                            const response = await fetch('/dashboard/user/openai/generate', {
                                method: 'POST',
                                body: formData,
                            });
                            const data = await response.json();

                            if (data.status === 'success') {
                                const images = data.images;

                                if (images[0]) {
                                    const image = images[0];
                                    const output = image.output;
                                    const filesList = new DataTransfer();

                                    this.image = output;

                                    filesList.items.add(new File([output], data.nameOfImage, {
                                        type: 'image/png'
                                    }));

                                    this.$refs.uploadImage.files = filesList.files;
                                }

                                @if (setting('social_media_image_model') === 'flux-pro' || setting('social_media_image_model') === 'flux-pro-kontext')
                                    if (data.requestId) {
                                        this.getImageStatus(data.requestId);
                                    }
                                @endif


                            } else {
                                toastr.error(data.message);
                            }
                        } catch (error) {
                            toastr.error(error.message);
                        } finally {
                            this.generatingImage = false;
                        }
                    },
                    async getImageStatus(requestId) {
                        fetch('{{ route('dashboard.user.social-media.image.get.status') }}?request_id=' +
                            requestId, {
                                method: 'GET',
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }).then(response => {
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw new Error(errorData.message ||
                                        'An unknown error occurred');
                                });
                            }
                            return response.json();
                        }).then(data => {
                            if (data.status === 'success') {
                                const image = data.data;
                                const output = image.output;
                                const filesList = new DataTransfer();

                                this.image = output;

                                filesList.items.add(new File([output], data.data
                                    .nameOfImage, {
                                        type: 'image/png'
                                    }));

                                this.$refs.uploadImage.files = filesList.files;
                            } else {
                                setTimeout(() => {
                                    this.getImageStatus(requestId);
                                }, 1000);
                            }
                        }).catch(error => {
                            toastr.error(error?.message || error);
                        });
                    },
                    async generateContent() {

                        {{-- if(this.personalizedContent) { --}}
                        {{--	if(!this.selectedCompany) { --}}
                        {{--		return toastr.error('{{ __('Please select a company first.') }}'); --}}
                        {{--	} --}}

                        {{--	if(!this.selectedProduct) { --}}
                        {{--		return toastr.error('{{ __('Please select a product first.') }}'); --}}
                        {{--	} --}}

                        {{--	if(!this.selectedCampaign) { --}}
                        {{--		return toastr.error('{{ __('Please select a campaign first.') }}'); --}}
                        {{--	} --}}
                        {{-- } --}}

                        if (!this.content || !this.content.trim().length) {
                            return toastr.error(
                            '{{ __('Please enter some content first.') }}');
                        }

                        const formData = new FormData();
                        formData.append('campaign_id', this.selectedCampaign);
                        formData.append('is_personalized_content', (this.personalizedContent ?
                            1 : 0));
                        formData.append('selected_company', this.selectedCompany);
                        formData.append('selected_product', this.selectedProduct);
                        formData.append('selected_platform', this.currentPlatform);
                        formData.append('social_media_platform_id', this.socialMediaPlatformId);
                        formData.append('content', this.content);
                        formData.append('tone', this.tone);
                        formData.append('platform', this.currentPlatform);

                        try {
                            this.generatingContent = true;

                            const response = await fetch(
                                '{{ route('dashboard.user.social-media.campaign.generate') }}', {
                                    method: 'POST',
                                    body: formData,
                                });
                            const data = await response.json();

                            if (data.result) {
                                this.content = data.result;
                            } else {
                                if (data.message) {
                                    toastr.error(data.message);
                                    return;
                                }
                                toastr.error('{{ __('Failed to generate content.') }}');
                            }
                        } catch (error) {
                            toastr.error(error.message);
                        } finally {
                            this.generatingContent = false;
                        }
                    },
                }));
            });
        })();
    </script>
@endpush
