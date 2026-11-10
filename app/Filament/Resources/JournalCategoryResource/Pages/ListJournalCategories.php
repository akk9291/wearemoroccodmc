<?php

namespace App\Filament\Resources\JournalCategoryResource\Pages;

use App\Filament\Resources\JournalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJournalCategories extends ListRecords
{
    protected static string $resource = JournalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
