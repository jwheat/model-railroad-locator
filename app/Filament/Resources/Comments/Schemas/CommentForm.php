<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Author')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('parent_id')
                    ->relationship('parent', 'id')
                    ->label('Reply to')
                    ->searchable()
                    ->preload(),
                Textarea::make('body')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
