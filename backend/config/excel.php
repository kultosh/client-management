<?php

return [
    'imports' => [
        'chunk_size' => env('IMPORT_CHUNK_SIZE', 1000),
    ],
    'exports' => [
        'chunk_size' => env('EXPORT_CHUNK_SIZE', 1000),
    ],
];