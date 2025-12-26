<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonException;

class ClaudeAIService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.anthropic.com/v1/messages';
    protected string $model  = 'claude-sonnet-4-20250514';

    // ✅ Mock activé par défaut (tu peux passer à false plus tard)
    protected bool $mockMode = true;

    // ✅ IMPORTANT: on lit explicitement sur le disque local
    protected string $faqDisk = 'local';
    protected string $faqPath = 'chatbot/faq.json';

    protected ?array $faqData = null;

    public function __construct()
    {
        $this->apiKey = (string) config('services.anthropic.api_key');

        $this->mockMode = (bool) config('chatbot.mock', $this->mockMode);
        $this->faqDisk  = (string) config('chatbot.faq_disk', $this->faqDisk);
        $this->faqPath  = (string) config('chatbot.faq_path', $this->faqPath);

        $this->loadFaqData();
    }


    /**
     * Charger les données FAQ depuis storage (disk local)
     */
    protected function loadFaqData(): void
    {
        $this->faqData = null;

        // 1) Disk sûr (fallback sur "local")
        $disk = (string) ($this->faqDisk ?? '');
        $disk = $disk !== '' ? $disk : (string) config('filesystems.default', 'local');

        // 2) Vérifier que le disk existe en config
        $diskConfigKey = sprintf('filesystems.disks.%s', $disk);
        $diskConfig = config($diskConfigKey);

        if (!is_array($diskConfig)) {
            Log::error('FAQ disk non configuré', [
                'disk' => $disk,
                'config_key' => $diskConfigKey,
                'available_disks' => array_keys((array) config('filesystems.disks', [])),
            ]);

            // fallback sur local si possible
            $disk = 'local';
            $diskConfigKey = 'filesystems.disks.local';
            $diskConfig = config($diskConfigKey);

            if (!is_array($diskConfig)) {
                Log::critical('FAQ: aucun disk utilisable (local manquant)', [
                    'config_key' => $diskConfigKey,
                ]);
                return;
            }
        }

        $root = $diskConfig['root'] ?? null;

        // 3) Path
        $path = (string) ($this->faqPath ?? '');
        if ($path === '') {
            Log::error('FAQ path vide/non défini', ['disk' => $disk, 'root' => $root]);
            return;
        }

        try {
            $fs = Storage::disk($disk);

            if (!$fs->exists($path)) {
                Log::warning('FAQ JSON introuvable', [
                    'disk' => $disk,
                    'path' => $path,
                    'root' => $root,
                ]);
                return;
            }

            $jsonContent = (string) $fs->get($path);

            // Optionnel: supprime BOM si présent (évite JSON invalid)
            $jsonContent = ltrim($jsonContent, "\xEF\xBB\xBF");

            $data = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data) || empty($data['responses']) || !is_array($data['responses'])) {
                Log::error('FAQ JSON structure invalide', [
                    'disk' => $disk,
                    'path' => $path,
                    'root' => $root,
                    'type' => gettype($data),
                    'keys' => is_array($data) ? array_keys($data) : null,
                ]);
                return;
            }

            $this->faqData = $data;

        } catch (JsonException $e) {
            Log::error('FAQ JSON invalide', [
                'disk' => $disk,
                'path' => $path,
                'root' => $root,
                'error' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Erreur chargement FAQ', [
                'disk' => $disk,
                'path' => $path,
                'root' => $root,
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function chat(array $messages, ?string $systemPrompt = null): ?string
    {
        if ($this->mockMode) {
            return $this->getMockResponse($messages);
        }

        try {
            $payload = [
                'model' => $this->model,
                'max_tokens' => 1024,
                'messages' => $messages,
            ];

            if ($systemPrompt) {
                $payload['system'] = $systemPrompt;
            }

            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
            ])->timeout(60)->post($this->apiUrl, $payload);

            if ($response->successful()) {
                $data = $response->json();
                return $data['content'][0]['text'] ?? null;
            }

            Log::error('Claude API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;

        } catch (\Throwable $e) {
            Log::error('Claude AI Service Exception', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Obtenir la réponse depuis le fichier JSON (mock)
     */
    protected function getMockResponse(array $messages): string
    {
        if (!$this->faqData || empty($this->faqData['responses'])) {
            return "Désolé, le système de FAQ n'est pas disponible. Contactez le support.";
        }

        $question = $this->extractLastUserMessage($messages);
        if ($question === '') {
            return $this->faqData['default_response'] ?? "Posez-moi une question !";
        }

        $normalizedQ = $this->normalizeText($question);

        $bestResponse = null;
        $bestScore = 0;

        foreach ($this->faqData['responses'] as $faq) {
            $keywords = $faq['keywords'] ?? [];
            if (!is_array($keywords)) continue;

            $score = 0;

            foreach ($keywords as $kw) {
                $kwNorm = $this->normalizeText((string) $kw);
                if ($kwNorm === '') continue;

                // phrase match (mot de passe, etc.)
                if (str_contains($normalizedQ, $kwNorm)) {
                    $score += 3;
                    continue;
                }

                // mot exact (boundary)
                if ($this->wordBoundaryMatch($kwNorm, $normalizedQ)) {
                    $score += 2;
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestResponse = $faq['response'] ?? null;
            }
        }

        if (is_string($bestResponse) && $bestScore >= 2) {
            return $bestResponse;
        }

        return $this->faqData['default_response'] ?? "Désolé, je n'ai pas compris.";
    }

    private function extractLastUserMessage(array $messages): string
    {
        for ($i = count($messages) - 1; $i >= 0; $i--) {
            $m = $messages[$i] ?? null;
            if (!is_array($m)) continue;
            if (($m['role'] ?? '') === 'user') {
                $c = $m['content'] ?? '';
                return is_string($c) ? trim($c) : '';
            }
        }
        return '';
    }

    private function wordBoundaryMatch(string $word, string $text): bool
    {
        // si keyword contient des espaces, on traite comme phrase
        if (str_contains($word, ' ')) {
            return str_contains($text, $word);
        }

        return (bool) preg_match('/(^|\s)' . preg_quote($word, '/') . '($|\s)/', $text);
    }

    protected function normalizeText(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');

        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        if ($converted !== false) {
            $text = $converted;
        }

        $text = preg_replace('/[^a-z0-9\s]/i', ' ', $text) ?? $text;
        $text = preg_replace('/\s+/', ' ', trim($text)) ?? trim($text);

        return $text;
    }

    public function getFaqContext(): string
    {
        return "Tu es un assistant virtuel pour EpiRC (Faculté de Médecine, Université de Mahajanga). Réponds de façon claire et concise.";
    }
}
