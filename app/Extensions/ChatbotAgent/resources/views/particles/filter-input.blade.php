<x-forms.input
	class:label="text-heading-foreground"
	label="{{ __('') }}"
	name="chatbot_channel"
	size="lg"
	type="select"
	x-model="chatbot_channel"
	x-on:change="fetchChats(true)"
>
	<option value="all">@lang('All Channel')</option>
	<option value="frame">@lang('Frame')</option>
	@if(\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-telegram'))
		<option value="telegram">@lang('Telegram')</option>
	@endif
	@if(\App\Helpers\Classes\MarketplaceHelper::isRegistered('chatbot-whatsapp'))
		<option value="whatsapp">@lang('Whatsapp')</option>
	@endif
</x-forms.input>
