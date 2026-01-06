<?php

namespace App\Filament\Resources\WebhookDeliveries\Tables;

use App\Enums\WebhookDeliveryStatus;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebhookDeliveriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]))
            ->columns([
                TextColumn::make('event_name')
                    ->label(__('webhook_delivery.fields.event'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('webhook_delivery.fields.status'))
                    ->badge()
                    ->color(function (WebhookDeliveryStatus|string|null $state): string {
                        $value = $state instanceof WebhookDeliveryStatus ? $state->value : $state;

                        return match ($value) {
                            WebhookDeliveryStatus::Pending->value => 'gray',
                            WebhookDeliveryStatus::Succeeded->value => 'success',
                            WebhookDeliveryStatus::Failed->value => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable(),
                TextColumn::make('attempt')
                    ->label(__('webhook_delivery.fields.attempt'))
                    ->sortable(),
                TextColumn::make('next_attempt_at')
                    ->dateTime()
                    ->label(__('webhook_delivery.fields.next_attempt'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('webhook_delivery.fields.created_at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                ]),
            ]);
    }
}
