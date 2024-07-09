<?php

namespace App\Http\Requests\Cours;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoursRequest extends FormRequest
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
            'titre' => 'required|string',
            'resume' => 'required|string',
            'image' => 'file|mimes:jpeg,png,jpg|nullable',
            'matiere_id' => 'required|exists:matieres,id',
            'video' => 'required|file|mimes:mp4,avi,mkv',
        ];
    }
}
