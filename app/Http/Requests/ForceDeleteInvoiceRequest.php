<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Exceptions\HttpResponseException;

class ForceDeleteInvoiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->hasRole('Administrador');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'reason' => 'required|string|min:10|max:500',
            'password' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'reason.required' => 'La razón de eliminación permanente es obligatoria.',
            'reason.string' => 'La razón debe ser texto.',
            'reason.min' => 'La razón debe tener al menos 10 caracteres.',
            'reason.max' => 'La razón no puede exceder 500 caracteres.',
            'password.required' => 'La contraseña es obligatoria para confirmar la acción.',
            'password.string' => 'La contraseña debe ser texto.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!Hash::check(request('password'), Auth::user()->password)) {
                $validator->errors()->add('password', 'La contraseña ingresada es incorrecta.');
            }
        });
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        $response = back()->withErrors([
            'error' => 'Solo los administradores pueden eliminar facturas permanentemente.'
        ]);
        throw new HttpResponseException($response);
    }
}
