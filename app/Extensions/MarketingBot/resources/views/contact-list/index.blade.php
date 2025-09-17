@extends('panel.layout.app', ['disable_tblr' => true])
@section('title', trans('Contact'))

@section('titlebar_actions')

    <div class="flex gap-4 lg:justify-end">
        <x-button href="{{ route('dashboard.user.marketing-bot.contact-list.create') }}">
            <x-tabler-plus class="size-4" />
            {{ __('Add New Contact') }}
        </x-button>

    </div>
@endsection

@section('content')
    <div class="py-10">
        <x-table>
            <x-slot:head>
                <th>
                    {{ __('ID') }}
                </th>
                <th>
                    {{ __('Name') }}
                </th>
                <th>
                    {{ __('phone') }}
                </th>

                <th class="text-end">
                    {{ __('Action') }}
                </th>
            </x-slot:head>

            <x-slot:body>
                @foreach ($items as $item)
                    <tr>
                        <td>
                            {{ $item->id }}
                        </td>
                        <td>
                            {{ $item->name }}
                        </td>
                        <td>
                            {{ $item->phone }}
                        </td>
                        <td class="whitespace-nowrap text-end">
                            @if ($app_is_demo)
                                <x-button
                                    class="size-9"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    onclick="return toastr.info('This feature is disabled in Demo version.')"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-tabler-x class="size-4" />
                                </x-button>
                            @else
                                <x-button
                                    class="size-9"
                                    href="{{ route('dashboard.user.marketing-bot.contact-list.edit', $item->id) }}"
                                    variant="ghost-shadow"
                                    size="none"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-tabler-edit class="size-4" />
                                </x-button>
                                <x-button
                                    class="size-9"
                                    data-delete="delete"
                                    data-delete-link="{{ route('dashboard.user.marketing-bot.contact-list.destroy', $item->id) }}"
                                    variant="ghost-shadow"
                                    hover-variant="danger"
                                    size="none"
                                    title="{{ __('Delete') }}"
                                >
                                    <x-tabler-x class="size-4" />
                                </x-button>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </x-slot:body>
        </x-table>

        <div class="mt-3 flex justify-end">
            {{ $items->links() }}
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            "use strict";
            $('#submit').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let data = $(this).serialize();

                $.post(form.attr('action'), data, function(data) {}).done(function(data) {
                    if (data.status === 'success') {
                        toastr.success(data.message);

                        setTimeout(function() {
                            window.location.reload();
                        }, 600);

                        return;
                    }

                    if (data.message) {
                        toastr.error(data.message);
                        return;
                    }

                    toastr.error('Something went wrong!');
                }).fail(function(e) {
                    if (e?.responseJSON?.message) {
                        toastr.error(e.responseJSON.message);
                    } else {
                        toastr.error('Something went wrong!');
                    }
                });
            });

            $('[data-delete="delete"]').on('click', function(e) {
                console.log('salam');

                if (!confirm('Are you sure you want to delete this contact?')) {
                    return;
                }

                let deleteLink = $(this).data('delete-link');

                $.ajax({
                    url: deleteLink,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        if (data.status === 'success') {
                            toastr.success(data.message);

                            setTimeout(function() {
                                window.location.reload();
                            }, 600);

                            return;
                        }

                        if (data.message) {
                            toastr.error(data.message);
                            return;
                        }

                        toastr.error('Something went wrong!');
                    },
                    error: function(e) {
                        if (e?.responseJSON?.message) {
                            toastr.error(e.responseJSON.message);
                        } else {
                            toastr.error('Something went wrong!');
                        }
                    }
                });
            });
        });
    </script>
@endpush
