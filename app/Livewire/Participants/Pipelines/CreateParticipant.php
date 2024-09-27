<?php

namespace App\Livewire\Participants\Pipelines;

use App\Enums\Participants\Gender;
use App\Models\Event\Package;
use App\Models\Event\Participant;
use Closure;
use Illuminate\Support\Facades\Auth;

class CreateParticipant
{
    public function __invoke(array $data, Closure $next): Participant
    {
        $start = 1000;
        $counter = $data['schedule']->participants()->count();

        $prefix = match (strtolower($data['gender'])) {
            Gender::Male->value => 'M',
            Gender::Female->value => 'F',
        };

        $bib = sprintf('%s%s', $prefix, $start + $counter);

        $package = Package::query()->findOrFail($data['package']);

        $participant = $data['schedule']->participants()->create([
            'user_id' => Auth::id(),
            'package_id' => $package['id'],
            'bib' => $bib,
            'id_type' => $data['idType'],
            'id_number' => $data['idNumber'],
            'name' => $data['name'],
            'gender' => $data['gender'],
            'blood_type' => $data['bloodType'],
            'birthdate' => $data['birthdate'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'emergency_name' => $data['emergencyName'],
            'emergency_phone' => $data['emergencyPhone'],
            'emergency_relation' => $data['emergencyRelation'],
        ]);

        return $next($participant);
    }
}
