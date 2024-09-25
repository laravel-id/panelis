<?php

namespace App\Livewire\Participants;

use App\Actions\Transaction\CreatePaymentUrl;
use App\Enums\Participants\BloodType;
use App\Enums\Participants\Gender;
use App\Enums\Participants\IdentityType;
use App\Enums\Participants\Relation;
use App\Mail\Participants\RegisteredMail;
use App\Models\Event\Package;
use App\Models\Event\Schedule;
use App\Notifications\Participants\RegisteredNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Register extends Component
{
    public ?Schedule $schedule = null;

    #[Validate]
    public ?int $package = null;

    #[Validate]
    public string $idType = '';

    #[Validate('required|numeric')]
    public string $idNumber = '';

    #[Validate]
    public string $name = '';

    #[Validate]
    public string $gender = '';

    #[Validate]
    public string $bloodType = '';

    #[Validate('required|date')]
    public string $birthdate = '';

    #[Validate('required')]
    public string $phone = '';

    #[Validate('email')]
    public string $email = '';

    #[Validate('required|min:3')]
    public string $emergencyName = '';

    #[Validate('required')]
    public string $emergencyPhone = '';

    #[Validate]
    public string $emergencyRelation = '';

    #[Validate('accepted')]
    public bool $accepted = false;

    public function mount(string $slug): void
    {
        $this->schedule = Schedule::getScheduleBySlug($slug);
        $this->schedule->load('packages');

        $this->package = request('package');
    }

    public function rules(): array
    {
        return [
            'package' => [
                'required',
                'exists:packages,id',
            ],
            'idType' => [
                'required',
                Rule::in(array_column(IdentityType::cases(), 'value')),
            ],
            'name' => [
                'required',
                'min:3',
                'max:100',
                'regex:/^[\pL\s\-]+$/u',
            ],
            'gender' => [
                'required',
                Rule::in(array_column(Gender::cases(), 'value')),
            ],
            'bloodType' => [
                'required',
                Rule::in(array_column(BloodType::cases(), 'value')),
            ],
            'emergencyRelation' => [
                'required',
                Rule::in(array_column(Relation::cases(), 'value')),
            ],
        ];
    }

    public function validationAttributes(): array
    {
        return [
            'idType' => __('event.participant_id_type'),
            'idNumber' => __('event.participant_id_number'),
            'name' => __('event.participant_name'),
            'birthdate' => __('event.participant_birthdate'),
            'bloodType' => __('event.participant_blood_type'),
            'gender' => __('event.participant_gender'),
            'phone' => __('event.participant_phone'),
            'email' => __('event.participant_email'),
            'emergencyName' => __('event.participant_emergency_name'),
            'emergencyPhone' => __('event.participant_emergency_phone'),
            'emergencyRelation' => __('event.participant_emergency_relation'),
        ];
    }

    public function register()
    {
        $this->validate();

        $participant = DB::transaction(function (): Model {
            $start = 1000;
            $counter = $this->schedule->participants()->count();

            $prefix = match ($this->gender) {
                Gender::Male->value => 'M',
                Gender::Female->value => 'F',
            };

            $bib = sprintf('%s%s', $prefix, $start + $counter);

            $package = Package::query()->findOrFail($this->package);

            $participant = $this->schedule->participants()->create([
                'user_id' => Auth::id(),
                'package_id' => $package->id,
                'bib' => $bib,
                'id_type' => $this->idType,
                'id_number' => $this->idNumber,
                'name' => $this->name,
                'gender' => $this->gender,
                'blood_type' => $this->bloodType,
                'birthdate' => $this->birthdate,
                'phone' => $this->phone,
                'email' => $this->email,
                'emergency_name' => $this->emergencyName,
                'emergency_phone' => $this->emergencyPhone,
                'emergency_relation' => $this->emergencyRelation,
            ]);

            $transaction = $participant->transaction()->create([
                'total' => $package->price,
                'expired_at' => now()->addHours(6),
                'metadata' => [
                    'customer' => [
                        'name' => $participant->name,
                        'phone' => $participant->phone,
                        'email' => $participant->email,
                    ],
                ],
            ]);

            $transaction->items()->create([
                'name' => $package->title,
                'price' => $package->price,
            ]);

            //            $payment = CreatePaymentUrl::run($transaction);
            //
            //            $metadata = $transaction->metadata;
            //            $metadata['payment_url'] = $payment->getPaymentUrl();
            //            $transaction->metadata = $metadata;
            //            $transaction->save();

            return $participant;
        });

        if (! empty($participant->email)) {
            Mail::to($participant->email)
                ->locale(data_get($this->schedule->metadata, 'locale', config('app.locale')))
                ->send(new RegisteredMail($participant));
        }

        Notification::routes([
            'mail' => data_get($this->schedule->metadata, 'notification_email'),
            'slack' => data_get($this->schedule->metadata, 'notification_slack_channel_id'),
        ])->notify(new RegisteredNotification($participant));

        //        $this->reset();
        //
        //        return redirect()->route('participant.status', $participant->ulid);
    }

    public function render(): View
    {
        abort_if(empty($this->schedule->metadata['registration']), Response::HTTP_NOT_FOUND);

        set_locale($this->schedule->metadata['locale'] ?? app()->getLocale());

        seo()->title(__('event.schedule_registration', ['title' => $this->schedule->title]), false);

        return view('livewire.participants.register')
            ->with('title', __('event.schedule_registration', ['title' => $this->schedule->title]))
            ->extends('layouts.app');
    }
}
