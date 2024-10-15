<?php

namespace App\Notifications\Participants;

use App\Filament\Resources\Event\ParticipantResource\Pages\EditParticipant;
use App\Mail\Participants\RegisteredNotification as RegisteredMail;
use App\Models\Event\Participant;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Notification;

class RegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private readonly Participant|Model $participant)
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
        return FilamentNotification::make()
            ->info()
            ->title(__('event.notification_new_participant', [
                'participant' => $this->participant->name,
                'event' => $this->participant->schedule->title,
            ]))
            ->actions([
                Action::make('view_participant')
                    ->label(__('event.view_participant'))
                    ->url(EditParticipant::getUrl(['record' => $this->participant->id])),
            ])
            ->icon('heroicon-s-user')
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

        return (new RegisteredMail($this->participant))
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
