<?php

namespace App\Filament\Resources\JournalCategoryResource\Pages;

use App\Filament\Resources\JournalCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJournalCategory extends EditRecord
{
    protected static string $resource = JournalCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
