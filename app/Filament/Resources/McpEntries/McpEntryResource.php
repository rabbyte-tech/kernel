<?php

namespace App\Filament\Resources\McpEntries;

use App\Filament\Resources\McpEntries\Pages\ListMcpEntries;
use App\Filament\Resources\McpEntries\Schemas\McpEntryInfolist;
use App\Filament\Resources\McpEntries\Tables\McpEntriesTable;
use App\Models\McpEntry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class McpEntryResource extends Resource
{
    protected static ?string $model = McpEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCpuChip;

    protected static ?int $navigationSort = 5;

    public static function getModelLabel(): string
    {
        return __('mcp_entry.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('mcp_entry.model_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.kernel');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return McpEntriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return McpEntryInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMcpEntries::route('/'),
        ];
    }
}
