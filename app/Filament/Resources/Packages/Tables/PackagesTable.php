<?php

namespace App\Filament\Resources\Packages\Tables;

use App\Actions\Packages\SetPackageStatus;
use App\Enums\PackageStatus;
use App\Models\Package;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PackagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('package.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('version')
                    ->label(__('package.fields.version'))
                    ->sortable(),
                TextColumn::make('status')
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
                            PackageStatus::Installed->value => 'gray',
                            PackageStatus::Enabled->value => 'success',
                            PackageStatus::Disabled->value => 'danger',
                            default => 'gray',
                        };
                    })
                    ->sortable(),
                TextColumn::make('manifest_hash')
                    ->label(__('package.fields.manifest_hash'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('package.fields.created_at'))
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
                        ->label(__('package.actions.enable'))
                        ->color('success')
                        ->icon(Heroicon::CheckCircle)
                        ->requiresConfirmation()
                        ->visible(fn (Package $record): bool => $record->status !== PackageStatus::Enabled)
                        ->action(function (Package $record, SetPackageStatus $setPackageStatus): void {
                            $setPackageStatus->execute($record, PackageStatus::Enabled);
                        }),
                    Action::make('disable')
                        ->label(__('package.actions.disable'))
                        ->color('danger')
                        ->icon(Heroicon::XCircle)
                        ->requiresConfirmation()
                        ->visible(fn (Package $record): bool => $record->status === PackageStatus::Enabled)
                        ->action(function (Package $record, SetPackageStatus $setPackageStatus): void {
                            $setPackageStatus->execute($record, PackageStatus::Disabled);
                        }),
                ]),
            ])
            ->toolbarActions([]);
    }
}
