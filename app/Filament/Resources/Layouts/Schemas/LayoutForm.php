<?php

namespace App\Filament\Resources\Layouts\Schemas;

use App\LayoutScale;
use App\LayoutVisibility;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LayoutForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ownership')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('owner', 'name')
                            ->label('Owner')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->required(),
                        Select::make('club_id')
                            ->relationship('club', 'name')
                            ->searchable()
                            ->preload(),
                    ]),
                Section::make('Layout details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->helperText('Leave blank to generate from the name.')
                            ->unique(ignoreRecord: true),
                        Select::make('scale')
                            ->options(LayoutScale::options())
                            ->required(),
                        TextInput::make('era')
                            ->placeholder('e.g. Transition era, 1950s'),
                        Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),
                    ]),
                Section::make('Location')
                    ->columns(2)
                    ->schema([
                        TextInput::make('city'),
                        TextInput::make('region'),
                        TextInput::make('country')
                            ->maxLength(2)
                            ->placeholder('US'),
                        TextInput::make('latitude')
                            ->numeric()
                            ->step('0.0000001'),
                        TextInput::make('longitude')
                            ->numeric()
                            ->step('0.0000001'),
                    ]),
                Section::make('Publishing')
                    ->columns(2)
                    ->schema([
                        Select::make('visibility')
                            ->options(LayoutVisibility::options())
                            ->default(LayoutVisibility::Public->value)
                            ->required(),
                        Toggle::make('is_featured'),
                        FileUpload::make('cover_image_path')
                            ->image()
                            ->disk('public')
                            ->directory('layouts')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
