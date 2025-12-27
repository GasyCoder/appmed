<?php

return [
    'mock' => (bool) env('CHATBOT_MOCK', true),

    'faq_disk' => env('CHATBOT_FAQ_DISK', 'local'),
    'faq_path' => env('CHATBOT_FAQ_PATH', 'chatbot/faq.json'),

    'assistant' => [
        'name'         => env('CHATBOT_NAME', 'Assistant EPIRC'),
        'product'      => env('CHATBOT_PRODUCT', 'EPIRC'),
        'builder'      => env('CHATBOT_BUILDER', 'GasyCoder'),
        'organization' => env('CHATBOT_ORG', 'Université de Mahajanga'),
        'engine'       => env('CHATBOT_ENGINE', 'FAQ locale + Données (Programmes/Documents) + Option IA'),
    ],
];
