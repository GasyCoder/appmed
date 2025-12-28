<?php
// app/Services/PdfConversionService.php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PdfConversionService
{
    public const CONVERTIBLE_FORMATS = ['doc', 'docx', 'ppt', 'pptx'];
    public const CONVERSION_TIMEOUT = 180; // 3 minutes

    public function isConvertibleFormat(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, self::CONVERTIBLE_FORMATS, true);
    }

    public function isLibreOfficeAvailable(): bool
    {
        return (bool) $this->resolveLibreOfficeCommand();
    }

    /**
     * Convertir un fichier office en PDF via LibreOffice
     * @return string absolute path du PDF final
     */
    public function convertToPdf(string $inputPath, string $outputDir, ?string $outputFileName = null): string
    {
        if (!$this->isConvertibleFormat($inputPath)) {
            throw new \InvalidArgumentException("Format non supporté pour conversion PDF: {$inputPath}");
        }

        $command = $this->resolveLibreOfficeCommand();
        if (!$command) {
            throw new \RuntimeException("LibreOffice n'est pas disponible sur ce système");
        }

        $realInput = $this->ensureAbsolutePath($inputPath);
        $realOut   = $this->ensureAbsolutePath($outputDir);

        $this->validateInputFile($realInput);
        $this->ensureDirectoryExists($realOut);

        Log::info('Starting PDF conversion', [
            'input' => $realInput,
            'output_dir' => $realOut,
            'output_filename' => $outputFileName,
            'command' => $command,
        ]);

        $this->executeConversion($command, $realInput, $realOut);

        $convertedFile = $this->findConvertedFile($realInput, $realOut);
        if (!$convertedFile) {
            throw new \RuntimeException('Le fichier PDF converti n’a pas été trouvé.');
        }

        // Renommer si demandé
        if ($outputFileName) {
            $finalPath = rtrim($realOut, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $outputFileName;
            if (!@rename($convertedFile, $finalPath)) {
                throw new \RuntimeException("Impossible de renommer le fichier converti vers: {$finalPath}");
            }
            $convertedFile = $finalPath;
        }

        $this->validateOutputFile($convertedFile);

        Log::info('PDF conversion successful', [
            'input' => $realInput,
            'output' => $convertedFile,
            'size' => filesize($convertedFile) ?: 0,
        ]);

        return $convertedFile;
    }

    /**
     * Détecter une commande LO disponible
     */
    private function resolveLibreOfficeCommand(): ?string
    {
        $candidates = [
            'soffice',
            'libreoffice',
            '/usr/bin/soffice',
            '/usr/bin/libreoffice',
        ];

        foreach ($candidates as $cmd) {
            if ($this->commandExists($cmd)) {
                return $cmd;
            }
        }

        return null;
    }

    private function commandExists(string $command): bool
    {
        $process = new Process(['bash', '-lc', "command -v " . escapeshellarg($command) . " >/dev/null 2>&1"]);
        $process->run();
        return $process->isSuccessful();
    }

    private function executeConversion(string $command, string $inputPath, string $outputDir): void
    {
        $profileDir = storage_path('app/lo-profile/' . uniqid('lo_', true));
        $this->ensureDirectoryExists($profileDir);

        $process = new Process([
            $command,
            '--headless',
            '--invisible',
            '--nodefault',
            '--nolockcheck',
            '--nologo',
            '--norestore',
            '-env:UserInstallation=file://' . $profileDir,
            '--convert-to', 'pdf',
            '--outdir', $outputDir,
            $inputPath,
        ]);

        $process->setTimeout(self::CONVERSION_TIMEOUT);

        // HOME writable
        $process->setEnv([
            'HOME' => storage_path('app/temp'),
        ]);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            Log::error('LibreOffice conversion failed', [
                'command' => $process->getCommandLine(),
                'stdout' => $process->getOutput(),
                'stderr' => $process->getErrorOutput(),
                'exit_code' => $process->getExitCode(),
            ]);
            throw new \RuntimeException('La conversion LibreOffice a échoué : ' . trim($process->getErrorOutput()));
        } finally {
            // Nettoyage profile (best effort)
            $this->rrmdir($profileDir);
        }
    }

    private function findConvertedFile(string $inputPath, string $outputDir): ?string
    {
        $base = pathinfo($inputPath, PATHINFO_FILENAME);
        $expected = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $base . '.pdf';

        if (file_exists($expected)) return $expected;

        $pdfs = glob(rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.pdf') ?: [];
        if (!$pdfs) return null;

        // plus récent <= 3min
        $recent = array_filter($pdfs, fn($f) => (time() - filemtime($f)) < 180);
        if (!$recent) return null;

        usort($recent, fn($a, $b) => filemtime($b) <=> filemtime($a));
        return $recent[0] ?? null;
    }

    private function validateInputFile(string $path): void
    {
        if (!file_exists($path)) throw new \RuntimeException("Fichier d'entrée introuvable: {$path}");
        if (!is_readable($path)) throw new \RuntimeException("Fichier d'entrée non lisible: {$path}");
        $size = filesize($path) ?: 0;
        if ($size <= 0) throw new \RuntimeException("Fichier d'entrée vide: {$path}");

        // (Optionnel) limite conversion 50MB
        if ($size > 50 * 1024 * 1024) {
            throw new \RuntimeException("Fichier trop volumineux pour conversion (max 50MB).");
        }
    }

    private function validateOutputFile(string $path): void
    {
        if (!file_exists($path)) throw new \RuntimeException("PDF de sortie introuvable: {$path}");
        if (!is_readable($path)) throw new \RuntimeException("PDF de sortie non lisible: {$path}");
        $size = filesize($path) ?: 0;
        if ($size <= 0) throw new \RuntimeException("PDF de sortie vide.");

        $h = @fopen($path, 'rb');
        if (!$h) throw new \RuntimeException("Impossible d'ouvrir le PDF de sortie.");
        $header = fread($h, 4);
        fclose($h);

        if ($header !== '%PDF') {
            throw new \RuntimeException("Le fichier de sortie n'est pas un PDF valide.");
        }
    }

    private function ensureDirectoryExists(string $dir): void
    {
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new \RuntimeException("Impossible de créer le dossier: {$dir}");
            }
        }
        if (!is_writable($dir)) {
            @chmod($dir, 0755);
            if (!is_writable($dir)) {
                throw new \RuntimeException("Dossier non writable: {$dir}");
            }
        }
    }

    private function ensureAbsolutePath(string $path): string
    {
        if ($this->isAbsolutePath($path)) return $path;
        return storage_path('app/' . ltrim($path, '/'));
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') return false;
        return $path[0] === '/' || (PHP_OS_FAMILY === 'Windows' && preg_match('/^[A-Z]:\\\\/i', $path));
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) return;
        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $p = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($p)) $this->rrmdir($p);
            else @unlink($p);
        }
        @rmdir($dir);
    }
}
