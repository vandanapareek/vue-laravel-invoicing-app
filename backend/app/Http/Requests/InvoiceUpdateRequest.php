<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentTerms;

class InvoiceUpdateRequest extends FormRequest
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
            'clientName' => 'required_if:status,pending|nullable|string|max:100',
            'clientEmail' => 'required_if:status,pending|nullable|email|max:254',
            'paymentTerms' => ['required_if:status,pending', 'nullable', 'in:1,7,14,30', new Enum(PaymentTerms::class)],
            'createdAt' => 'required_if:status,pending|nullable|date',
            'items' => 'required_if:status,pending|nullable|array',
            'items.*.name' => 'required_if:status,pending|nullable|string|max:60',
            'items.*.quantity' => [
                'required_if:status,pending',
                'integer',
                'max:999999',
                function($attribute, $value, $fail) {
                    if ($value === '' || is_string($value)) {
                        $fail('The '.$attribute.' must be an integer and not an empty string.');
                    }
                }
            ],
            'items.*.price' => [
                'required_if:status,pending',
                'numeric',
                'min:0',
                'max:999999.99',
                'regex:/^\d{1,8}(\.\d{1,2})?$/',
                function($attribute, $value, $fail) {
                    if ($value === '' || is_string($value)) {
                        $fail('The '.$attribute.' must be a number and not an empty string.');
                    }
                }
            ],
            'status' => ['required', new Enum(InvoiceStatus::class), 'in:draft,pending'],
            'senderAddress.street' => 'nullable|string|max:100',
            'senderAddress.city' => 'nullable|string|max:50',
            'senderAddress.postCode' => 'nullable|string|max:10',
            'senderAddress.country' => 'nullable|string|max:60',
            'clientAddress.street' => 'nullable|string|max:100',
            'clientAddress.city' => 'nullable|string|max:50',
            'clientAddress.postCode' => 'nullable|string|max:10',
            'clientAddress.country' => 'nullable|string|max:60',
            'description' => 'required_if:status,pending|nullable|string|max:1000',
        ];
    }

    public function withValidator($validator)
    {
        $validator->sometimes('items', 'min:1', function ($input) {
            return $input->status === 'pending';
        });
    }
}
