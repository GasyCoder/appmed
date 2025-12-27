<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Services\ClaudeAIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function __construct(private readonly ClaudeAIService $ai)
    {
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message'    => ['required', 'string', 'max:500'],
            'session_id' => ['required', 'string', 'max:120'],
        ]);

        $sessionId = trim($validated['session_id']);
        $message   = trim($validated['message']);

        if ($sessionId === '' || $message === '') {
            return response()->json([
                'success' => false,
                'message' => 'Message ou session invalide.',
                'timestamp' => now()->format('H:i'),
            ], 422);
        }

        $userId = Auth::id();

        // 1) Sauvegarder le message utilisateur
        ChatMessage::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'role' => 'user',
            'message' => $message,
            'metadata' => [
                'ip' => $request->ip(),
                'ua' => (string) $request->userAgent(),
            ],
        ]);

        // 2) Construire l'historique (dernier N messages, remis dans l'ordre)
        $historyLimit = 12;

        $history = ChatMessage::query()
            ->where('session_id', $sessionId)
            ->orderByDesc('id')
            ->limit($historyLimit)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $m) => [
                'role' => $m->role,
                'content' => $m->message,
            ])
            ->toArray();

        // 3) Appel “IA” (en réalité: profil + programmes/documents + fallback FAQ/IA)
        $systemPrompt = $this->ai->getFaqContext(Auth::user());
        $reply = $this->ai->chat($history, $systemPrompt);

        if (!is_string($reply) || trim($reply) === '') {
            return response()->json([
                'success' => false,
                'message' => "Désolé, je rencontre un problème technique. Veuillez réessayer.",
                'timestamp' => now()->format('H:i'),
                'session_id' => $sessionId,
            ], 500);
        }

        $reply = trim($reply);

        // 4) Sauvegarder la réponse assistant
        ChatMessage::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'role' => 'assistant',
            'message' => $reply,
            'metadata' => [
                'source' => (bool) config('chatbot.mock', true) ? 'faq_json+db' : 'anthropic+db',
            ],
        ]);

        return response()->json([
            'success' => true,
            'message' => $reply,
            'timestamp' => now()->format('H:i'),
            'session_id' => $sessionId,
        ]);
    }

    public function getHistory(Request $request, string $sessionId): JsonResponse
    {
        $sessionId = trim($sessionId);

        $limit = (int) $request->query('limit', 20);
        $limit = max(1, min($limit, 50));

        $messages = ChatMessage::query()
            ->where('session_id', $sessionId)
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $m) => [
                'role' => $m->role,
                'content' => $m->message,
                'timestamp' => $m->created_at->format('H:i'),
            ]);

        return response()->json(['messages' => $messages]);
    }

    public function clearHistory(string $sessionId): JsonResponse
    {
        $sessionId = trim($sessionId);

        ChatMessage::query()
            ->where('session_id', $sessionId)
            ->delete();

        return response()->json(['success' => true]);
    }
}
