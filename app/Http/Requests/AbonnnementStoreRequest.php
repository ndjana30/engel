<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AbonnnementStoreRequest extends FormRequest
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
          'client_number'=>'required|string|min:8',
          'user_number'=>'required|string|min:8',
          'concours_id' => 'required|exists:concours,id|integer',
        //   'user_id' => 'required|exists:users,id|integer',
        ];
    }
}
