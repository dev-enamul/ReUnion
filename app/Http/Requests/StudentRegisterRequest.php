<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StudentRegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'profile_picture' => 'nullable',
            'phone' => 'required|string|max:20',
            'guardiant' => 'nullable|string|max:255',
            'passing_year' => 'nullable|integer',
            'exam_name' => 'nullable|string|max:255',
            'profession_id' => 'required|exists:professions,id',
            'profession_details' => 'nullable|string',

            'present_village' => 'nullable|string|max:255',
            'present_post' => 'nullable|string|max:255',
            'present_upazila' => 'nullable|string|max:255',
            'present_zila' => 'nullable|string|max:255',

            'permanent_village' => 'nullable|string|max:255',
            'permanent_post' => 'nullable|string|max:255',
            'permanent_upazila' => 'nullable|string|max:255',
            'permanent_zila' => 'nullable|string|max:255',
        ];
    }
}
