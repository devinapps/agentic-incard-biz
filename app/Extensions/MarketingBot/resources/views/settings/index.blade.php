@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', __('Marketing Bot Settings'))

@section('titlebar_actions')

@endsection

@section('content')
    <div class="grid grid-cols-1 gap-8 py-10 lg:grid-cols-2">
        <x-card>
            <x-slot name="head">
                {{ __('Marketing Bot Telegram') }}
            </x-slot>
            <form
                action="{{ route('dashboard.user.marketing-bot.settings.telegram') }}"
                method="post"
            >
                @method('post')
                <x-forms.input
                    id="access_token"
                    size="lg"
                    label="{{ __('Access Token') }}"
                    name="access_token"
                    required
                    value="{{ $app_is_demo ? '**********' : $telegram?->access_token }}"
                />
                @if ($app_is_demo)
                    <x-button
                        class="mt-3"
                        type="button"
                        onclick="return toastr.info('This feature is disabled in Demo version.');"
                    >
                        {{ __('Save') }}
                    </x-button>
                @else
                    <x-button
                        class="mt-3"
                        type="submit"
                    >
                        {{ __('Save') }}
                    </x-button>
                @endif
            </form>
        </x-card>
        <x-card>
            <x-slot name="head">
                {{ __('Marketing Bot Whatsapp Settings') }}
            </x-slot>
            <form
                action="{{ route('dashboard.user.marketing-bot.settings.whatsapp') }}"
                method="post"
            >
                <input
                    hidden
                    name="channel"
                    value="whatsapp"
                >
                <input
                    hidden
                    name="user_id"
                    value="{{ \Illuminate\Support\Facades\Auth::id() }}"
                >
                @csrf
                <div class="mb-3">
                    <x-forms.input
                        :label="__('Whatsapp sid')"
                        name="whatsapp_sid"
                        size="lg"
                        required
                        value="{{ $app_is_demo ? '**********' : $whatsapp?->whatsapp_sid }}"
                    >
                    </x-forms.input>
                </div>
                <div class="mb-3">
                    <x-forms.input
                        :label="__('Whatsapp token')"
                        name="whatsapp_token"
                        size="lg"
                        required
                        value="{{ $app_is_demo ? '**********' : $whatsapp?->whatsapp_token }}"
                    >
                    </x-forms.input>
                </div>
                <div class="mb-3">
                    <x-forms.input
                        :label="__('Whatsapp phone')"
                        name="whatsapp_phone"
                        size="lg"
                        required
                        value="{{ $app_is_demo ? '**********' : $whatsapp?->whatsapp_phone }}"
                    >
                    </x-forms.input>
                </div>
                <div class="mb-3">
                    <x-forms.input
                        :label="__('Whatsapp sandbox phone')"
                        name="whatsapp_sandbox_phone"
                        size="lg"
                        value="{{ $app_is_demo ? '**********' : $whatsapp?->whatsapp_sandbox_phone }}"
                    >
                    </x-forms.input>
                </div>
                <div class="mb-3">
                    <x-forms.input
                        id="whatsapp_environment"
                        type="select"
                        size="lg"
                        name="whatsapp_environment"
                        label="{{ __('Environment') }}"
                    >
                        <option
                            {{ $whatsapp?->whatsapp_environment === 'sandbox' ? 'selected' : '' }}
                            value="sandbox"
                        >@lang('SANDBOX')</option>
                        <option
                            {{ $whatsapp?->whatsapp_environment === 'production' ? 'selected' : '' }}
                            value="production"
                        >@lang('PRODUCTION')</option>
                    </x-forms.input>
                </div>

                @if ($whatsapp)
                    <div class="mb-3">
                        <x-forms.input
                            :label="__('Webhook Url')"
                            name="webhook"
                            size="lg"
                            value="{{ route('api.marketing-bot.whatsapp.webhook', $whatsapp?->id) }}"
                        >
                        </x-forms.input>
                    </div>
                @endif

                @if ($app_is_demo)
                    <x-button
                        type="button"
                        onclick="return toastr.info('This feature is disabled in Demo version.');"
                    >
                        {{ __('Save') }}
                    </x-button>
                @else
                    <x-button
                        class="mt-3"
                        type="submit"
                    >
                        {{ __('Save') }}
                    </x-button>
                @endif
            </form>
        </x-card>
    </div>
@endsection

@push('script')
@endpush
