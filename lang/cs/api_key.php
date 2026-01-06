<?php

return [
    'model' => 'API Klíč',
    'model_plural' => 'API Klíče',
    'sections' => [
        'details' => 'Detaily',
    ],
    'fields' => [
        'name' => 'Název',
        'roles' => 'Role',
        'status' => 'Stav',
        'last_used_at' => 'Naposledy použito',
        'created_at' => 'Vytvořeno',
    ],
    'actions' => [
        'enable' => 'Povolit',
        'disable' => 'Zakázat',
    ],
    'status' => [
        'enabled' => 'Povoleno',
        'disabled' => 'Zakázáno',
        'unknown' => 'Neznámé',
    ],
    'messages' => [
        'created' => 'API klíč byl úspěšně vytvořen',
        'token_body' => 'Váš API token: :token',
    ],
];
