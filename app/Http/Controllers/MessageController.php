<?php

namespace App\Http\Controllers;

use App\Http\Requests\Messages\SubmitRequest;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Message\NewNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function form(): View
    {
        seo()->title(__('message.contact'), false)
            ->openGraphSite(config('app.name'));

        return view('pages.messages.form')
            ->with('title', __('message.contact'));
    }

    public function submit(SubmitRequest $request): RedirectResponse
    {
        $message = Message::query()->create($request->validated());

        Notification::send(
            notifiables: User::query()->whereDoesntHave('roles')->get(),
            notification: new NewNotification($message),
        );

        return redirect()
            ->back()
            ->with('success', __('message.sent_success'));
    }
}
