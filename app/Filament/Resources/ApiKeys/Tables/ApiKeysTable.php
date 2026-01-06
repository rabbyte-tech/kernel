<?php

namespace App\Filament\Resources\ApiKeys\Tables;

use App\Models\ApiKey;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ApiKeysTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withMax('tokens', 'last_used_at'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('api_key.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label(__('api_key.fields.roles'))
                    ->badge()
                    ->separator(', ')
                    ->toggleable(),
                TextColumn::make('is_active')
                    ->label(__('api_key.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (?bool $state): string => match ($state) {
                        true => __('api_key.status.enabled'),
                        false => __('api_key.status.disabled'),
                        default => __('api_key.status.unknown'),
                    })
                    ->color(fn (?bool $state): string => $state === null ? 'gray' : ($state ? 'success' : 'danger'))
                    ->sortable(),
                TextColumn::make('last_used_at')
                    ->label(__('api_key.fields.last_used_at'))
                    ->dateTime()
                    ->getStateUsing(fn (ApiKey $record): mixed => $record->tokens_max_last_used_at)
                    ->sortable(query: fn (Builder $query, string $direction): Builder => $query->orderBy('tokens_max_last_used_at', $direction))
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label(__('api_key.fields.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('enable')
                        ->label(__('api_key.actions.enable'))
                        ->color('success')
                        ->icon(Heroicon::CheckCircle)
                        ->requiresConfirmation()
                        ->visible(fn (ApiKey $record): bool => ! $record->is_active)
                        ->action(fn (ApiKey $record): bool => $record->update(['is_active' => true])),
                    Action::make('disable')
                        ->label(__('api_key.actions.disable'))
                        ->color('danger')
                        ->icon(Heroicon::XCircle)
                        ->requiresConfirmation()
                        ->visible(fn (ApiKey $record): bool => $record->is_active)
                        ->action(fn (ApiKey $record): bool => $record->update(['is_active' => false])),
                ]),
            ])
            ->toolbarActions([]);
    }
}
