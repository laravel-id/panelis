<?php

namespace App\Notifications\Participants;

use App\Filament\Resources\Event\ParticipantResource\Pages\EditParticipant;
use App\Mail\Participants\PaidNotification as PaidMail;
use App\Models\Event\Participant;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class PaidNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Participant $participant)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = $notifiable->pivot?->channels;
        if (! empty($channels) && json_validate($channels)) {
            return json_decode($channels, true);
        }

        return [];
    }

    public function toDatabase(): array
    {
        return FilamentNotification::make('paid_notification')
            ->success()
            ->title(__('event.notification_participant_paid', [
                'name' => $this->participant->name,
            ]))
            ->actions([
                Action::make('view_participant')
                    ->label(__('event.view_participant'))
                    ->url(EditParticipant::getUrl(['record' => $this->participant->id])),
            ])
            ->getDatabaseMessage();
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): Mailable
    {
        $address = $notifiable instanceof AnonymousNotifiable
            ? $notifiable->routeNotificationFor('mail')
            : $notifiable->email;

        return (new PaidMail($this->participant))
            ->to($address);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
