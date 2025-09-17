<?php

namespace App\Extensions\MarketingBot\System\Http\Controllers\Whatsapp;

use App\Extensions\MarketingBot\System\Http\Requests\ContactListRequest;
use App\Extensions\MarketingBot\System\Models\Whatsapp\Contact;
use App\Extensions\MarketingBot\System\Models\Whatsapp\ContactList;
use App\Extensions\MarketingBot\System\Models\Whatsapp\Segment;
use App\Helpers\Classes\Helper;
use App\Helpers\Classes\Localization;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ContactListController extends Controller
{
    public function index()
    {
        return view('marketing-bot::contact-list.index', [
            'items' => ContactList::query()->where('user_id', Auth::id())->paginate(10),
        ]);
    }

    public function create()
    {
        return view('marketing-bot::contact-list.edit', [
            'title'            => trans('Contact Add'),
            'contacts'         => Contact::my()->get(),
            'segments'         => Segment::my()->get(),
            'selectedContacts' => [],
            'selectedSegments' => [],
            'item'             => new ContactList,
            'method'           => 'POST',
            'action'           => route('dashboard.user.marketing-bot.contact-list.store'),
            'countries'        => Localization::countyCodes(),
        ]);
    }

    public function store(ContactListRequest $request): \Illuminate\Http\RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $contactList = ContactList::query()->create($request->validated());

        $contactList->contacts()->sync($request->input('contacts'));
        $contactList->segments()->sync($request->input('segments'));

        return to_route('dashboard.user.marketing-bot.contact-list.index')->with([
            'status'  => 'success',
            'message' => __('Contact updated successfully'),
            'type'    => 'success',
        ]);
    }

    public function edit(ContactList $contactList)
    {
        return view('marketing-bot::contact-list.edit', [
            'title'            => trans('Contact Edit'),
            'contacts'         => Contact::my()->get(),
            'segments'         => Segment::query()->where('user_id', Auth::id())->get(),
            'item'             => $contactList,
            'method'           => 'PUT',
            'action'           => route('dashboard.user.marketing-bot.contact-list.update', $contactList->id),
            'selectedContacts' => $contactList->contacts()->pluck('contact_id')->toArray(),
            'selectedSegments' => $contactList->segments()->pluck('segment_id')->toArray(),
            'countries'        => Localization::countyCodes(),
        ]);
    }

    public function update(ContactListRequest $request, ContactList $contactList): \Illuminate\Http\RedirectResponse
    {
        if (Helper::appIsDemo()) {
            return back()->with([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $contactList->update($request->validated());

        $contactList->contacts()->sync($request->input('contacts'));
        $contactList->segments()->sync($request->input('segments'));

        return back()->with([
            'status'  => 'success',
            'message' => __('Contact updated successfully'),
            'type'    => 'success',
        ]);
    }

    public function destroy(ContactList $contactList): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status'  => 'error',
                'message' => trans('This feature is disabled in demo mode.'),
            ]);
        }

        $contactList->delete();

        return response()->json([
            'status'  => 'success',
            'message' => __('Contact deleted successfully'),
        ]);
    }
}
