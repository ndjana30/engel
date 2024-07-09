<?php

namespace App\Http\Requests\Cours;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoursRequest extends FormRequest
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
            'cours_id' => 'integer|required|exists:cours,id',
            'titre' => 'string',
            'resume' => 'string',
            'image' => 'nullable|file|mimes:jpeg,png,jpg',
            'matiere_id' => 'exists:matieres,id',
            'video' => 'nullable|file|mimes:mp4,avi,mkv|size:1000000',
        ];
    }
}
