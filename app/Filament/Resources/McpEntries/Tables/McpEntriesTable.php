<?php

namespace App\Filament\Resources\McpEntries\Tables;

use App\Enums\McpPrimitiveType;
use App\Models\McpEntry;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class McpEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('package.name')
                    ->label(__('mcp_entry.fields.package'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('mcp_entry.fields.type'))
                    ->badge()
                    ->formatStateUsing(fn (McpPrimitiveType|string|null $state): string => $state instanceof McpPrimitiveType ? $state->value : ($state ?? ''))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('mcp_entry.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('permission')
                    ->label(__('mcp_entry.fields.permission'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('class')
                    ->label(__('mcp_entry.fields.class'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('is_enabled')
                    ->label(__('mcp_entry.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (?bool $state): string => match ($state) {
                        true => __('mcp_entry.status.enabled'),
                        false => __('mcp_entry.status.disabled'),
                        default => __('mcp_entry.status.unknown'),
                    })
                    ->color(fn (?bool $state): string => $state === null ? 'gray' : ($state ? 'success' : 'danger'))
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('mcp_entry.fields.updated_at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    Action::make('enable')
                        ->label(__('mcp_entry.actions.enable'))
                        ->color('success')
                        ->icon(Heroicon::CheckCircle)
                        ->requiresConfirmation()
                        ->visible(fn (McpEntry $record): bool => ! $record->is_enabled)
                        ->action(fn (McpEntry $record): bool => $record->update(['is_enabled' => true])),
                    Action::make('disable')
                        ->label(__('mcp_entry.actions.disable'))
                        ->color('danger')
                        ->icon(Heroicon::XCircle)
                        ->requiresConfirmation()
                        ->visible(fn (McpEntry $record): bool => $record->is_enabled)
                        ->action(fn (McpEntry $record): bool => $record->update(['is_enabled' => false])),
                ]),
            ]);
    }
}
