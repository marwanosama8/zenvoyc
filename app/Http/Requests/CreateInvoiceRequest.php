<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
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

            'ref_number' => ['required', 'string'],
            'rgnr' => ['required', 'string', 'max:50'],
            // 'customer_address' => ['nullable', 'string'],
            // 'date_origin' => ['required', 'date'],
            // 'date_start' => ['required', 'date', 'after_or_equal:date_origin'],
            // 'date_end' => ['required', 'date', 'after_or_equal:date_start'],
            // 'date_pay' => ['required', 'date', 'after_or_equal:date_end'],
            // 'rate' => ['nullable', 'numeric', 'between:0,999999.99'],
            // 'info' => ['nullable', 'string'],
            // 'vat_percent' => ['required', 'numeric', 'between:0,100'],
            // 'has_vat' => ['required', 'boolean'],
            // 'send' => ['required', 'boolean'],
            // 'payed' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
{
    return [
        'ref_number.required' => 'A title is required',
        'rgnr.required' => 'A message is required',
    ];
}
}
