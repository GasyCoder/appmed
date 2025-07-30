<?php

namespace App\Livewire\Teacher;

use App\Models\Document;
use App\Models\Niveau;
use App\Models\Parcour;
use App\Models\Semestre;
use App\Services\PdfConversionService;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Rule;

class DocumentEdit extends Component
{
    use WithFileUploads;
    use LivewireAlert;

    public Document $document;
    public $title;
    public $currentDateTime;
    public $currentUser;

    #[Rule(['required', 'exists:niveaux,id'])]
    public $niveau_id = '';

    #[Rule(['required', 'exists:parcours,id'])]
    public $parcour_id = '';

    public $is_actif;
    public $newFile;
    public $showNewFile = false;
    public $semestres_selected = [];

    protected function rules()
    {
        return [
            'title' => 'required|string|min:3|max:255',
            'newFile' => 'nullable|file|max:10240|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpeg,jpg,png',
            'niveau_id' => 'required|exists:niveaux,id',
            'parcour_id' => 'required|exists:parcours,id',
            'semestres_selected' => 'required|array|min:1',
        ];
    }

    protected function messages()
    {
        return [
            'title.required' => 'Le titre est requis',
            'title.min' => 'Le titre doit contenir au moins 3 caractères',
            'title.max' => 'Le titre ne peut pas dépasser 255 caractères',
            'niveau_id.required' => 'Le niveau est requis',
            'niveau_id.exists' => 'Niveau invalide',
            'parcour_id.required' => 'Le parcours est requis',
            'parcour_id.exists' => 'Parcours invalide',
            'newFile.file' => 'Fichier invalide',
            'newFile.max' => 'Taille maximale : 10MB',
            'newFile.mimes' => 'Types acceptés : PDF, Word, Excel, PowerPoint, Images',
            'semestres_selected.required' => 'Au moins un semestre actif est requis.',
            'semestres_selected.min' => 'Au moins un semestre actif est requis.',
        ];
    }

