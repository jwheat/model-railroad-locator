<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\PostStatus;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Story')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        TextInput::make('slug')
                            ->helperText('Leave blank to generate from the title.')
                            ->unique(ignoreRecord: true),
                        Textarea::make('excerpt')
                            ->rows(2)
                            ->helperText('Optional summary shown on cards and search results.'),
                        Textarea::make('body')
                            ->label('Body (Markdown)')
                            ->rows(18)
                            ->required()
                            ->helperText('Markdown is rendered to safe HTML. Raw HTML is stripped.'),
                    ]),
                Section::make('Publishing')
                    ->columns(2)
                    ->schema([
                        Select::make('user_id')
                            ->relationship('author', 'name')
                            ->label('Author')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->required(),
                        Select::make('status')
                            ->options(PostStatus::options())
                            ->default(PostStatus::Draft->value)
                            ->required(),
                        DateTimePicker::make('published_at'),
                        Toggle::make('allow_comments')
                            ->default(true),
                        Select::make('layout_id')
                            ->relationship('layout', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('club_id')
                            ->relationship('club', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('tags')
                            ->relationship('tags', 'name')
                            ->multiple()
                            ->preload()
                            ->columnSpanFull(),
                        FileUpload::make('cover_image_path')
                            ->image()
                            ->disk('public')
                            ->directory('posts')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
