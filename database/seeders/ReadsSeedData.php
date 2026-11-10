<?php

namespace Database\Seeders;

/** Seed data lives in database/seeders/data/*.json (extracted from the original site by tools/extract_content.py). */
trait ReadsSeedData
{
    protected function data(string $name): array
    {
        return json_decode(file_get_contents(database_path("seeders/data/{$name}.json")), true, flags: JSON_THROW_ON_ERROR);
    }

    /** Keep only keys that are real columns / fillable on the model. */
    protected function only(string $model, array $row): array
    {
        $fillable = (new $model)->getFillable();

        return array_intersect_key($row, array_flip($fillable));
    }
}
