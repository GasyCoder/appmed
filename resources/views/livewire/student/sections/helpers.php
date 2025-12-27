<?php

use Illuminate\Support\Str;

return (function () {

    $getProvider = function (string $url): string {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        return match (true) {
            str_contains($host, 'drive.google') => 'Google Drive',
            str_contains($host, 'docs.google')  => 'Google Docs',
            str_contains($host, 'dropbox')      => 'Dropbox',
            str_contains($host, 'onedrive')     => 'OneDrive',
            str_contains($host, 'sharepoint')   => 'SharePoint',
            default                             => 'Lien externe',
        };
    };

    // Externe = fichier OU page web (détection robuste)
    $externalKind = function (string $url): array {
        $host = strtolower((string) (parse_url($url, PHP_URL_HOST) ?? ''));
        $path = strtolower((string) (parse_url($url, PHP_URL_PATH) ?? ''));

        // Google => fichier (même si pas d’extension)
        if (str_contains($host, 'drive.google.com') || str_contains($host, 'docs.google.com')) {
            return ['isGoogle' => true, 'isFile' => true, 'isWeb' => false, 'ext' => ''];
        }

        $ext = strtolower((string) (pathinfo($path, PATHINFO_EXTENSION) ?: ''));

        // fallback si extension dans query
        if ($ext === '') {
            $u = strtolower($url);
            foreach (['pdf','pptx','ppt','docx','doc','xlsx','xls','csv','zip','rar'] as $e) {
                if (str_contains($u, '.' . $e)) { $ext = $e; break; }
            }
        }

        $isFile = in_array($ext, ['pdf','ppt','pptx','doc','docx','xls','xlsx','csv','zip','rar'], true);

        return ['isGoogle' => false, 'isFile' => $isFile, 'isWeb' => !$isFile, 'ext' => $ext];
    };

    $fileMeta = function ($document) use ($getProvider, $externalKind): array {
        $path = (string) ($document->file_path ?? '');
        $isExternal = Str::startsWith($path, ['http://','https://']);

        $kind = $isExternal ? $externalKind($path) : ['isGoogle'=>false,'isFile'=>true,'isWeb'=>false,'ext'=>''];
        $isExternalWeb = $isExternal ? (bool) $kind['isWeb'] : false;
        $isGoogle = $isExternal ? (bool) $kind['isGoogle'] : false;

        $ext = (string) ($document->extensionFromPath() ?? '');
        if ($ext === '' && $isExternal) $ext = (string) ($kind['ext'] ?: 'link');
        if ($ext === '' && !$isExternal) $ext = 'doc';

        $isPdf = ($ext === 'pdf');
        $isPpt = in_array($ext, ['ppt','pptx'], true);
        $isDoc = in_array($ext, ['doc','docx'], true);
        $isXls = in_array($ext, ['xls','xlsx','csv'], true);

        // Badge prioritaire sur type fichier, ensuite web, ensuite lien
        $badge = match (true) {
            $isPdf         => 'PDF',
            $isPpt         => 'PPT',
            $isDoc         => 'DOC',
            $isXls         => 'XLS',
            $isExternalWeb => 'WEB',
            $isExternal    => 'LIEN',
            default        => strtoupper($ext),
        };

        $badgeClass = match (true) {
            $isExternalWeb => 'bg-slate-50 text-slate-700 ring-slate-200 dark:bg-slate-900/20 dark:text-slate-300 dark:ring-slate-800/40',
            $isExternal    => 'bg-indigo-50 text-indigo-700 ring-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-300 dark:ring-indigo-800/40',
            $isPdf         => 'bg-red-50 text-red-700 ring-red-200 dark:bg-red-900/20 dark:text-red-300 dark:ring-red-800/40',
            $isPpt         => 'bg-orange-50 text-orange-700 ring-orange-200 dark:bg-orange-900/20 dark:text-orange-300 dark:ring-orange-800/40',
            $isDoc         => 'bg-sky-50 text-sky-700 ring-sky-200 dark:bg-sky-900/20 dark:text-sky-300 dark:ring-sky-800/40',
            $isXls         => 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-300 dark:ring-emerald-800/40',
            default        => 'bg-blue-50 text-blue-700 ring-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:ring-blue-800/40',
        };

        // Icon prioritaire sur type fichier
        $icon = match (true) {
            $isPdf         => 'pdf',
            $isPpt         => 'ppt',
            $isDoc         => 'doc',
            $isXls         => 'xls',
            $isExternal    => 'link',
            default        => 'file',
        };

        $provider = $isExternal ? $getProvider($path) : null;

        return compact('isExternal','isExternalWeb','isGoogle','ext','badge','badgeClass','icon','provider','isPdf','isPpt','isDoc','isXls');
    };

    $iconSvg = function (string $name): string {
        return match ($name) {
            'pdf' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="8" y1="13" x2="8" y2="17"></line>
                <line x1="12" y1="13" x2="16" y2="13"></line>
                <line x1="12" y1="17" x2="16" y2="17"></line>
            </svg>',
            'link' => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/>
                <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
            </svg>',
            default => '<svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                <line x1="8" y1="21" x2="16" y2="21"></line>
                <line x1="12" y1="17" x2="12" y2="21"></line>
                <path d="M7 8l2 2 4-4"></path>
            </svg>',
        };
    };

    return compact('getProvider','externalKind','fileMeta','iconSvg');
})();
