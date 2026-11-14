<?php

namespace App\Http\Requests\Forms;

/** Design-your-stay builder on /quote: the six answers, sent through the inquiry drawer with name and email. */
class QuoteRequestForm extends InquiryRequest
{
    protected function fields(): array
    {
        $answer = ['nullable', 'string', 'max:500'];

        return parent::fields() + [
            'pace' => $answer, 'mood' => $answer, 'party' => $answer, 'length' => $answer,
            'lead' => $answer, 'stays' => $answer, 'prio' => $answer,
        ];
    }
}
