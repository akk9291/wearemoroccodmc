<?php

namespace App\Http\Requests\Forms;

use Illuminate\Foundation\Http\FormRequest;

/** Shared bits of the public website forms: honeypot field and the page the form was sent from. */
abstract class WebsiteFormRequest extends FormRequest
{
    /** Name of the hidden field real visitors never fill in. */
    public const HONEYPOT = 'website';

    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, mixed> */
    abstract protected function fields(): array;

    public function rules(): array
    {
        return $this->fields() + [
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
            'source_page' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function isSpam(): bool
    {
        return filled($this->input(self::HONEYPOT));
    }

    /** Path of the page the form was on ("/about"): posted by the form, else taken from the Referer header. */
    public function sourcePage(): ?string
    {
        $path = $this->input('source_page') ?: parse_url((string) $this->headers->get('referer'), PHP_URL_PATH);
        if (! is_string($path) || $path === '') {
            return null;
        }

        return mb_substr('/'.ltrim($path, '/'), 0, 255);
    }

    /** Validated submitted values without the technical fields. */
    public function values(): array
    {
        return collect($this->validated())->except([self::HONEYPOT, 'source_page', 'privacy'])->all();
    }
}
