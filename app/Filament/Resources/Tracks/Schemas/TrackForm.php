<?php

declare(strict_types=1);

namespace App\Filament\Resources\Tracks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TrackForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('country')
                    ->required(),
                TextInput::make('city')
                    ->required(),
                TextInput::make('latitude')
                    ->required()
                    ->numeric(),
                TextInput::make('longitude')
                    ->required()
                    ->numeric(),
                TextInput::make('website')
                    ->url(),
                TextInput::make('noise_limit')
                    ->numeric(),
            ]);
    }
}
