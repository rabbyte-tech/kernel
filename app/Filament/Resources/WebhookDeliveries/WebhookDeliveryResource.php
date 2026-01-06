<?php

namespace App\Filament\Resources\WebhookDeliveries;

use App\Filament\Resources\WebhookDeliveries\Pages\ListWebhookDeliveries;
use App\Filament\Resources\WebhookDeliveries\Pages\ViewWebhookDelivery;
use App\Filament\Resources\WebhookDeliveries\Schemas\WebhookDeliveryInfolist;
use App\Filament\Resources\WebhookDeliveries\Tables\WebhookDeliveriesTable;
use App\Models\WebhookDelivery;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WebhookDeliveryResource extends Resource
{
    protected static ?string $model = WebhookDelivery::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?int $navigationSort = 7;

    public static function getModelLabel(): string
    {
        return __('webhook_delivery.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('webhook_delivery.model_plural');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('nav.kernel');
    }

    public static function infolist(Schema $schema): Schema
    {
        return WebhookDeliveryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WebhookDeliveriesTable::configure($table);
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
            'index' => ListWebhookDeliveries::route('/'),
            'view' => ViewWebhookDelivery::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
