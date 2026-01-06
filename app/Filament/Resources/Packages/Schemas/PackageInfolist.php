<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Enums\PackageStatus;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'md' => 2,
                ])->components([
                    Section::make(__('package.sections.info'))->components([
                        TextEntry::make('name')
                            ->label(__('package.fields.name')),
                        TextEntry::make('version')
                            ->label(__('package.fields.version')),
                        TextEntry::make('status')
                            ->label(__('package.fields.status'))
                            ->badge()
                            ->formatStateUsing(function (PackageStatus|string|null $state): string {
                                $value = $state instanceof PackageStatus ? $state->value : $state;

                                return match ($value) {
                                    PackageStatus::Installed->value => __('package.status.installed'),
                                    PackageStatus::Enabled->value => __('package.status.enabled'),
                                    PackageStatus::Disabled->value => __('package.status.disabled'),
                                    default => __('package.status.unknown'),
                                };
                            })
                            ->color(function (PackageStatus|string|null $state): string {
                                $value = $state instanceof PackageStatus ? $state->value : $state;

                                return match ($value) {
                                    PackageStatus::Enabled->value => 'success',
                                    PackageStatus::Disabled->value => 'danger',
                                    default => 'gray',
                                };
                            }),
                    ])->columns(2),
                    Section::make(__('package.sections.meta'))->components([
                        TextEntry::make('manifest_hash')
                            ->label(__('package.fields.manifest_hash')),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->label(__('package.fields.created_at')),
                    ]),

                ]),
            ])->columns(1);
    }
}
