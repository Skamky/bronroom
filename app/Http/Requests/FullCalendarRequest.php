<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FullCalendarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    /**
     * Если входной параметр `rooms` является закодированным в JSON параметром - пытаемся преобразовать его в массив
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $rooms = $this->input('rooms');

        if (is_string($rooms) && json_validate($rooms)) {
            $this->merge([
                'rooms' => json_decode($rooms, true),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rooms' => 'required|array',
            'rooms.*' => 'integer|exists:rooms,id',
            'start' => 'required',
            'end' => 'required',

        ];
    }
}
