<?php

namespace App\Filament\Support;

use Illuminate\Support\Str;

/**
 * For create/edit pages of records with a "sections" column: stored flat as [{type, ...fields}],
 * edited in a Builder as [uuid => {type, data: {...fields}}].
 */
trait ConvertsSections
{
    public static function sectionsToBuilder(?array $sections): array
    {
        $items = [];
        foreach ($sections ?? [] as $item) {
            $type = $item['type'] ?? 'html';
            unset($item['type']);
            $items[(string) Str::uuid()] = ['type' => $type, 'data' => $item];
        }

        return $items;
    }

    public static function sectionsFromBuilder(?array $items): array
    {
        return collect($items ?? [])
            ->map(fn ($item) => ['type' => $item['type']] + array_filter($item['data'] ?? [],
                fn ($v, $k) => $v !== null && ! str_contains((string) $k, '__') && ! ($k === 'hidden' && ! $v), ARRAY_FILTER_USE_BOTH))
            ->values()->all();
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (array_key_exists('sections', $data)) {
            $data['sections'] = static::sectionsToBuilder($data['sections']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (array_key_exists('sections', $data)) {
            $data['sections'] = static::sectionsFromBuilder($data['sections']);
        }

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateFormDataBeforeSave($data);
    }
}
