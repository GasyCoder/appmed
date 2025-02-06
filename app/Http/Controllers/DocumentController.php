<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::where('file_type', 'application/pdf')
                    ->where('is_actif', true)
                    ->latest()
                    ->get();

        return view('documents.index', compact('documents'));
    }

    public function incrementView($id)
    {
        $document = Document::findOrFail($id);
        $document->registerView();
        return response()->json(['success' => true]);
    }
}
