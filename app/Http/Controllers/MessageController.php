<?php

namespace App\Http\Controllers;

use App\Http\Requests\Messages\SubmitRequest;
use App\Models\Message;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function form(): View
    {
        return view('pages.messages.form')
            ->with('title', __('message.contact'));
    }

    public function submit(SubmitRequest $request): RedirectResponse
    {
        Message::query()->create($request->validated());

        return redirect()
            ->back()
            ->with('success', __('message.sent_success'));
    }
}
