<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkripsiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'nim' => ['required', 'string', 'regex:/^[0-9]+$/', 'max:30'],
            'nama' => ['required', 'string', 'max:100'],
            'judul' => ['required', 'string', 'max:255'],
            'abstrak' => ['nullable', 'string'],
            'prodi' => ['required', 'in:PTIK,TEKOM'],
            'tahun_selesai' => ['required', 'integer', 'digits:4', 'min:2010', 'max:' . date('Y')],
            'file_skripsi' => ['required', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:30720'],
            'file_preview_skripsi' => ['nullable', 'file', 'mimes:pdf', 'mimetypes:application/pdf', 'max:30720'],
        ];
    }
}
