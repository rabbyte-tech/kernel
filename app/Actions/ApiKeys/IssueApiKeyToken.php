<?php

namespace App\Actions\ApiKeys;

use App\Models\ApiKey;
use RabbyteTech\Support\Actions\DomainAction;

class IssueApiKeyToken extends DomainAction
{
    protected function ability(): ?string
    {
        return 'api-keys.create';
    }

    public function execute(ApiKey $apiKey, string $name): string
    {
        $this->authorize();

        $token = $apiKey->createToken($name, []);

        return $token->plainTextToken;
    }
}
