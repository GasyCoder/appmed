<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class PdfConversionService
{
    const CONVERTIBLE_FORMATS = ['doc', 'docx', 'ppt', 'pptx'];
    const CONVERSION_TIMEOUT = 120; // 2 minutes

    /**
     * Convertir un fichier en PDF
     */
    public function convertToPdf($inputPath, $outputDir, $outputFileName = null)
    {
        if (!$this->isConvertibleFormat($inputPath)) {
            throw new \InvalidArgumentException('Format de fichier non supporté pour la conversion PDF');
        }

        if (!$this->isLibreOfficeAvailable()) {
            throw new \Exception('LibreOffice n\'est pas disponible sur ce système');
        }

        try {
            // Préparer les chemins
            $realInputPath = $this->ensureAbsolutePath($inputPath);
            $realOutputDir = $this->ensureAbsolutePath($outputDir);
            
            // Vérifications de sécurité
            $this->validateInputFile($realInputPath);
            
            // Créer le dossier de sortie si nécessaire
            $this->ensureDirectoryExists($realOutputDir);

            Log::info("Starting PDF conversion", [
                'input' => $realInputPath,
                'output_dir' => $realOutputDir,
                'output_filename' => $outputFileName
            ]);

            // Exécuter la conversion
            $this->executeConversion($realInputPath, $realOutputDir);

            // Déterminer le fichier converti
            $convertedFile = $this->findConvertedFile($realInputPath, $realOutputDir);
            
            if (!$convertedFile) {
                throw new \Exception('Le fichier PDF converti n\'a pas été trouvé');
            }

            // Renommer si nécessaire
            if ($outputFileName) {
                $finalPath = $realOutputDir . DIRECTORY_SEPARATOR . $outputFileName;
                if (!rename($convertedFile, $finalPath)) {
                    throw new \Exception('Impossible de renommer le fichier converti');
                }
                $convertedFile = $finalPath;
            }

            // Vérifier le fichier final
            $this->validateOutputFile($convertedFile);

            Log::info("PDF conversion successful", [
                'input' => $realInputPath,
                'output' => $convertedFile,
                'size' => filesize($convertedFile)
            ]);

            return $convertedFile;

        } catch (\Exception $e) {
            Log::error('Erreur de conversion PDF', [
                'input' => $inputPath,
                'output_dir' => $outputDir,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Vérifier si le format est convertible
     */
    public function isConvertibleFormat($filePath)
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        return in_array($extension, self::CONVERTIBLE_FORMATS);
    }

    /**
     * Vérifier si LibreOffice est disponible
     */
    public function isLibreOfficeAvailable()
    {
        try {
            $command = $this->getLibreOfficeCommand();
            return !empty($command);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Obtenir la commande LibreOffice disponible
     */
    private function getLibreOfficeCommand()
    {
        $commands = [
            'libreoffice',
            '/usr/bin/libreoffice', 
            '/Applications/LibreOffice.app/Contents/MacOS/soffice',
            'soffice'
        ];

        foreach ($commands as $command) {
            if ($this->commandExists($command)) {
                return $command;
            }
        }

        throw new \Exception('LibreOffice n\'est pas disponible');
    }

    /**
     * Valider le fichier d'entrée
     */
    private function validateInputFile($inputPath)
    {
        if (!file_exists($inputPath)) {
            throw new \Exception("Le fichier d'entrée n'existe pas: {$inputPath}");
        }

        if (!is_readable($inputPath)) {
            throw new \Exception("Le fichier d'entrée n'est pas lisible: {$inputPath}");
        }

        $fileSize = filesize($inputPath);
        if ($fileSize === 0) {
            throw new \Exception("Le fichier d'entrée est vide: {$inputPath}");
        }

        // Vérifier la taille maximale (50MB)
        if ($fileSize > 50 * 1024 * 1024) {
            throw new \Exception("Le fichier est trop volumineux pour la conversion (max 50MB)");
        }

        // Vérifier le type MIME si possible
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $inputPath);
            finfo_close($finfo);

            $allowedMimes = [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-powerpoint',
                'application/vnd.openxmlformats-officedocument.presentationml.presentation'
            ];

            if (!in_array($mimeType, $allowedMimes)) {
                Log::warning("Suspicious MIME type for conversion: {$mimeType}");
            }
        }

        Log::info("Input file validated", [
            'path' => $inputPath,
            'size' => $fileSize,
            'readable' => true
        ]);
    }

    /**
     * Valider le fichier de sortie
     */
    private function validateOutputFile($outputPath)
    {
        if (!file_exists($outputPath)) {
            throw new \Exception("Le fichier PDF de sortie n'existe pas: {$outputPath}");
        }

        if (!is_readable($outputPath)) {
            throw new \Exception("Le fichier PDF de sortie n'est pas lisible: {$outputPath}");
        }

        $fileSize = filesize($outputPath);
        if ($fileSize === 0) {
            throw new \Exception("Le fichier PDF de sortie est vide");
        }

        // Vérifier l'en-tête PDF
        $handle = fopen($outputPath, 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        if ($header !== '%PDF') {
            throw new \Exception("Le fichier de sortie n'est pas un PDF valide");
        }

        Log::info("Output file validated", [
            'path' => $outputPath,
            'size' => $fileSize,
            'is_pdf' => true
        ]);
    }

    /**
     * Exécuter la conversion avec LibreOffice
     */
    private function executeConversion($inputPath, $outputDir)
    {
        $command = $this->getLibreOfficeCommand();

        $process = new Process([
            $command,
            '--headless',
            '--invisible',
            '--nodefault',
            '--nolockcheck',
            '--nologo',
            '--norestore',
            '--convert-to', 'pdf',
            '--outdir', $outputDir,
            $inputPath
        ]);

        $process->setTimeout(self::CONVERSION_TIMEOUT);

        // Définir les variables d'environnement
        $process->setEnv([
            'DISPLAY' => ':99',
            'HOME' => storage_path('app/temp')
        ]);

        Log::info("Executing LibreOffice conversion", [
            'command' => $process->getCommandLine(),
            'timeout' => self::CONVERSION_TIMEOUT,
            'input_exists' => file_exists($inputPath),
            'input_readable' => is_readable($inputPath),
            'input_size' => file_exists($inputPath) ? filesize($inputPath) : 0
        ]);

        try {
            $process->mustRun();

            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }

            Log::info("LibreOffice conversion successful", [
                'output' => trim($process->getOutput()),
                'error_output' => trim($process->getErrorOutput())
            ]);

        } catch (ProcessFailedException $e) {
            Log::error('Échec de la conversion LibreOffice', [
                'command' => $process->getCommandLine(),
                'output' => $process->getOutput(),
                'error_output' => $process->getErrorOutput(),
                'exit_code' => $process->getExitCode(),
                'input_path' => $inputPath,
                'output_dir' => $outputDir,
                'input_exists' => file_exists($inputPath),
                'input_readable' => is_readable($inputPath)
            ]);

            throw new \Exception('La conversion LibreOffice a échoué : ' . $process->getErrorOutput());
        }
    }

    /**
     * Trouver le fichier PDF converti
     */
    private function findConvertedFile($inputPath, $outputDir)
    {
        $inputBasename = pathinfo($inputPath, PATHINFO_FILENAME);
        $expectedPdfPath = $outputDir . DIRECTORY_SEPARATOR . $inputBasename . '.pdf';
        
        Log::info("Looking for converted file", [
            'expected_path' => $expectedPdfPath,
            'input_basename' => $inputBasename
        ]);

        if (file_exists($expectedPdfPath)) {
            return $expectedPdfPath;
        }

        // Chercher tous les PDF dans le dossier de sortie
        $pdfFiles = glob($outputDir . DIRECTORY_SEPARATOR . '*.pdf');
        
        if (empty($pdfFiles)) {
            Log::error("No PDF files found in output directory", [
                'output_dir' => $outputDir,
                'directory_contents' => scandir($outputDir)
            ]);
            return null;
        }

        // Retourner le plus récent créé dans les 2 dernières minutes
        $recentPdfs = array_filter($pdfFiles, function($file) {
            return time() - filemtime($file) < 120;
        });

        if (!empty($recentPdfs)) {
            usort($recentPdfs, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            
            Log::info("Found recent PDF file", [
                'file' => $recentPdfs[0],
                'created' => date('Y-m-d H:i:s', filemtime($recentPdfs[0]))
            ]);
            
            return $recentPdfs[0];
        }

        Log::warning("No recent PDF files found", [
            'pdf_files_count' => count($pdfFiles),
            'pdf_files' => $pdfFiles
        ]);

        return null;
    }

    /**
     * Vérifier si une commande existe
     */
    private function commandExists($command)
    {
        // Pour Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $process = new Process(['where', $command]);
        } else {
            $process = new Process(['which', $command]);
        }
        
        $process->run();
        return $process->isSuccessful();
    }

    /**
     * S'assurer que le chemin est absolu
     */
    private function ensureAbsolutePath($path)
    {
        if (!$this->isAbsolutePath($path)) {
            return storage_path('app/' . $path);
        }
        return $path;
    }

    /**
     * Vérifier si le chemin est absolu
     */
    private function isAbsolutePath($path)
    {
        return $path[0] === '/' || (PHP_OS_FAMILY === 'Windows' && preg_match('/^[A-Z]:\\\\/i', $path));
    }

    /**
     * S'assurer que le dossier existe avec les bonnes permissions
     */
    private function ensureDirectoryExists($dir)
    {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new \Exception("Impossible de créer le dossier: {$dir}");
            }
            Log::info("Created directory: {$dir}");
        }

        // Vérifier les permissions
        if (!is_writable($dir)) {
            if (!chmod($dir, 0755)) {
                throw new \Exception("Impossible de définir les permissions du dossier: {$dir}");
            }
            Log::info("Fixed permissions for directory: {$dir}");
        }
    }

    /**
     * Nettoyer les fichiers temporaires
     */
    public function cleanupTempFiles($pattern = null)
    {
        $tempDir = storage_path('app/temp');
        
        if (!file_exists($tempDir)) {
            return;
        }

        $pattern = $pattern ?: $tempDir . '/*';
        $files = glob($pattern);
        $cleanedCount = 0;
        
        foreach ($files as $file) {
            if (is_file($file) && time() - filemtime($file) > 3600) { // Plus vieux qu'1 heure
                if (@unlink($file)) {
                    $cleanedCount++;
                    Log::info("Cleaned up temp file: {$file}");
                }
            }
        }

        Log::info("Temp files cleanup completed", [
            'files_cleaned' => $cleanedCount,
            'temp_dir' => $tempDir
        ]);
    }

    /**
     * Obtenir des informations sur la conversion
     */
    public function getConversionInfo($inputPath)
    {
        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));
        
        return [
            'can_convert' => $this->isConvertibleFormat($inputPath),
            'original_extension' => $extension,
            'target_extension' => 'pdf',
            'libreoffice_available' => $this->isLibreOfficeAvailable(),
            'estimated_time' => $this->estimateConversionTime($inputPath),
            'file_size' => file_exists($inputPath) ? filesize($inputPath) : 0,
            'file_exists' => file_exists($inputPath),
            'file_readable' => file_exists($inputPath) && is_readable($inputPath)
        ];
    }

    /**
     * Estimer le temps de conversion
     */
    private function estimateConversionTime($inputPath)
    {
        if (!file_exists($inputPath)) {
            return 0;
        }

        $fileSize = filesize($inputPath);
        $extension = strtolower(pathinfo($inputPath, PATHINFO_EXTENSION));
        
        // Estimation basique en secondes
        $baseTime = 3; // Temps de base
        $sizeMultiplier = $fileSize / (1024 * 1024); // Taille en MB
        
        $extensionMultipliers = [
            'doc' => 1.0,
            'docx' => 1.2,
            'ppt' => 1.5,
            'pptx' => 2.0,
        ];
        
        $multiplier = $extensionMultipliers[$extension] ?? 1.0;
        
        return max($baseTime, round($baseTime + ($sizeMultiplier * $multiplier)));
    }

    /**
     * Tester la conversion (pour diagnostic)
     */
    public function testConversion()
    {
        $results = [
            'libreoffice_available' => false,
            'temp_directory_writable' => false,
            'output_directory_writable' => false,
            'test_conversion' => false,
            'errors' => []
        ];

        try {
            // Test LibreOffice
            $results['libreoffice_available'] = $this->isLibreOfficeAvailable();
            if (!$results['libreoffice_available']) {
                $results['errors'][] = 'LibreOffice n\'est pas disponible';
            }

            // Test dossier temp
            $tempDir = storage_path('app/temp');
            $this->ensureDirectoryExists($tempDir);
            $results['temp_directory_writable'] = is_writable($tempDir);
            if (!$results['temp_directory_writable']) {
                $results['errors'][] = 'Dossier temporaire non accessible en écriture: ' . $tempDir;
            }

            // Test dossier output
            $outputDir = storage_path('app/public/documents');
            $this->ensureDirectoryExists($outputDir);
            $results['output_directory_writable'] = is_writable($outputDir);
            if (!$results['output_directory_writable']) {
                $results['errors'][] = 'Dossier de sortie non accessible en écriture: ' . $outputDir;
            }

        } catch (\Exception $e) {
            $results['errors'][] = 'Erreur lors du test: ' . $e->getMessage();
        }

        Log::info("PDF conversion test completed", $results);

        return $results;
    }
}