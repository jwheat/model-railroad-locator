<?php

namespace App\Filament\Resources\Clubs\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClubForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Club')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->helperText('Leave blank to generate from the name.')
                            ->unique(ignoreRecord: true),
                        Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                        TextInput::make('website')
                            ->url(),
                        Toggle::make('is_verified'),
                        FileUpload::make('logo_path')
                            ->image()
                            ->disk('public')
                            ->directory('clubs')
                            ->columnSpanFull(),
                    ]),
                Section::make('Location')
                    ->columns(3)
                    ->schema([
                        TextInput::make('city'),
                        TextInput::make('region'),
                        TextInput::make('country')
                            ->maxLength(2)
                            ->placeholder('US'),
                    ]),
            ]);
    }
}
