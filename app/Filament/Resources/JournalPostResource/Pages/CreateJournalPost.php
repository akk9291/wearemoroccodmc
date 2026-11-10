<?php

namespace App\Filament\Resources\JournalPostResource\Pages;

use App\Filament\Resources\JournalPostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateJournalPost extends CreateRecord
{
    use \App\Filament\Support\ConvertsSections;

    protected static string $resource = JournalPostResource::class;
}
