<?php

return [
    'max_files' => 10,

    'allowed_extensions' => [
        'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx',
        'jpg', 'jpeg', 'png'
    ],

    'convert_to_pdf' => ['doc', 'docx', 'ppt', 'pptx'],

    // Quand on reçoit un lien "direct" (non Google)
    'allowed_direct_url_extensions' => [
        'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png'
    ],
];
