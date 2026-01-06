<?php

namespace App\Filament\Resources\WebhookDeliveries\Schemas;

use App\Enums\WebhookDeliveryStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WebhookDeliveryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(['default' => 2])->components([
                    Grid::make(['default' => 1])->components([
                        Section::make(__('webhook_delivery.sections.details'))
                            ->schema([
                                TextEntry::make('event_name')
                                    ->label(__('webhook_delivery.fields.event')),
                                TextEntry::make('status')
                                    ->label(__('webhook_delivery.fields.status'))
                                    ->badge()
                                    ->color(function (WebhookDeliveryStatus|string|null $state): string {
                                        $value = $state instanceof WebhookDeliveryStatus ? $state->value : $state;

                                        return match ($value) {
                                            WebhookDeliveryStatus::Succeeded->value => 'success',
                                            WebhookDeliveryStatus::Failed->value => 'danger',
                                            default => 'gray',
                                        };
                                    }),
                                TextEntry::make('attempt')
                                    ->label(__('webhook_delivery.fields.attempt')),
                                TextEntry::make('next_attempt_at')
                                    ->dateTime()
                                    ->label(__('webhook_delivery.fields.next_attempt')),
                                TextEntry::make('webhookSubscription.name')
                                    ->label(__('webhook_delivery.fields.subscription')),
                            ])
                            ->columns(2),
                        Section::make(__('webhook_delivery.sections.metadata'))
                            ->schema([
                                TextEntry::make('created_at')
                                    ->dateTime()
                                    ->label(__('webhook_delivery.fields.created_at')),
                            ])
                            ->columns(1),
                    ]),
                    Grid::make(['default' => 1])->components([
                        Section::make(__('webhook_delivery.sections.payload'))
                            ->schema([
                                TextEntry::make('payload')
                                    ->label(__('webhook_delivery.fields.request_payload'))
                                    ->columnSpanFull()
                                    ->formatStateUsing(function (mixed $state): string {
                                        if (is_array($state)) {
                                            return json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}';
                                        }

                                        if (is_string($state) && $state !== '') {
                                            $decoded = json_decode($state, true);

                                            if (is_array($decoded)) {
                                                return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}';
                                            }

                                            return $state;
                                        }

                                        return '{}';
                                    })
                                    ->html()
                                    ->extraAttributes(['class' => 'whitespace-pre-wrap font-mono text-sm']),
                            ]),
                        Section::make(__('webhook_delivery.sections.error'))
                            ->schema([
                                TextEntry::make('last_error')
                                    ->label(__('webhook_delivery.fields.last_error'))
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'whitespace-pre-wrap font-mono text-sm']),
                            ])
                            ->visible(fn (mixed $record): bool => ! blank($record->last_error)),

                    ]),
                ])->columns(2),
            ])->columns(1);
    }
}
