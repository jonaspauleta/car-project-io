<?php

declare(strict_types=1);

namespace App\Filament\Resources\Organizers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class OrganizerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
                TextInput::make('website')
                    ->url(),
                TextInput::make('logo_url')
                    ->url(),
            ]);
    }
}
