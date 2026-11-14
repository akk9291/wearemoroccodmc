<?php

namespace App\Http\Requests\Forms;

/** General enquiry form on /contact. */
class ContactRequest extends WebsiteFormRequest
{
    protected $errorBag = 'contact';

    protected function fields(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'message' => ['required', 'string', 'max:5000'],
            'privacy' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please add your name.',
            'email.required' => 'Please add a valid email address.',
            'email.email' => 'Please add a valid email address.',
            'message.required' => 'Please write a short message.',
            'privacy.accepted' => 'Please accept the privacy policy.',
        ];
    }
}
