<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if (!$this->has('description')) {
            $this->merge(['description' => null]);
        }

        if (!$this->has('title')) {
            $this->merge(['title' => null]);
        }
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    //    public function authorize(): bool
    //    {
    //        return false;
    //    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'start_date' => 'required|date|before:end_date',
            'end_date' => 'required|date|after:start_date',
            'room_id' => 'required|exists:rooms,id',
        ];
    }
}
