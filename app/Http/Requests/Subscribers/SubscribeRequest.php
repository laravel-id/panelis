<?php

namespace App\Http\Requests\Subscribers;

use App\Filament\Resources\SubscriberResource\Enums\SubscriberPeriod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SubscribeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:150'],
            'period' => [
                'required',
                Rule::in(array_keys(SubscriberPeriod::options())),
            ],
            'confirmation_key' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'confirmation_key' => Str::random(40),
        ]);
    }
}
