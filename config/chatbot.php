<?php

return [
    // true = utilise faq.json (mock), false = appelle Anthropic
    'mock' => env('CHATBOT_MOCK', true),

    // disk storage (ex: local, public, s3, etc.)
    'faq_disk' => env('CHATBOT_FAQ_DISK', 'local'),

    // chemin relatif dans le disk
    // avec ton test tinker: storage/app/private/chatbot/faq.json => disk local + path chatbot/faq.json (car root = storage/app/private)
    'faq_path' => env('CHATBOT_FAQ_PATH', 'chatbot/faq.json'),
];
