<?php

declare(strict_types=1);

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('track_id')
                    ->relationship('track', 'name')
                    ->required(),
                Select::make('organizer_id')
                    ->relationship('organizer', 'name')
                    ->required(),
                TextInput::make('title')
                    ->required(),
                TextInput::make('description'),
                DatePicker::make('start_date')
                    ->required(),
                DatePicker::make('end_date')
                    ->required(),
                TextInput::make('website')
                    ->url(),
            ]);
    }
}
