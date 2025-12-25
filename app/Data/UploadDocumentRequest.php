<?php

namespace App\Data;

use Illuminate\Http\UploadedFile;

class UploadDocumentRequest
{
    /**
     * @param array<int, \Livewire\Features\SupportFileUploads\TemporaryUploadedFile|\Illuminate\Http\UploadedFile> $files
     * @param array<int, string> $titles
     * @param array<int, bool|int|string> $statuses
     * @param array<int, string> $urls
     */
    public function __construct(
        public int $uploadedBy,
        public int $niveauId,

        public int $ueId,
        public ?int $ecId,

        // IMPORTANT: programme_id stocké dans documents (UE si pas d’EC, sinon EC)
        public int $programmeId,

        public int $parcourId,
        public int $semestreId,

        public array $files = [],
        public array $titles = [],
        public array $statuses = [],
        public array $urls = [],
    ) {}
}
