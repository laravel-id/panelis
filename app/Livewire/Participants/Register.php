<?php

namespace App\Livewire\Participants;

use App\Enums\Participants\BloodType;
use App\Enums\Participants\Gender;
use App\Enums\Participants\IdentityType;
use App\Enums\Participants\Relation;
use App\Livewire\Participants\Pipelines\CreateParticipant;
use App\Livewire\Participants\Pipelines\CreatePayment;
use App\Livewire\Participants\Pipelines\CreateTransaction;
use App\Livewire\Participants\Pipelines\SendEmail;
use App\Livewire\Participants\Pipelines\SendNotification;
use App\Models\Event\Participant;
use App\Models\Event\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;

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

    public function mount(string $slug): Redirector|RedirectResponse|null
    {
        $this->schedule = Schedule::getScheduleBySlug($slug);
        if (data_get($this->schedule->metadata, 'registration_need_auth', false) && Auth::guest()) {
            return to_route('login');
        }

        $this->schedule->load('packages');

        $this->package = request('package');

        return null;
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

    public function register(): Redirector|RedirectResponse
    {
        $this->validate();

        $participant = DB::transaction(function (): Participant {
            return app(Pipeline::class)
                ->send($this->all())
                ->through([
                    CreateParticipant::class,
                    CreateTransaction::class,
                    CreatePayment::class,
                    SendEmail::class,
                    SendNotification::class,
                ])
                ->thenReturn();
        });

        $this->reset();

        return redirect()->route('participant.status', $participant->ulid);
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
