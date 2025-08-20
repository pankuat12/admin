<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportProductsRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'file' => ['required','file','mimetypes:text/plain,text/csv','max:20480'], // 20MB
            'duplicate_strategy' => ['nullable','in:update,skip'],
        ];
    }
}
