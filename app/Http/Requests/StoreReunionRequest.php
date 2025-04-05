<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreReunionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */ 

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422)
        );
    } 
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
            'student_id' => 'required|exists:students,id', 
            'fee' => 'required|numeric',
            'payment_method' => 'required|string',
            'payment_number' => 'required|string',
            'payment_photo' => 'required|url',   
            't_shirt_size' => 'required|string', 
        ];
    }
}
