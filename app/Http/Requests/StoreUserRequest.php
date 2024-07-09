<?php

namespace App\Http\Requests;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\FormRequest;
// use App\Models\User;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;//Gate::allows('create',User::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'rule_id'=>'integer|exists:rules,id',
            'name'=> ["string", "required","min:3"],
            'email'=> ["string", "required","email","unique:users,email"],
            'phone'=> ["string", "required","unique:users"],
            'profile'=> ["file", "nullable","mimes:jpeg,png,jpg"],
        ];
    }

    public function prepareForValidation(){
        $this->merge([
            'rule_id'=>2,
        ]);
    }
}
