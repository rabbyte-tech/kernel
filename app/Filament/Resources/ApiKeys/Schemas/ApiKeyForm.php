<?php

namespace App\Filament\Resources\ApiKeys\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ApiKeyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('api_key.sections.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('api_key.fields.name'))
                            ->required()
                            ->maxLength(255),
                        Select::make('roles')
                            ->label(__('api_key.fields.roles'))
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Toggle::make('is_active')
                            ->label(__('api_key.fields.status'))
                            ->default(true),
                    ]),
            ]);
    }
}
