<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // exige login
    }

    public function rules(): array
    {
        return [
            'rating'    => 'required|integer|min:1|max:5',
            'title'     => 'nullable|string|max:120',
            'body'      => 'required|string|min:10|max:2000',
            'parent_id' => 'nullable|exists:reviews,id',
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Selecciona una puntuación.',
            'rating.min'      => 'La puntuación mínima es 1.',
            'rating.max'      => 'La puntuación máxima es 5.',
            'body.required'   => 'Escribe un comentario.',
            'body.min'        => 'El comentario es muy corto.',
        ];
    }
}
