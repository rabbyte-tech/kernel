<?php

return [
    'model' => 'Balíček',
    'model_plural' => 'Balíčky',
    'sections' => [
        'info' => 'Informace',
        'meta' => 'Meta',
    ],
    'fields' => [
        'name' => 'Název',
        'version' => 'Verze',
        'manifest_hash' => 'Hash manifestu',
        'status' => 'Stav',
        'created_at' => 'Vytvořeno',
    ],
    'actions' => [
        'enable' => 'Povolit',
        'disable' => 'Zakázat',
    ],
    'status' => [
        'installed' => 'Nainstalováno',
        'enabled' => 'Povoleno',
        'disabled' => 'Zakázáno',
        'unknown' => 'Neznámé',
    ],
];
