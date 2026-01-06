<?php

namespace App\Filament\Resources\WebhookSubscriptions\Tables;

use App\Models\WebhookSubscription;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class WebhookSubscriptionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('webhook_subscription.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('url')
                    ->label(__('webhook_subscription.fields.url'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('event')
                    ->label(__('webhook_subscription.fields.event'))
                    ->toggleable(),
                TextColumn::make('is_active')
                    ->label(__('webhook_subscription.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (?bool $state): string => match ($state) {
                        true => __('webhook_subscription.status.enabled'),
                        false => __('webhook_subscription.status.disabled'),
                        default => __('webhook_subscription.status.unknown'),
                    })
                    ->color(fn (?bool $state): string => $state === null ? 'gray' : ($state ? 'success' : 'danger'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('webhook_subscription.fields.created_at'))
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
                        ->label(__('webhook_subscription.actions.enable'))
                        ->color('success')
                        ->icon(Heroicon::CheckCircle)
                        ->requiresConfirmation()
                        ->visible(fn (WebhookSubscription $record): bool => ! $record->is_active)
                        ->action(fn (WebhookSubscription $record): bool => $record->update(['is_active' => true])),
                    Action::make('disable')
                        ->label(__('webhook_subscription.actions.disable'))
                        ->color('danger')
                        ->icon(Heroicon::XCircle)
                        ->requiresConfirmation()
                        ->visible(fn (WebhookSubscription $record): bool => $record->is_active)
                        ->action(fn (WebhookSubscription $record): bool => $record->update(['is_active' => false])),
                ]),
            ])
            ->toolbarActions([]);
    }
}
