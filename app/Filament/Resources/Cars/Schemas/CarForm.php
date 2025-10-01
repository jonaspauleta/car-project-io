<?php

declare(strict_types=1);

namespace App\Filament\Resources\Cars\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('make')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                TextInput::make('nickname'),
                TextInput::make('vin'),
                FileUpload::make('image_url')
                    ->image(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
