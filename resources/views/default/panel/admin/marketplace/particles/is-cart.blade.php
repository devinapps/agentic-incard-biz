@php
    $cart_color = in_array($item['id'], $cartExists) ? 'text-green-500' : 'text-current';
@endphp

@if (!$item['licensed'] && $item['price'] && $item['is_buy'])
    @if (isset($item['parent']))

        @if (\App\Helpers\Classes\MarketplaceHelper::isRegistered($item['parent']['slug']))

            @if (in_array($item['slug'], ['whatsapp', 'telegram', 'facebook', 'instagram']))
                @if (\App\Helpers\Classes\MarketplaceHelper::getDbVersion($item['parent']['slug']) >= $item['parent']['min_version'])
                    @if ($item['only_premium'])
                        @if ($item['check_subscription'])
                            <x-button
                                class="relative ms-2 hover:bg-foreground/10"
                                data-toogle="cart"
                                href="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                                variant="ghost-shadow"
                            >
                                <x-tabler-shopping-cart
                                    id="{{ $item['id'] . '-icon' }}"
                                    @class(['size-7', $cart_color])
                                />
                            </x-button>
                        @else
                            {{--							<x-button --}}
                            {{--								class="relative ms-2 hover:bg-foreground/10" --}}
                            {{--								variant="ghost-shadow" --}}
                            {{--								href="#" --}}
                            {{--								onclick="return toastr.info('This extension is for premium customers only.')" --}}
                            {{--							> --}}
                            {{--								<x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}" --}}
                            {{--														@class(["size-7", $cart_color])/> --}}
                            {{--							</x-button> --}}
                        @endif
                    @else
                        <x-button
                            class="relative ms-2 hover:bg-foreground/10"
                            data-toogle="cart"
                            href="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                            variant="ghost-shadow"
                        >
                            <x-tabler-shopping-cart
                                id="{{ $item['id'] . '-icon' }}"
                                @class(['size-7', $cart_color])
                            />
                        </x-button>
                    @endif
                @else
                    <x-button
                        class="relative ms-2 hover:bg-foreground/10"
                        variant="ghost-shadow"
                        href="#"
                        onclick="return toastr.info('{{ $item['parent']['min_version_message'] }}')"
                    >
                        <x-tabler-shopping-cart
                            id="{{ $item['id'] . '-icon' }}"
                            @class(['size-7', $cart_color])
                        />
                    </x-button>
                @endif
            @else
                @if ($item['only_premium'])
                    @if ($item['check_subscription'])
                        <x-button
                            class="relative ms-2 hover:bg-foreground/10"
                            data-toogle="cart"
                            href="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                            variant="ghost-shadow"
                        >
                            <x-tabler-shopping-cart
                                id="{{ $item['id'] . '-icon' }}"
                                @class(['size-7', $cart_color])
                            />
                        </x-button>
                    @else
                        {{--						<x-button --}}
                        {{--							class="relative ms-2 hover:bg-foreground/10" --}}
                        {{--							variant="ghost-shadow" --}}
                        {{--							href="#" --}}
                        {{--							onclick="return toastr.info('This extension is for premium customers only.')" --}}
                        {{--						> --}}
                        {{--							<x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}" --}}
                        {{--													@class(["size-7", $cart_color])/> --}}
                        {{--						</x-button> --}}
                    @endif
                @else
                    <x-button
                        class="relative ms-2 hover:bg-foreground/10"
                        data-toogle="cart"
                        href="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                        variant="ghost-shadow"
                    >
                        <x-tabler-shopping-cart
                            id="{{ $item['id'] . '-icon' }}"
                            @class(['size-7', $cart_color])
                        />
                    </x-button>
                @endif
            @endif
        @else
            <x-button
                class="relative ms-2 hover:bg-foreground/10"
                onclick="return toastr.info('{{ $item['parent']['message'] }}')"
                variant="ghost-shadow"
                href="#"
            >
                <x-tabler-shopping-cart
                    id="{{ $item['id'] . '-icon' }}"
                    @class(['size-7', $cart_color])
                />
            </x-button>

        @endif
    @else
        @if ($item['only_premium'])
            @if ($item['check_subscription'])
                <x-button
                    class="relative ms-2 hover:bg-foreground/10"
                    data-toogle="cart"
                    href="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                    variant="ghost-shadow"
                >
                    <x-tabler-shopping-cart
                        id="{{ $item['id'] . '-icon' }}"
                        @class(['size-7', $cart_color])
                    />
                </x-button>
            @else
                {{--						<x-button --}}
                {{--							class="relative ms-2 hover:bg-foreground/10" --}}
                {{--							variant="ghost-shadow" --}}
                {{--							href="#" --}}
                {{--							onclick="return toastr.info('This extension is for premium customers only.')" --}}
                {{--						> --}}
                {{--							<x-tabler-shopping-cart id="{{ $item['id'].'-icon' }}" --}}
                {{--													@class(["size-7", $cart_color])/> --}}
                {{--						</x-button> --}}
            @endif
        @else
            <x-button
                class="relative ms-2 hover:bg-foreground/10"
                data-toogle="cart"
                href="{{ route('dashboard.admin.marketplace.cart.add-delete', $item['id']) }}"
                variant="ghost-shadow"
            >
                <x-tabler-shopping-cart
                    id="{{ $item['id'] . '-icon' }}"
                    @class(['size-7', $cart_color])
                />
            </x-button>
        @endif
    @endif

@endif
