<?php

namespace App\Livewire\Teacher;

use App\Models\Niveau;
use App\Models\Parcour;
use App\Services\PdfConversionService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Document;
use App\Models\Semestre;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentUpload extends Component
{
    use WithFileUploads;
    use WithPagination;
    use LivewireAlert;

    #[Rule(['array'])]
    public $file = [];
    public $file_status = [];
    public $titles = [];
    public $niveau_id = '';
    public $parcour_id = '';
    public $is_actif = true;
    
    const MAX_FILES = 6;

    public $isUploading = false;
    public $uploadProgress = 0;
    public $conversionProgress = 0;
    public $currentConvertingFile = '';
    public $conversionStatus = [];
    public $successMessage = '';
    public $errorMessage = '';

    const SUPPORTED_FORMATS = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'jpeg', 'jpg', 'png'];

    private function getConversionService(): PdfConversionService
    {
        return app(PdfConversionService::class);
    }

    public function mount()
    {
        $this->file = [];
        $this->titles = [];
        $this->file_status = [];
        $this->conversionStatus = [];

        $firstNiveau = $this->getTeacherNiveauxProperty()->first();
        if ($firstNiveau) {
            $this->niveau_id = $firstNiveau->id;
            $this->updatedNiveauId();
        }
    }

    private function sanitizeFilename($filename)
    {
        $pathInfo = pathinfo($filename);
        $basename = $pathInfo['filename'] ?? $filename;
        $extension = $pathInfo['extension'] ?? '';

        $forbiddenChars = ['<', '>', ':', '"', '/', '\\', '|', '?', '*', '[', ']', '{', '}'];
        $cleanBasename = str_replace($forbiddenChars, '', $basename);
        $cleanBasename = str_replace(' ', '_', $cleanBasename);
        $cleanBasename = preg_replace('/[^\w\-\.]/', '_', $cleanBasename);
        $cleanBasename = preg_replace('/_+/', '_', $cleanBasename);
        $cleanBasename = trim($cleanBasename, '_');

        if (strlen($cleanBasename) > 100) {
            $cleanBasename = substr($cleanBasename, 0, 100);
        }

        if (empty($cleanBasename)) {
            $cleanBasename = 'document_' . time();
        }

        $result = $extension ? $cleanBasename . '.' . $extension : $cleanBasename;
        Log::info("Filename sanitized: '{$filename}' -> '{$result}'");
        return $result;
    }

    private function createSafeTemporaryFilename($originalFile, $title = null)
    {
        $extension = strtolower($originalFile->getClientOriginalExtension());
        $baseName = $title ? Str::slug($title) : 'temp_file';
        $timestamp = now()->format('Y_m_d_H_i_s');
        $random = Str::random(8);
        return "{$baseName}_{$timestamp}_{$random}.{$extension}";
    }

    public function updatedFile()
    {
        if (!empty($this->file)) {
            $this->validate([
                'file' => 'array|max:' . self::MAX_FILES,
                'file.*' => 'file|max:10240|mimes:' . implode(',', self::SUPPORTED_FORMATS)
            ]);

            // Détecter et supprimer les doublons
            $uniqueFiles = [];
            $seenHashes = [];
            $duplicateCount = 0;

            foreach ($this->file as $index => $file) {
                $fileHash = md5($file->getClientOriginalName() . $file->getSize() . $file->getMimeType());
                
                if (!in_array($fileHash, $seenHashes)) {
                    $seenHashes[] = $fileHash;
                    $uniqueFiles[] = $file;
                    
                    $newIndex = count($uniqueFiles) - 1;
                    
                    if (!isset($this->titles[$newIndex])) {
                        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                        $this->titles[$newIndex] = $this->sanitizeFilename($originalName);
                    }
                    if (!isset($this->file_status[$newIndex])) {
                        $this->file_status[$newIndex] = true;
                    }

                    $extension = strtolower($file->getClientOriginalExtension());
                    $needsConversion = $this->getConversionService()->isConvertibleFormat($file->getClientOriginalName());

                    $this->conversionStatus[$newIndex] = [
                        'needs_conversion' => $needsConversion,
                        'status' => 'pending',
                        'original_extension' => $extension,
                        'final_extension' => $needsConversion ? 'pdf' : $extension,
                        'original_filename' => $file->getClientOriginalName(),
                        'sanitized_title' => $this->sanitizeFilename($this->titles[$newIndex])
                    ];
                } else {
                    $duplicateCount++;
                    Log::warning("Duplicate file detected and removed: {$file->getClientOriginalName()}");
                }
            }

            $this->file = $uniqueFiles;
            $this->titles = array_slice($this->titles, 0, count($uniqueFiles));
            $this->file_status = array_slice($this->file_status, 0, count($uniqueFiles));
            $this->conversionStatus = array_slice($this->conversionStatus, 0, count($uniqueFiles));

            if ($duplicateCount > 0) {
                $this->alert('info', "{$duplicateCount} fichier(s) dupliqué(s) supprimé(s) automatiquement", [
                    'position' => 'top-end',
                    'timer' => 3000,
                    'toast' => true
                ]);
            }

            $this->dispatch('files-updated', [
                'total' => count($this->file),
                'conversions_needed' => $this->getConversionCount(),
                'duplicates_removed' => $duplicateCount
            ]);

            Log::info("Files updated", [
                'total_files' => count($this->file),
                'duplicates_removed' => $duplicateCount,
                'conversions_needed' => $this->getConversionCount()
            ]);
        }
    }

    public function removeFile($index)
    {
        if (isset($this->file[$index])) {
            unset($this->file[$index]);
            $this->file = array_values($this->file);

            if (isset($this->titles[$index])) {
                unset($this->titles[$index]);
                $this->titles = array_values($this->titles);
            }

            if (isset($this->file_status[$index])) {
                unset($this->file_status[$index]);
                $this->file_status = array_values($this->file_status);
            }

            if (isset($this->conversionStatus[$index])) {
                unset($this->conversionStatus[$index]);
                $this->conversionStatus = array_values($this->conversionStatus);
            }
        }
    }

    public function getConversionCount()
    {
        return collect($this->conversionStatus)->where('needs_conversion', true)->count();
    }

    public function getSemestresActifsProperty()
    {
        if (!$this->niveau_id) {
            return collect();
        }

        return Semestre::where('niveau_id', $this->niveau_id)
            ->where('is_active', true)
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    public function getTeacherNiveauxProperty()
    {
        return Auth::user()->niveaux()
            ->where('niveaux.status', true)
            ->orderBy('niveaux.name')
            ->get();
    }

    public function getTeacherParcoursProperty()
    {
        if (!$this->niveau_id) {
            return collect();
        }

        return Auth::user()->parcours()
            ->where('parcours.status', true)
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('niveau_user')
                      ->where('niveau_user.user_id', Auth::id())
                      ->where('niveau_user.niveau_id', $this->niveau_id);
            })
            ->orderBy('parcours.name')
            ->get();
    }

    public function updatedNiveauId()
    {
        $this->parcour_id = '';

        if (!$this->niveau_id) {
            return;
        }

        $availableParcours = $this->getTeacherParcoursProperty();
        if ($availableParcours->isNotEmpty()) {
            $this->parcour_id = $availableParcours->first()->id;
        }

        $this->dispatch('niveau-changed', [
            'niveau_id' => $this->niveau_id,
            'parcours_count' => $availableParcours->count(),
            'semestres_count' => $this->getSemestresActifsProperty()->count()
        ]);

        Log::info("Niveau changed", [
            'niveau_id' => $this->niveau_id,
            'parcour_id' => $this->parcour_id,
            'available_semestres' => $this->getSemestresActifsProperty()->count()
        ]);
    }

    public function updatedParcourId()
    {
        $this->dispatch('parcour-changed', [
            'parcour_id' => $this->parcour_id
        ]);
    }

    public function uploadDocument()
    {
        if (count($this->file) > self::MAX_FILES) {
            $this->alert('error', 'Vous ne pouvez pas téléverser plus de ' . self::MAX_FILES . ' fichiers à la fois.');
            return;
        }

        $this->validate([
            'file' => 'required|array|max:' . self::MAX_FILES,
            'file.*' => [
                'required',
                'file',
                'max:10240',
                'mimes:' . implode(',', self::SUPPORTED_FORMATS)
            ],
            'titles.*' => 'required|string|min:3|max:255',
            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'required|exists:parcours,id',
        ]);

        $this->isUploading = true;
        $this->uploadProgress = 0;
        $this->conversionProgress = 0;
        $this->successMessage = '';
        $this->errorMessage = '';

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $hasNiveauAccess = $user->hasAccessToNiveau($this->niveau_id);
            $hasParcoursAccess = $user->hasAccessToParcours($this->parcour_id);

            if (!$hasNiveauAccess || !$hasParcoursAccess) {
                throw new \Exception('Vous n\'avez pas accès à ce niveau ou parcours.');
            }

            Log::info("Starting document upload with NIVEAU logic", [
                'user_id' => Auth::id(),
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
                'files_count' => count($this->file),
                'logic' => 'ONE_FILE_ONE_NIVEAU_ENTRY'
            ]);

            $totalFiles = count($this->file);
            $filesProcessed = 0;
            $conversionCount = 0;
            $totalEntriesCreated = 0;
            $totalEntriesSkipped = 0;
            $processedFiles = [];

            foreach ($this->file as $index => $uploadedFile) {
                if (empty($this->titles[$index])) {
                    throw new \Exception('Titre requis pour tous les fichiers');
                }

                $cleanTitle = $this->sanitizeFilename($this->titles[$index]);
                $fileHash = md5($uploadedFile->getClientOriginalName() . $cleanTitle . $this->niveau_id . $this->parcour_id);

                if (in_array($fileHash, $processedFiles)) {
                    Log::warning("Duplicate file detected in upload process: {$cleanTitle}");
                    continue;
                }

                $processedFiles[] = $fileHash;
                $this->currentConvertingFile = $cleanTitle;

                Log::info("Processing file {$index}: '{$this->titles[$index]}' -> '{$cleanTitle}'");

                $result = $this->processAndStoreDocument($uploadedFile, $cleanTitle, $index);

                if ($result['converted']) {
                    $conversionCount++;
                }

                // LOGIQUE CORRIGÉE : Créer UNE SEULE entrée au niveau du NIVEAU
                $documentResult = $this->createSingleDocumentForNiveau($cleanTitle, $uploadedFile, $result['file_path'], $index, $result);
                
                if ($documentResult['created']) {
                    $totalEntriesCreated++;
                } else {
                    $totalEntriesSkipped++;
                }

                $filesProcessed++;
                $this->uploadProgress = intval(($filesProcessed / $totalFiles) * 100);

                $this->dispatch('upload-progress-updated', [
                    'progress' => $this->uploadProgress,
                    'current_file' => $cleanTitle,
                    'files_processed' => $filesProcessed,
                    'total_files' => $totalFiles
                ]);

                usleep(300000);
            }

            DB::commit();

            // Message de succès corrigé
            $message = "{$filesProcessed} fichier(s) uploadé(s) avec succès";
            $message .= " → {$totalEntriesCreated} entrée(s) créée(s) au niveau du NIVEAU";
            
            if ($totalEntriesSkipped > 0) {
                $message .= " • {$totalEntriesSkipped} doublon(s) évité(s)";
            }
            if ($conversionCount > 0) {
                $message .= " • {$conversionCount} fichier(s) converti(s) en PDF";
            }

            $this->alert('success', $message, [
                'position' => 'top-end',
                'timer' => 6000,
                'toast' => true,
                'timerProgressBar' => true,
                'showConfirmButton' => false,
                'width' => '500px',
            ]);

            Log::info("Upload completed successfully with NIVEAU logic", [
                'files_processed' => $filesProcessed,
                'entries_created' => $totalEntriesCreated,
                'entries_skipped' => $totalEntriesSkipped,
                'conversions' => $conversionCount,
                'logic' => 'ONE_ENTRY_PER_FILE_AT_NIVEAU_LEVEL'
            ]);

            $this->resetForm();
            return redirect()->route('document.teacher');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Upload error:', [
                'error' => $e->getMessage(),
                'user' => Auth::id(),
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
                'trace' => $e->getTraceAsString()
            ]);

            $this->alert('error', 'Erreur: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 7000,
                'toast' => false,
            ]);
        } finally {
            $this->isUploading = false;
            $this->currentConvertingFile = '';
            $this->getConversionService()->cleanupTempFiles();
        }
    }

    // NOUVELLE MÉTHODE : Créer UNE SEULE entrée au niveau du NIVEAU
    private function createSingleDocumentForNiveau($title, $originalFile, $finalPath, $index, $conversionResult)
    {
        $originalExtension = strtolower($originalFile->getClientOriginalExtension());
        $wasConverted = $conversionResult['converted'] ?? false;
        $finalMimeType = $wasConverted ? 'application/pdf' : $originalFile->getMimeType();

        $absolutePath = storage_path('app/public/' . $finalPath);
        if (!file_exists($absolutePath)) {
            Log::error("File does not exist at path: {$absolutePath}", [
                'final_path' => $finalPath,
                'title' => $title,
                'index' => $index
            ]);
            throw new \Exception("Le fichier n'existe pas à l'emplacement: {$finalPath}");
        }

        $fileSize = filesize($absolutePath);
        if ($fileSize === false || $fileSize === 0) {
            Log::error("Failed to retrieve file size or file is empty: {$absolutePath}");
            throw new \Exception("Impossible de récupérer la taille du fichier: {$finalPath}");
        }

        // LOGIQUE CORRIGÉE : Vérifier si le document existe déjà pour ce NIVEAU et PARCOURS
        $existingDocument = Document::where('file_path', $finalPath)
            ->where('uploaded_by', Auth::id())
            ->where('niveau_id', $this->niveau_id)
            ->where('parcour_id', $this->parcour_id)
            ->first();

        if ($existingDocument) {
            Log::info("Document already exists for this niveau/parcours, skipping", [
                'file_path' => $finalPath,
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
                'title' => $title,
                'existing_id' => $existingDocument->id
            ]);
            return ['created' => false, 'reason' => 'already_exists'];
        }

        // CORRECTION : Ne PAS spécifier de semestre_id spécifique
        // Option 1: semestre_id = null (document au niveau du niveau)
        // Option 2: semestre_id = premier semestre du niveau (si votre DB l'exige)
        
        $firstSemestre = $this->getSemestresActifsProperty()->first();
        $semestre_id = $firstSemestre ? $firstSemestre->id : null;

        // Créer UNE SEULE entrée document au niveau du NIVEAU
        $document = Document::create([
            'title' => $title,
            'file_path' => $finalPath,
            'protected_path' => $finalPath,
            'file_type' => $finalMimeType,
            'file_size' => $fileSize,
            'original_filename' => $originalFile->getClientOriginalName(),
            'original_extension' => $originalExtension,
            'converted_from' => $wasConverted ? $originalExtension : null,
            'converted_at' => $wasConverted ? now() : null,
            'niveau_id' => $this->niveau_id,
            'parcour_id' => $this->parcour_id,
            'semestre_id' => $semestre_id, // Peut être null ou premier semestre selon votre logique
            'uploaded_by' => Auth::id(),
            'is_actif' => $this->file_status[$index] ?? false,
            'download_count' => 0,
            'view_count' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Log::info("CORRECTED: Single document entry created for NIVEAU", [
            'document_id' => $document->id,
            'title' => $title,
            'file_path' => $finalPath,
            'niveau_id' => $this->niveau_id,
            'parcour_id' => $this->parcour_id,
            'semestre_id' => $semestre_id,
            'user_id' => Auth::id(),
            'logic' => 'ONE_ENTRY_PER_FILE_AT_NIVEAU_LEVEL'
        ]);

        return ['created' => true, 'document_id' => $document->id];
    }

    private function processAndStoreDocument($file, $title, $index)
    {
        $originalExtension = strtolower($file->getClientOriginalExtension());
        $conversionService = $this->getConversionService();

        $needsConversion = $conversionService->isConvertibleFormat($file->getClientOriginalName());

        if ($needsConversion) {
            return $this->convertToPdfUsingService($file, $title, $index);
        } else {
            return [
                'file_path' => $this->storeDocument($file, $title),
                'converted' => false,
                'original_extension' => $originalExtension,
                'final_extension' => $originalExtension
            ];
        }
    }

    private function convertToPdfUsingService($file, $title, $index)
    {
        $tempPath = null;
        $absoluteTempPath = null;

        try {
            if (!$file->isValid()) {
                Log::error("Invalid uploaded file", [
                    'original_name' => $file->getClientOriginalName(),
                    'error' => $file->getErrorMessage(),
                    'temp_path' => $file->getPathname(),
                    'temp_exists' => file_exists($file->getPathname()),
                    'temp_readable' => is_readable($file->getPathname())
                ]);
                throw new \Exception("Le fichier uploadé est invalide: " . $file->getClientOriginalName());
            }

            $fileSize = $file->getSize();
            if ($fileSize === 0) {
                Log::error("Uploaded file is empty", [
                    'original_name' => $file->getClientOriginalName(),
                    'temp_path' => $file->getPathname()
                ]);
                throw new \Exception("Le fichier uploadé est vide: " . $file->getClientOriginalName());
            }

            Log::info("Starting PDF conversion", [
                'original_name' => $file->getClientOriginalName(),
                'temp_path' => $file->getPathname(),
                'temp_exists' => file_exists($file->getPathname()),
                'temp_readable' => is_readable($file->getPathname()),
                'temp_size' => $fileSize,
                'title' => $title
            ]);

            $this->dispatch('conversion-status-updated', [
                'file' => $title,
                'status' => 'converting',
                'index' => $index
            ]);

            $safeFileName = $this->createSafeTemporaryFilename($file, $title);
            $tempDir = storage_path('app/temp');
            $absoluteTempPath = $tempDir . '/' . $safeFileName;

            if (!file_exists($tempDir)) {
                if (!mkdir($tempDir, 0755, true)) {
                    Log::error("Failed to create temp directory: {$tempDir}");
                    throw new \Exception("Impossible de créer le dossier temporaire: {$tempDir}");
                }
                Log::info("Created temp directory: {$tempDir}");
            }

            if (!is_writable($tempDir)) {
                if (!chmod($tempDir, 0755)) {
                    Log::error("Temp directory not writable: {$tempDir}");
                    throw new \Exception("Dossier temporaire non accessible en écriture: {$tempDir}");
                }
                Log::info("Fixed permissions for temp directory: {$tempDir}");
            }

            $sourcePath = $file->getPathname();
            if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
                Log::error("Source file is not accessible", [
                    'source_path' => $sourcePath,
                    'exists' => file_exists($sourcePath),
                    'readable' => is_readable($sourcePath)
                ]);
                throw new \Exception("Le fichier source n'est pas accessible: {$sourcePath}");
            }

            if (!copy($sourcePath, $absoluteTempPath)) {
                Log::error("Failed to copy file to temporary location", [
                    'source_path' => $sourcePath,
                    'destination_path' => $absoluteTempPath
                ]);
                throw new \Exception("Échec du déplacement du fichier temporaire: {$safeFileName}");
            }

            Log::info("File copied to temporary location", [
                'original_name' => $file->getClientOriginalName(),
                'source_path' => $sourcePath,
                'temp_path' => $absoluteTempPath
            ]);

            if (!file_exists($absoluteTempPath) || !is_readable($absoluteTempPath) || filesize($absoluteTempPath) === 0) {
                Log::error("Temporary file validation failed", [
                    'path' => $absoluteTempPath,
                    'exists' => file_exists($absoluteTempPath),
                    'readable' => is_readable($absoluteTempPath),
                    'size' => filesize($absoluteTempPath)
                ]);
                throw new \Exception("Validation du fichier temporaire échouée: {$absoluteTempPath}");
            }

            $outputDir = storage_path('app/public/documents');
            $pdfFileName = time() . '_' . Str::slug($title) . '_' . Str::random(8) . '.pdf';

            if (!file_exists($outputDir)) {
                if (!mkdir($outputDir, 0755, true)) {
                    Log::error("Failed to create output directory: {$outputDir}");
                    throw new \Exception("Impossible de créer le dossier de sortie: {$outputDir}");
                }
                Log::info("Created output directory: {$outputDir}");
            }

            if (!is_writable($outputDir)) {
                if (!chmod($outputDir, 0755)) {
                    Log::error("Output directory not writable: {$outputDir}");
                    throw new \Exception("Dossier de sortie non accessible en écriture: {$outputDir}");
                }
                Log::info("Fixed permissions for output directory: {$outputDir}");
            }

            if (file_exists($outputDir . '/' . $pdfFileName)) {
                $pdfFileName = time() . '_' . Str::slug($title) . '_' . Str::random(8) . '.pdf';
            }

            Log::info("Starting PDF conversion with service", [
                'input' => $absoluteTempPath,
                'output_dir' => $outputDir,
                'pdf_filename' => $pdfFileName
            ]);

            $convertedFilePath = $this->getConversionService()->convertToPdf(
                $absoluteTempPath,
                $outputDir,
                $pdfFileName
            );

            if (!file_exists($convertedFilePath)) {
                Log::error("PDF conversion failed - file not found: {$convertedFilePath}");
                throw new \Exception("La conversion a échoué - fichier PDF non trouvé: {$convertedFilePath}");
            }

            $this->dispatch('conversion-status-updated', [
                'file' => $title,
                'status' => 'completed',
                'index' => $index
            ]);

            $this->conversionStatus[$index]['status'] = 'completed';

            Log::info("Conversion successful", [
                'title' => $title,
                'pdf_filename' => $pdfFileName,
                'output_size' => filesize($convertedFilePath)
            ]);

            return [
                'file_path' => 'documents/' . $pdfFileName,
                'converted' => true,
                'original_extension' => strtolower($file->getClientOriginalExtension()),
                'final_extension' => 'pdf'
            ];

        } catch (\Exception $e) {
            $this->dispatch('conversion-status-updated', [
                'file' => $title,
                'status' => 'error',
                'error' => $e->getMessage(),
                'index' => $index
            ]);

            $this->conversionStatus[$index]['status'] = 'error';

            Log::error('Conversion error:', [
                'file' => $title,
                'safe_filename' => $safeFileName ?? 'unknown',
                'temp_path' => $absoluteTempPath ?? 'unknown',
                'source_path' => $file->getPathname(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Erreur lors de la conversion de '{$title}': " . $e->getMessage());
        } finally {
            if (isset($absoluteTempPath) && file_exists($absoluteTempPath)) {
                @unlink($absoluteTempPath);
                Log::info("Cleaned up temp file: {$absoluteTempPath}");
            }
        }
    }

    private function storeDocument($file, $title)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $fileName = time() . '_' . Str::slug($title) . '_' . Str::random(8) . '.' . $extension;
        $filePath = $file->storeAs('documents', $fileName, 'public');

        if (!$filePath) {
            throw new \Exception('Échec du stockage du fichier.');
        }

        return $filePath;
    }

    private function resetForm()
    {
        $this->file = [];
        $this->titles = [];
        $this->file_status = [];
        $this->conversionStatus = [];
        $this->uploadProgress = 0;
        $this->conversionProgress = 0;
        $this->currentConvertingFile = '';
    }

    public function checkLibreOfficeStatus()
    {
        $isAvailable = $this->getConversionService()->isLibreOfficeAvailable();

        $this->dispatch('libreoffice-status-checked', [
            'available' => $isAvailable,
            'message' => $isAvailable 
                ? 'LibreOffice est disponible - Conversion PDF activée' 
                : 'LibreOffice non disponible - Seuls les PDF seront acceptés'
        ]);

        return $isAvailable;
    }

    public function getFileConversionInfo($index)
    {
        if (!isset($this->file[$index])) {
            return null;
        }

        $file = $this->file[$index];
        $tempPath = storage_path('app/temp/temp_info_' . time() . '.' . $file->getClientOriginalExtension());

        try {
            $file->storeAs('temp', basename($tempPath));
            $info = $this->getConversionService()->getConversionInfo($tempPath);
            @unlink($tempPath);
            return $info;
        } catch (\Exception $e) {
            Log::error("Error getting conversion info: " . $e->getMessage());
            return null;
        }
    }

    public function resetErrorMessage()
    {
        $this->errorMessage = '';
    }

    public function resetSuccessMessage()
    {
        $this->successMessage = '';
    }

    public function render()
    {
        return view('livewire.teacher.document-upload', [
            'libreoffice_available' => $this->getConversionService()->isLibreOfficeAvailable()
        ]);
    }
}