<?php

namespace App\Filament\Resources\McpEntries\Pages;

use App\Filament\Resources\McpEntries\McpEntryResource;
use Filament\Resources\Pages\ListRecords;

class ListMcpEntries extends ListRecords
{
    protected static string $resource = McpEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
