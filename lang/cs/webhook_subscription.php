<?php

return [
    'model' => 'Odběr webhooku',
    'model_plural' => 'Odběry webhooků',
    'sections' => [
        'details' => 'Detaily',
    ],
    'fields' => [
        'status' => 'Stav',
        'name' => 'Název',
        'url' => 'URL',
        'event' => 'Událost',
        'secret' => 'Tajemství',
        'is_active' => 'Aktivní',
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
];
