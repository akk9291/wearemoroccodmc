<?php

namespace App\Http\Requests\Forms;

/** Newsletter sign-up (footer on every page, journal page). */
class NewsletterRequest extends WebsiteFormRequest
{
    protected $errorBag = 'newsletter';

    protected function fields(): array
    {
        return ['email' => ['required', 'email', 'max:190']];
    }

    public function messages(): array
    {
        return ['email.required' => 'Please add a valid email.', 'email.email' => 'Please add a valid email.'];
    }
}
