<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreHazardRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_name' => ['required', 'string', 'max:255'],
            'hazard_category' => ['required', 'in:Road Hazard,Environmental Hazard,Building Hazard'],
            'hazard_description' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'reported_at' => ['required', 'date'],
            'device_info' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Get the validation error messages.
     */
    public function messages(): array
    {
        return [
            'user_name.required' => 'The user name field is required.',
            'hazard_category.required' => 'The hazard category field is required.',
            'hazard_description.required' => 'The hazard description field is required.',
            'latitude.required' => 'The latitude field is required.',
            'longitude.required' => 'The longitude field is required.',
            'reported_at.required' => 'The reported_at field is required.',
        ];
    }
}
