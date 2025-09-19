<?php

namespace App\Filament\Resources\Modifications\Pages;

use App\Filament\Resources\Modifications\ModificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModification extends EditRecord
{
    protected static string $resource = ModificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
