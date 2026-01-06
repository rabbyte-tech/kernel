<?php

namespace App\Filament\Resources\ApiKeys\Pages;

use App\Actions\ApiKeys\IssueApiKeyToken;
use App\Filament\Resources\ApiKeys\ApiKeyResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateApiKey extends CreateRecord
{
    protected static string $resource = ApiKeyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function afterCreate(): void
    {
        $action = app(IssueApiKeyToken::class);
        $plainTextToken = $action->execute($this->record, 'Initial Token');

        Notification::make('api-key-created')
            ->title(__('api_key.messages.created'))
            ->body(__('api_key.messages.token_body', ['token' => $plainTextToken]))
            ->warning()
            ->persistent()
            ->send();
    }
}
