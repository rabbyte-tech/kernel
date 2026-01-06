<?php

namespace App\Filament\Resources\Roles\Schemas;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('role.sections.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('role.fields.name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        Select::make('permissions')
                            ->label(__('role.fields.permissions'))
                            ->relationship('permissions', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Hidden::make('guard_name')
                            ->default(config('auth.defaults.guard')),
                    ]),
            ]);
    }
}
