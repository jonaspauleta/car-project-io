<?php

namespace App\Filament\Resources\Modifications\Pages;

use App\Filament\Resources\Modifications\ModificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListModifications extends ListRecords
{
    protected static string $resource = ModificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
