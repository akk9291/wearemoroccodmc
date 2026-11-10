<?php

namespace App\Filament\Resources\CaseStudyResource\Pages;

use App\Filament\Resources\CaseStudyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCaseStudy extends CreateRecord
{
    use \App\Filament\Support\ConvertsSections;

    protected static string $resource = CaseStudyResource::class;
}