    private function getConversionService(): PdfConversionService
    {
        return app(PdfConversionService::class);
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

    /**
     * Renommer un fichier existant avec un nouveau titre
     */
    private function renameExistingFile($oldFilePath, $newTitle)
    {
        try {
            // Vérifier que l'ancien fichier existe
            if (!Storage::disk('public')->exists($oldFilePath)) {
                Log::error("Cannot rename: old file does not exist: {$oldFilePath}");
                return null;
            }

            // Extraire l'extension de l'ancien fichier
            $oldExtension = strtolower(pathinfo($oldFilePath, PATHINFO_EXTENSION));
            
            // Créer le nouveau nom de fichier avec le nouveau titre
            $cleanTitle = $this->sanitizeFilename($newTitle);
            $newFileName = time() . '_' . Str::slug($cleanTitle) . '_' . Str::random(8) . '.' . $oldExtension;
            $newFilePath = 'documents/' . $newFileName;

            // Obtenir les chemins absolus
            $oldAbsolutePath = Storage::disk('public')->path($oldFilePath);
            $newAbsolutePath = Storage::disk('public')->path($newFilePath);

            // Renommer le fichier physique
            if (rename($oldAbsolutePath, $newAbsolutePath)) {
                Log::info("File successfully renamed", [
                    'old_title' => $this->document->title,
                    'new_title' => $newTitle,
                    'old_path' => $oldFilePath,
                    'new_path' => $newFilePath
                ]);
                
                return $newFilePath;
            } else {
                Log::error("Failed to rename file physically", [
                    'old_path' => $oldAbsolutePath,
                    'new_path' => $newAbsolutePath
                ]);
                return null;
            }

        } catch (\Exception $e) {
            Log::error('Error renaming existing file', [
                'old_path' => $oldFilePath,
                'new_title' => $newTitle,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function mount(Document $document)
    {
        abort_if(
            !Auth::user()?->roles->contains('name', 'teacher') ||
            $document->uploaded_by !== Auth::id(),
            403
        );

        $this->document = $document;
        $this->title = $document->title;
        $this->niveau_id = $document->niveau_id;
        $this->parcour_id = $document->parcour_id;
        $this->is_actif = $document->is_actif;
        $this->currentDateTime = now()->format('Y-m-d H:i:s');
        $this->currentUser = Auth::user()->name;
        $this->semestres_selected = $document->semestre_id ? [$document->semestre_id] : [];
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
        return Niveau::query()
            ->whereHas('teachers', fn($q) => $q->where('niveau_user.user_id', Auth::id()))
            ->where('status', true)
            ->orderBy('name')
            ->get();
    }

    public function getTeacherParcoursProperty()
    {
        if (!$this->niveau_id) {
            return collect();
        }

        return Parcour::query()
            ->whereHas('teachers', fn($q) => $q->where('parcour_user.user_id', Auth::id()))
            ->where('status', true)
            ->whereExists(fn($query) => $query->select(DB::raw(1))
                ->from('niveau_user')
                ->where('user_id', Auth::id())
                ->where('niveau_id', $this->niveau_id))
            ->orderBy('name')
            ->get();
    }

    public function updatedNiveauId()
    {
        $this->parcour_id = '';
        $this->semestres_selected = [];
        if ($this->niveau_id) {
            $availableParcours = $this->teacherParcours;
            if ($availableParcours->isNotEmpty()) {
                $this->parcour_id = $availableParcours->first()->id;
            }
            $this->semestres_selected = $this->semestresActifs->pluck('id')->toArray();
        }
        $this->dispatch('parcours-updated');
    }

    public function updatedNewFile()
    {
        try {
            $this->validateOnly('newFile');
            $this->showNewFile = true;
        } catch (\Exception $e) {
            $this->newFile = null;
            $this->showNewFile = false;
            $this->alert('error', 'Le type de fichier n\'est pas accepté', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => true,
            ]);
        }
    }

    public function removeNewFile()
    {
        $this->newFile = null;
        $this->showNewFile = false;
    }

    protected function handleFileUpload()
    {
        if (!$this->newFile) {
            return null;
        }

        $originalExtension = strtolower($this->newFile->getClientOriginalExtension());
        $conversionService = $this->getConversionService();
        $needsConversion = $conversionService->isConvertibleFormat($this->newFile->getClientOriginalName());

        $cleanTitle = $this->sanitizeFilename($this->title);
        $fileName = time() . '_' . Str::slug($cleanTitle) . '_' . Str::random(8) . ($needsConversion ? '.pdf' : '.' . $originalExtension);
        $filePath = $needsConversion ? null : $this->newFile->storeAs('documents', $fileName, 'public');

        $fileData = [
            'file_path' => $filePath,
            'protected_path' => $filePath,
            'file_type' => $this->newFile->getMimeType(),
            'file_size' => $this->newFile->getSize(),
            'original_filename' => $this->newFile->getClientOriginalName(),
            'original_extension' => $originalExtension,
        ];

        if ($needsConversion) {
            $convertedPath = $this->convertToPdfUsingService($this->newFile, $cleanTitle);
            if ($convertedPath) {
                $fileData['file_path'] = $convertedPath;
                $fileData['protected_path'] = $convertedPath;
                $fileData['file_type'] = 'application/pdf';
                $fileData['file_size'] = Storage::disk('public')->size($convertedPath);
                $fileData['converted_from'] = $originalExtension;
                $fileData['converted_at'] = now();
            }
        }

        if (!$fileData['file_path']) {
            throw new \Exception('Échec du stockage ou de la conversion du fichier.');
        }

        return $fileData;
    }

    protected function convertToPdfUsingService($file, $title)
    {
        $tempPath = null;
        $absoluteTempPath = null;

        try {
            if (!$file->isValid()) {
                throw new \Exception("Le fichier uploadé est invalide: " . $file->getClientOriginalName());
            }

            $fileSize = $file->getSize();
            if ($fileSize === 0) {
                throw new \Exception("Le fichier uploadé est vide: " . $file->getClientOriginalName());
            }

            $safeFileName = $this->createSafeTemporaryFilename($file, $title);
            $tempDir = storage_path('app/temp');
            $absoluteTempPath = $tempDir . '/' . $safeFileName;

            if (!file_exists($tempDir)) {
                if (!mkdir($tempDir, 0755, true)) {
                    throw new \Exception("Impossible de créer le dossier temporaire: {$tempDir}");
                }
            }

            if (!is_writable($tempDir)) {
                if (!chmod($tempDir, 0755)) {
                    throw new \Exception("Dossier temporaire non accessible en écriture: {$tempDir}");
                }
            }

            if (!copy($file->getPathname(), $absoluteTempPath)) {
                throw new \Exception("Échec du déplacement du fichier temporaire: {$safeFileName}");
            }

            if (!file_exists($absoluteTempPath) || !is_readable($absoluteTempPath) || filesize($absoluteTempPath) === 0) {
                throw new \Exception("Validation du fichier temporaire échouée: {$absoluteTempPath}");
            }

            $outputDir = storage_path('app/public/documents');
            $pdfFileName = time() . '_' . Str::slug($title) . '_' . Str::random(8) . '.pdf';

            if (!file_exists($outputDir)) {
                if (!mkdir($outputDir, 0755, true)) {
                    throw new \Exception("Impossible de créer le dossier de sortie: {$outputDir}");
                }
            }

            if (!is_writable($outputDir)) {
                if (!chmod($outputDir, 0755)) {
                    throw new \Exception("Dossier de sortie non accessible en écriture: {$outputDir}");
                }
            }

            if (file_exists($outputDir . '/' . $pdfFileName)) {
                $pdfFileName = time() . '_' . Str::slug($title) . '_' . Str::random(8) . '.pdf';
            }

            $convertedFilePath = $this->getConversionService()->convertToPdf(
                $absoluteTempPath,
                $outputDir,
                $pdfFileName
            );

            if (!file_exists($convertedFilePath)) {
                throw new \Exception("La conversion a échoué - fichier PDF non trouvé: {$convertedFilePath}");
            }

            return 'documents/' . $pdfFileName;

        } catch (\Exception $e) {
            Log::error('Conversion error in DocumentEdit:', [
                'file' => $title,
                'temp_path' => $absoluteTempPath ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        } finally {
            if (isset($absoluteTempPath) && file_exists($absoluteTempPath)) {
                @unlink($absoluteTempPath);
                Log::info("Cleaned up temp file in DocumentEdit: {$absoluteTempPath}");
            }
        }
    }

    public function updateDocument()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            Log::info("Starting document update", [
                'document_id' => $this->document->id,
                'old_title' => $this->document->title,
                'new_title' => $this->title,
                'user_id' => Auth::id()
            ]);

            $oldFilePath = $this->document->file_path;

            $updateData = [
                'title' => $this->title,
                'niveau_id' => $this->niveau_id,
                'parcour_id' => $this->parcour_id,
                'is_actif' => $this->is_actif,
                'updated_at' => now(),
            ];

            // Si nouveau fichier, traiter l'upload
            if ($this->newFile) {
                $fileData = $this->handleFileUpload();
                if ($fileData) {
                    $updateData = array_merge($updateData, $fileData);
                    
                    // Supprimer l'ancien fichier physique
                    if ($oldFilePath && Storage::disk('public')->exists($oldFilePath)) {
                        Storage::disk('public')->delete($oldFilePath);
                        Log::info("Old file deleted: {$oldFilePath}");
                    }
                }
            } 
            // Si pas de nouveau fichier mais titre modifié, renommer le fichier existant
            elseif ($this->title !== $this->document->title && $oldFilePath) {
                $newFilePath = $this->renameExistingFile($oldFilePath, $this->title);
                if ($newFilePath) {
                    $updateData['file_path'] = $newFilePath;
                    $updateData['protected_path'] = $newFilePath;
                    
                    Log::info("File renamed for title change", [
                        'old_path' => $oldFilePath,
                        'new_path' => $newFilePath,
                        'old_title' => $this->document->title,
                        'new_title' => $this->title
                    ]);
                }
            }

            // Mettre à jour via Query Builder
            $affectedRows = Document::where('id', $this->document->id)->update($updateData);
            
            Log::info("Document updated in database", [
                'document_id' => $this->document->id,
                'affected_rows' => $affectedRows,
                'new_title' => $this->title,
                'update_data' => $updateData
            ]);

            // Recharger l'instance depuis la base
            $this->document = Document::find($this->document->id);
            
            Log::info("Document reloaded from database", [
                'document_id' => $this->document->id,
                'current_title' => $this->document->title,
                'updated_at' => $this->document->updated_at
            ]);

            DB::commit();

            $this->alert('success', 'Document mis à jour avec succès', [
                'position' => 'top-end',
                'timer' => 5000,
                'toast' => true,
                'timerProgressBar' => true,
            ]);

            // Émettre événement pour informer les autres composants
            $this->dispatch('document-updated', [
                'document_id' => $this->document->id,
                'new_title' => $this->title,
                'timestamp' => now()->timestamp
            ]);

            // Redirection vers la liste des documents
            return $this->redirect(route('document.teacher'), navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour document', [
                'error' => $e->getMessage(),
                'document_id' => $this->document->id,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->alert('error', 'Erreur: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 7000,
                'toast' => false,
            ]);
        } finally {
            $this->getConversionService()->cleanupTempFiles();
        }
    }

    public function render()
    {
        return view('livewire.teacher.document-edit', [
            'niveaux' => $this->teacherNiveaux,
            'parcours' => $this->teacherParcours,
            'semestresActifs' => $this->semestresActifs,
        ]);
    }
}