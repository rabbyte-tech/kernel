<?php

namespace App\Filament\Resources\WebhookSubscriptions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WebhookSubscriptionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('webhook_subscription.sections.details'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('webhook_subscription.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('url')
                            ->label(__('webhook_subscription.fields.url'))
                            ->required()
                            ->url()
                            ->maxLength(255),
                        TextInput::make('event')
                            ->label(__('webhook_subscription.fields.event'))
                            ->required(),
                        TextInput::make('secret')
                            ->label(__('webhook_subscription.fields.secret'))
                            ->required()
                            ->password()
                            ->maxLength(255),
                        Toggle::make('is_active')
                            ->label(__('webhook_subscription.fields.is_active'))
                            ->default(true),
                    ]),
            ]);
    }
}
