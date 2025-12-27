<?php

return function ($document, callable $fileMeta): array {

    $m = $fileMeta($document);

    // ARCHIVE UI
    $isArchived = (bool) ($document->is_archive ?? false);
    $archivePillClass = 'bg-amber-50 text-amber-700 ring-amber-200
                        dark:bg-amber-900/20 dark:text-amber-200 dark:ring-amber-900/40';
    $archiveCardTone = $isArchived
        ? 'ring-2 ring-amber-400/30 dark:ring-amber-500/20'
        : '';

    // TEACHER INFO (safe)
    $grade = $document->uploader?->profil?->grade ?? null;
    $teacherName = $document->uploader?->name ?? null;

    // COUNTERS / SIZE
    $views = (int) ($document->view_count ?? 0);
    $downloads = (int) ($document->download_count ?? 0);

    $sizeLabel = $document->formatted_size
        ?? ($document->file_size ? number_format($document->file_size / 1024 / 1024, 1) . ' MB' : '-');

    $isExternal = (bool) ($m['isExternal'] ?? false);
    $isExternalWeb = (bool) ($m['isExternalWeb'] ?? false);
    $isGoogle = (bool) ($m['isGoogle'] ?? false);

    // RÈGLE: compteurs visibles uniquement si ce n’est pas une page web
    $showCounters = !$isExternalWeb;
    $showViewsCounter = $showCounters;

    // URL Consultation
    $consultUrl = $isExternal
        ? route('document.openExternal', $document)
        : route('document.viewer', $document);

    // Autoriser consulter
    $canConsult = $isExternal ? true : (bool) $document->isViewerLocalType();

    // URL Download
    $downloadUrl = null;
    if (!$isExternal) {
        $downloadUrl = route('document.download', $document);
    } else {
        // Externe: download uniquement si Google (Drive/Docs) pour que la route compte
        $downloadUrl = $isGoogle ? route('document.downloadExternal', $document) : null;
    }

    $showDownloadCounter = $showCounters && !empty($downloadUrl);

    $teacherLabel = trim(($grade ?? '') . ' ' . ($teacherName ?? ''));
    if ($teacherLabel === '') $teacherLabel = 'Enseignant non défini';

    return compact(
        'm',
        'isArchived','archivePillClass','archiveCardTone',
        'grade','teacherName','teacherLabel',
        'views','downloads','sizeLabel',
        'isExternal','isExternalWeb','isGoogle',
        'showViewsCounter','showDownloadCounter',
        'consultUrl','canConsult','downloadUrl'
    );
};
