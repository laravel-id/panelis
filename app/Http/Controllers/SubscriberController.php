<?php

namespace App\Http\Controllers;

use App\Http\Requests\Subscribers\SubscribeRequest;
use App\Mail\Subscribers\ConfirmationMail;
use App\Mail\Subscribers\SubscribedMail;
use App\Mail\Subscribers\UnsubscribedMail;
use App\Models\Subscriber;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class SubscriberController extends Controller
{
    public function form(): View
    {
        return view('pages.subscribers.form')
            ->with('periods', Subscriber::getPeriods())
            ->with('title', __('subscriber.title'));
    }

    public function submit(SubscribeRequest $request): RedirectResponse
    {
        $subscriber = Subscriber::query()->updateOrCreate(['email' => $request->input('email')], $request->validated());
        if (! $subscriber->wasRecentlyCreated && $subscriber->is_subscribed) {
            return redirect()
                ->back()
                ->with('error', __('subscriber.message_already_subscribed'));
        }

        Mail::to($subscriber->email)->send(new ConfirmationMail($subscriber));

        return redirect()
            ->back()
            ->with('success', __('subscriber.message_need_confirmation'));
    }

    public function subscribe(string $key): View
    {
        try {
            $subscriber = Subscriber::query()
                ->where('confirmation_key', trim($key))
                ->whereNull('subscribed_at')
                ->firstOrFail();

            $subscriber->subscribe();

            Mail::to($subscriber->email)->send(new SubscribedMail($subscriber->fresh()));

            return view('pages.subscribers.subscribed')
                ->with('title', __('subscriber.subscribed'));
        } catch (Exception) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }

    public function unsubscribe(string $key): View
    {
        try {
            $subscriber = Subscriber::query()
                ->whereNotNull('subscribed_at')
                ->where('confirmation_key', trim($key))
                ->firstOrFail();

            $subscriber->unsubscribe();

            Mail::to($subscriber->email)->send(new UnsubscribedMail($subscriber));

            return view('pages.subscribers.unsubscribed')
                ->with('title', __('subscriber.unsubscribed'));
        } catch (Exception) {
            abort(Response::HTTP_NOT_FOUND);
        }
    }
}
