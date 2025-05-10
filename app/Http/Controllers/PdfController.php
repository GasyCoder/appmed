<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class PdfController extends Controller
{
    public function show($filename)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $path = storage_path('app/public/documents/' . $filename);
        if (!file_exists($path)) {
            return response()->json(['error' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        // Increment view count
        $this->incrementView($filename);

        // Récupérer le document et les informations de l'enseignant
        $document = Document::where('file_path', 'like', '%' . $filename)->first();
        $teacherInfo = null;

        if ($document && $document->teacher) {
            $teacher = $document->teacher;
            $teacherInfo = [
                'name' =>  $teacher->name, // Utilise le nom avec grade
                'grade' => optional($teacher->profil)->grade,
                'title' => $document->title
            ];
        }

        $content = base64_encode(file_get_contents($path));
        return response()->json([
            'data' => $content,
            'filename' => $filename,
            'mime' => mime_content_type($path),
            'teacherInfo' => $teacherInfo
        ])->header('Cache-Control', 'no-cache, no-store, must-revalidate');
    }

    public function viewer($filename)
    {
        // Récupérer le document et les informations de l'enseignant
        $document = Document::where('file_path', 'like', '%' . $filename)->first();

        $teacherInfo = [];
        if ($document && $document->teacher) {
            $teacher = $document->teacher;
            $teacherInfo = [
                'name' => $teacher->name,
                'grade' => optional($teacher->profil)->grade,
                'title' => $document->title
            ];
        }

        // Increment view count
        $this->incrementView($filename);

        $css = file_get_contents(public_path('assets/css/flipbook.css'));
        return view('pdf.viewer', [
            'filename' => $filename,
            'css' => $css,
            'teacherInfo' => $teacherInfo,
            'document' => $document
        ]);
    }

    private function incrementView($filename)
    {
        $document = Document::where('file_path', 'like', '%' . $filename)->first();
        if ($document) {
            $document->registerView();
        }
    }
}
