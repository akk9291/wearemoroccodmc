<?php

namespace App\Http\Requests\Forms;

/** Inquiry drawer (#inq-form), on every page. */
class InquiryRequest extends WebsiteFormRequest
{
    protected $errorBag = 'inquiry';

    protected function fields(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'dates' => ['nullable', 'string', 'max:120'],
            'travelers' => ['nullable', 'string', 'max:120'],
            'intent' => ['nullable', 'string', 'max:60'],
            'note' => ['nullable', 'string', 'max:5000'],
            'brief' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please add your name.',
            'email.required' => 'Please add a valid email.',
            'email.email' => 'Please add a valid email.',
        ];
    }
}
