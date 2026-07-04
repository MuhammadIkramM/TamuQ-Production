<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkripsiRequest extends FormRequest
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
            'nim' => ['required', 'string', 'regex:/^[0-9]+$/', 'max:30'],
            'nama' => ['required', 'string', 'max:255'],
            'judul' => ['required', 'string', 'max:500'],
            'prodi' => ['required', 'in:PTIK,TEKOM'],
            'tahun_selesai' => ['required', 'integer', 'digits:4', 'min:2010', 'max:' . date('Y')],
            'file_skripsi' => ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:30720'],
        ];
    }
}
