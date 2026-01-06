<?php

namespace App\Filament\Resources\McpEntries\Schemas;

use App\Enums\McpPrimitiveType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class McpEntryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'md' => 2,
                ])->components([
                    Section::make(__('mcp_entry.sections.info'))
                        ->schema([
                            TextEntry::make('package.name')
                                ->label(__('mcp_entry.fields.package')),
                            TextEntry::make('type')
                                ->label(__('mcp_entry.fields.type'))
                                ->badge()
                                ->formatStateUsing(fn (McpPrimitiveType|string|null $state): string => $state instanceof McpPrimitiveType ? $state->value : ($state ?? '')),
                            TextEntry::make('name')
                                ->label(__('mcp_entry.fields.name')),
                            TextEntry::make('permission')
                                ->label(__('mcp_entry.fields.permission')),
                            TextEntry::make('is_enabled')
                                ->label(__('mcp_entry.fields.status'))
                                ->badge()
                                ->formatStateUsing(fn (?bool $state): string => match ($state) {
                                    true => __('mcp_entry.status.enabled'),
                                    false => __('mcp_entry.status.disabled'),
                                    default => __('mcp_entry.status.unknown'),
                                })
                                ->color(fn (?bool $state): string => $state === null ? 'gray' : ($state ? 'success' : 'danger')),
                        ])->columns(2),
                    Section::make(__('mcp_entry.sections.meta'))
                        ->schema([
                            TextEntry::make('class')
                                ->label(__('mcp_entry.fields.class')),
                            TextEntry::make('updated_at')
                                ->dateTime()
                                ->label(__('mcp_entry.fields.updated_at')),
                        ]),
                ]),
            ])->columns(1);
    }
}
