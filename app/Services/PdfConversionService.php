<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PdfConversionService
{
    public const CONVERTIBLE_FORMATS = ['doc', 'docx', 'ppt', 'pptx'];
    public const CONVERSION_TIMEOUT = 120;

    private ?string $libreOfficeCommand = null;
    private bool $checkedAvailability = false;

    public function convertToPdf(string $inputPath, string $outputDir, ?string $outputFileName = null): string
    {
        if (!$this->isConvertibleFormat($inputPath)) {
            throw new \InvalidArgumentException('Format de fichier non supporté pour la conversion PDF');
        }

        if (!$this->isLibreOfficeAvailable()) {
            throw new \RuntimeException("LibreOffice n'est pas disponible sur ce système");
        }

        try {
            $realInputPath = $this->ensureAbsolutePath($inputPath);
            $realOutputDir = $this->ensureAbsolutePath($outputDir);

            $this->validateInputFile($realInputPath);
            $this->ensureDirectoryExists($realOutputDir);

            Log::info("Starting PDF conversion", [
                'input' => $realInputPath,
                'output_dir' => $realOutputDir,
                'output_filename' => $outputFileName,
            ]);

            $this->executeConversion($realInputPath, $realOutputDir);

            $convertedFile = $this->findConvertedFile($realInputPath, $realOutputDir);
            if (!$convertedFile) {
                throw new \RuntimeException("Le fichier PDF converti n'a pas été trouvé");
            }

            if ($outputFileName) {
                $finalPath = rtrim($realOutputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $outputFileName;
                if (!@rename($convertedFile, $finalPath)) {
                    throw new \RuntimeException("Impossible de renommer le fichier converti");
                }
                $convertedFile = $finalPath;
            }

            $this->validateOutputFile($convertedFile);

            Log::info("PDF conversion successful", [
                'input' => $realInputPath,
                'output' => $convertedFile,
                'size' => @filesize($convertedFile) ?: 0,
            ]);

            return $convertedFile;

        } catch (\Throwable $e) {
            Log::error('Erreur de conversion PDF', [
                'input' => $inputPath,
                'output_dir' => $outputDir,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function isConvertibleFormat(string $filePath): bool
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, self::CONVERTIBLE_FORMATS, true);
    }

    /**
     * Vérifie si LibreOffice est disponible (avec cache)
     */
    public function isLibreOfficeAvailable(): bool
    {
        if ($this->checkedAvailability && $this->libreOfficeCommand !== null) {
            return true;
        }

        if ($this->checkedAvailability && $this->libreOfficeCommand === null) {
            return false;
        }

        try {
            $command = $this->getLibreOfficeCommand();
            $this->libreOfficeCommand = $command;
            $this->checkedAvailability = true;
            return true;
        } catch (\Throwable $e) {
            $this->libreOfficeCommand = null;
            $this->checkedAvailability = true;
            
            Log::warning('LibreOffice non disponible', [
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    private function getLibreOfficeCommand(): string
    {
        // Si déjà en cache
        if ($this->libreOfficeCommand !== null) {
            return $this->libreOfficeCommand;
        }

        $commands = [
            'libreoffice',
            '/usr/bin/libreoffice',
            '/usr/local/bin/libreoffice',
            'soffice',
            '/usr/bin/soffice',
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
        ];

        foreach ($commands as $command) {
            if ($this->commandExists($command)) {
                Log::info("LibreOffice trouvé", ['command' => $command]);
                return $command;
            }
        }

        throw new \RuntimeException("LibreOffice n'est pas disponible");
    }

    private function validateInputFile(string $inputPath): void
    {
        if (!file_exists($inputPath)) {
            throw new \RuntimeException("Le fichier d'entrée n'existe pas: {$inputPath}");
        }
        if (!is_readable($inputPath)) {
            throw new \RuntimeException("Le fichier d'entrée n'est pas lisible: {$inputPath}");
        }

        $fileSize = @filesize($inputPath) ?: 0;
        if ($fileSize <= 0) {
            throw new \RuntimeException("Le fichier d'entrée est vide: {$inputPath}");
        }

        if ($fileSize > 50 * 1024 * 1024) {
            throw new \RuntimeException("Le fichier est trop volumineux pour la conversion (max 50MB)");
        }

        // MIME check
        if (class_exists(\finfo::class)) {
            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($inputPath) ?: null;

            $allowedMimes = [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            ];

            if ($mimeType && !in_array($mimeType, $allowedMimes, true)) {
                Log::warning("Suspicious MIME type for conversion: {$mimeType}", [
                    'path' => $inputPath,
                ]);
            }
        }
    }

    private function validateOutputFile(string $outputPath): void
    {
        if (!file_exists($outputPath)) {
            throw new \RuntimeException("Le fichier PDF de sortie n'existe pas: {$outputPath}");
        }
        if (!is_readable($outputPath)) {
            throw new \RuntimeException("Le fichier PDF de sortie n'est pas lisible: {$outputPath}");
        }

        $fileSize = @filesize($outputPath) ?: 0;
        if ($fileSize <= 0) {
            throw new \RuntimeException("Le fichier PDF de sortie est vide");
        }

        $handle = @fopen($outputPath, 'rb');
        if (!$handle) {
            throw new \RuntimeException("Impossible d'ouvrir le fichier PDF pour validation");
        }

        $header = fread($handle, 5);
        fclose($handle);

        if (!str_starts_with($header, '%PDF')) {
            throw new \RuntimeException("Le fichier de sortie n'est pas un PDF valide (header: " . bin2hex($header) . ")");
        }
    }

    private function executeConversion(string $inputPath, string $outputDir): void
    {
        $command = $this->libreOfficeCommand ?? $this->getLibreOfficeCommand();

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
        $process->setEnv(['HOME' => storage_path('app/temp')]);

        try {
            $process->run();

            if (!$process->isSuccessful()) {
                Log::error('Échec de la conversion LibreOffice', [
                    'command' => $process->getCommandLine(),
                    'output' => $process->getOutput(),
                    'error_output' => $process->getErrorOutput(),
                    'exit_code' => $process->getExitCode(),
                    'input_path' => $inputPath,
                    'output_dir' => $outputDir,
                ]);

                throw new \RuntimeException(
                    'La conversion LibreOffice a échoué (code ' . $process->getExitCode() . '): ' . 
                    trim($process->getErrorOutput() ?: $process->getOutput())
                );
            }
        } finally {
            // Nettoyage du profil temporaire
            if (is_dir($profileDir)) {
                $this->recursiveRemoveDirectory($profileDir);
            }
        }
    }

    private function findConvertedFile(string $inputPath, string $outputDir): ?string
    {
        $inputBasename = pathinfo($inputPath, PATHINFO_FILENAME);
        $expected = rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $inputBasename . '.pdf';

        if (file_exists($expected)) return $expected;

        $pdfFiles = glob(rtrim($outputDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.pdf') ?: [];
        if (!$pdfFiles) return null;

        $recent = array_filter($pdfFiles, fn ($f) => time() - filemtime($f) < 120);
        if ($recent) {
            usort($recent, fn ($a, $b) => filemtime($b) - filemtime($a));
            return $recent[0];
        }

        usort($pdfFiles, fn ($a, $b) => filemtime($b) - filemtime($a));
        return $pdfFiles[0] ?? null;
    }

    private function commandExists(string $command): bool
    {
        try {
            $process = PHP_OS_FAMILY === 'Windows'
                ? new Process(['where', $command])
                : new Process(['which', $command]);

            $process->setTimeout(5); // Timeout court
            $process->run();
            
            return $process->isSuccessful();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function ensureAbsolutePath(string $path): string
    {
        return $this->isAbsolutePath($path) ? $path : storage_path('app/' . ltrim($path, '/'));
    }

    private function isAbsolutePath(string $path): bool
    {
        if ($path === '') return false;
        return $path[0] === '/' || (PHP_OS_FAMILY === 'Windows' && preg_match('/^[A-Z]:\\\\/i', $path));
    }

    private function ensureDirectoryExists(string $dir): void
    {
        if (!file_exists($dir)) {
            if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
                throw new \RuntimeException("Impossible de créer le dossier: {$dir}");
            }
        }

        if (!is_writable($dir)) {
            @chmod($dir, 0755);
            if (!is_writable($dir)) {
                throw new \RuntimeException("Dossier non inscriptible: {$dir}");
            }
        }
    }

    private function recursiveRemoveDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;

        $items = scandir($dir) ?: [];
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->recursiveRemoveDirectory($path);
            } else {
                @unlink($path);
            }
        }
        
        @rmdir($dir);
    }
}