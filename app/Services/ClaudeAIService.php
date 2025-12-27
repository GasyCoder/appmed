<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use JsonException;
use Throwable;

class ClaudeAIService
{
    protected string $apiKey;
    protected string $apiUrl = 'https://api.anthropic.com/v1/messages';
    protected string $model  = 'claude-sonnet-4-20250514';

    // ✅ Mock activé par défaut
    protected bool $mockMode = true;

    // ✅ FAQ JSON
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

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    public function chat(array $messages, ?string $systemPrompt = null): ?string
    {
        $user = Auth::user();

        $question = $this->extractLastUserMessage($messages);
        $qNorm = $this->normalizeText($question);

        if ($qNorm === '') {
            return $this->faqData['default_response'] ?? "Posez-moi une question.";
        }

        // 1) Profil assistant (réponse forcée, stable)
        if ($this->isProfileQuestion($qNorm)) {
            return $this->getAssistantProfileText($user);
        }

        // 2) Réponses intelligentes sur Programmes (UE/EC) + Documents
        $dbAnswer = $this->answerFromDatabase($question, $user);
        if ($dbAnswer !== null) {
            return $dbAnswer;
        }

        // 3) Fallback FAQ JSON (mock) ou IA (anthropic)
        if ($this->mockMode) {
            return $this->getMockResponse($messages);
        }

        return $this->callAnthropic($messages, $systemPrompt ?: $this->getFaqContext($user));
    }

    public function getFaqContext(?User $user = null): string
    {
        $name = (string) config('chatbot.assistant.name', 'Assistant EPIRC');
        $prod = (string) config('chatbot.assistant.product', 'EPIRC');
        $by   = (string) config('chatbot.assistant.builder', 'GasyCoder');
        $org  = (string) config('chatbot.assistant.organization', 'Université de Mahajanga');

        $userCtx = '';
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('student')) {
            $niv = $user->niveau_id ?? null;
            $par = $user->parcour_id ?? null;
            $userCtx = "Contexte étudiant: niveau_id={$niv}, parcour_id={$par}.";
        }

        return
            "Tu es {$name}, un assistant intégré à {$prod}.\n" .
            "Identité officielle: tu as été développé par {$by} ({$org}). Tu n'es pas ChatGPT d'OpenAI.\n" .
            "Règles:\n" .
            "- Si on te demande qui tu es / qui t'a créé / comment tu fonctionnes / contrôle / confidentialité: réponds selon le profil officiel.\n" .
            "- Réponds en français clair, concis, orienté étudiant.\n" .
            "- Si la question concerne UE/EC, crédits, coefficients, semestres: base-toi sur les données programmes.\n" .
            "- Si la question concerne documents/cours: propose des documents pertinents.\n" .
            ($userCtx ? "\n{$userCtx}\n" : "");
    }

    // -------------------------------------------------------------------------
    // 1) Profil assistant (stable)
    // -------------------------------------------------------------------------

    private function isProfileQuestion(string $qNorm): bool
    {
        $triggers = [
            'qui es tu', 'tu es qui', 'presentation', 'presenter', 'profil',
            'qui ta cree', 'qui t a cree', 'createur', 'developpeur',
            'comment tu fonctionne', 'comment tu marches', 'comment ca marche',
            'es tu chatgpt', 'chatgpt', 'openai', 'gpt',
            'controle en direct', 'on te controle', 'quelqu un te controle',
            'confidentialite', 'privacy', 'donnees', 'stocke', 'enregistre',
            'que peux tu faire', 'tu peux faire quoi', 'capacites',
        ];

        foreach ($triggers as $t) {
            if (str_contains($qNorm, $t)) return true;
        }

        return false;
    }

    private function getAssistantProfileText(?User $user = null): string
    {
        $name   = (string) config('chatbot.assistant.name', 'Assistant EPIRC');
        $prod   = (string) config('chatbot.assistant.product', 'EPIRC');
        $by     = (string) config('chatbot.assistant.builder', 'GasyCoder');
        $org    = (string) config('chatbot.assistant.organization', 'Université de Mahajanga');
        $engine = (string) config('chatbot.assistant.engine', 'FAQ locale + Données (Programmes/Documents) + Option IA');

        $userLine = '';
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('student')) {
            $userLine = "\n\nVotre profil: étudiant (niveau_id={$user->niveau_id}, parcour_id={$user->parcour_id}).";
        }

        return
            "Je suis {$name}, l’assistant intégré à {$prod}.\n\n" .
            "Qui m’a créé ? {$by} ({$org}).\n\n" .
            "Comment je réponds ?\n" .
            "- Je m’appuie sur une base FAQ et sur les données de la plateforme (programmes UE/EC, documents, informations utiles).\n" .
            "- Selon la configuration, je peux aussi utiliser un moteur IA pour formuler des réponses plus naturelles.\n\n" .
            "Est-ce qu’une personne me contrôle en direct ? Non. Je réponds automatiquement.\n\n" .
            "Système: {$engine}." .
            $userLine;
    }

    // -------------------------------------------------------------------------
    // 2) Réponses DB (Programmes + Documents) : “plus intelligent”
    // -------------------------------------------------------------------------

    private function answerFromDatabase(string $question, ?User $user = null): ?string
    {
        $qNorm = $this->normalizeText($question);

        // A) Programme par code UE/EC
        $code = $this->extractProgrammeCode($question);
        if ($code !== null) {
            $answer = $this->answerProgrammeByCode($code, $question, $user);
            if ($answer !== null) return $answer;
        }

        // B) Programme par sujet (inférence simple)
        $topicAnswer = $this->answerProgrammeByTopicInference($qNorm, $user);
        if ($topicAnswer !== null) return $topicAnswer;

        // C) Documents (si question contient des signaux “cours/documents”)
        if ($this->looksLikeDocumentQuestion($qNorm)) {
            $docsAnswer = $this->answerDocuments($question, $user);
            if ($docsAnswer !== null) return $docsAnswer;
        }

        // D) Mix (si question évoque UE/EC/cours)
        if ($this->looksLikeProgrammeQuestion($qNorm)) {
            $progAnswer = $this->answerProgrammeByNameSearch($question, $user);
            if ($progAnswer !== null) return $progAnswer;

            // si pas trouvé, tenter docs
            $docsAnswer = $this->answerDocuments($question, $user);
            if ($docsAnswer !== null) return $docsAnswer;
        }

        return null;
    }

    private function looksLikeProgrammeQuestion(string $qNorm): bool
    {
        $signals = ['ue', 'ec', 'programme', 'credits', 'coefficient', 'semestre', 'matiere', 'module', 'cours'];
        foreach ($signals as $s) {
            if (preg_match('/(^|\s)'.preg_quote($s,'/').'(\s|$)/', $qNorm)) return true;
        }
        return false;
    }

    private function looksLikeDocumentQuestion(string $qNorm): bool
    {
        $signals = ['document', 'pdf', 'fichier', 'telecharger', 'télécharger', 'lien', 'support', 'cours', 'diapo', 'ppt', 'pptx'];
        foreach ($signals as $s) {
            if (str_contains($qNorm, $this->normalizeText($s))) return true;
        }
        return false;
    }

    private function extractProgrammeCode(string $question): ?string
    {
        // UE1 / ue 1 / EC3 / ec 2
        if (preg_match('/\b(ue|ec)\s*([0-9]{1,2})\b/i', $question, $m)) {
            return strtoupper($m[1]) . (string) ((int) $m[2]);
        }
        return null;
    }

    private function answerProgrammeByCode(string $code, string $question, ?User $user = null): ?string
    {
        $type = str_starts_with($code, 'UE') ? 'UE' : 'EC';

        $query = Programme::query()
            ->where('type', $type)
            ->where('code', $code);

        // si étudiant: filtrer niveau/parcours (si colonnes existent)
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('student')) {
            if (SchemaHas::column('programmes', 'niveau_id') && $user->niveau_id) {
                $query->where('niveau_id', $user->niveau_id);
            }
            if (SchemaHas::column('programmes', 'parcour_id') && $user->parcour_id) {
                $query->where('parcour_id', $user->parcour_id);
            }
        }

        $items = $query->orderBy('id')->limit(10)->get();
        if ($items->isEmpty()) return null;

        // Si EC ambigu (EC1 existe sous plusieurs UE), afficher la liste
        if ($type === 'EC' && $items->count() > 1) {
            $lines = [];
            foreach ($items as $ec) {
                $ue = $ec->parent_id ? Programme::find($ec->parent_id) : null;
                $ueLabel = $ue ? "{$ue->code} - {$ue->name}" : "UE inconnue";
                $lines[] = "- {$ec->code} : {$ec->name} (dans {$ueLabel})";
            }

            return
                "Plusieurs éléments correspondent à **{$code}**.\n\n" .
                "Voici les correspondances possibles :\n" .
                implode("\n", $lines) .
                "\n\nPrécisez l’UE (ex: “EC1 de UE2”).";
        }

        $p = $items->first();

        // Si UE => afficher ses EC
        if ($p->type === 'UE') {
            $ecs = Programme::query()
                ->where('type', 'EC')
                ->where('parent_id', $p->id)
                ->orderBy('order')
                ->get();

            $ecLines = [];
            foreach ($ecs as $ec) {
                $ecLines[] = "- {$ec->code} : {$ec->name} (crédits {$ec->credits}, coef {$ec->coefficient})";
            }

            return
                "**{$p->code} – {$p->name}**\n" .
                "Semestre: {$p->semestre_id} | Crédits: {$p->credits} | Coef: {$p->coefficient}\n\n" .
                ($ecLines ? "EC inclus :\n" . implode("\n", $ecLines) : "Aucun EC enregistré pour cette UE.") .
                $this->appendUsefulDocsHint($p, $user);
        }

        // EC => afficher UE parent
        $ue = $p->parent_id ? Programme::find($p->parent_id) : null;
        $ueText = $ue ? "\nUE: {$ue->code} – {$ue->name}" : '';

        return
            "**{$p->code} – {$p->name}**\n" .
            "Semestre: {$p->semestre_id} | Crédits: {$p->credits} | Coef: {$p->coefficient}" .
            $ueText .
            $this->appendUsefulDocsHint($p, $user);
    }

    private function answerProgrammeByNameSearch(string $question, ?User $user = null): ?string
    {
        $tokens = $this->tokens($question);
        if (count($tokens) < 2) return null;

        $q = Programme::query()->whereIn('type', ['UE', 'EC']);

        if ($user && method_exists($user, 'hasRole') && $user->hasRole('student')) {
            if (SchemaHas::column('programmes', 'niveau_id') && $user->niveau_id) {
                $q->where('niveau_id', $user->niveau_id);
            }
            if (SchemaHas::column('programmes', 'parcour_id') && $user->parcour_id) {
                $q->where('parcour_id', $user->parcour_id);
            }
        }

        // filtrage LIKE (simple et robuste)
        $q->where(function ($sub) use ($tokens) {
            foreach (array_slice($tokens, 0, 6) as $t) {
                $sub->orWhere('name', 'like', '%' . $t . '%');
            }
        });

        $candidates = $q->limit(30)->get();
        if ($candidates->isEmpty()) return null;

        // score par overlap tokens
        $best = null;
        $bestScore = 0;

        foreach ($candidates as $p) {
            $nameNorm = $this->normalizeText((string) $p->name);
            $score = 0;
            foreach ($tokens as $t) {
                if ($t !== '' && str_contains($nameNorm, $t)) $score++;
            }
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $p;
            }
        }

        if (!$best || $bestScore < 2) return null;

        // Réutiliser la réponse "by code"
        return $this->answerProgrammeByCode((string) $best->code, $question, $user);
    }

    private function answerProgrammeByTopicInference(string $qNorm, ?User $user = null): ?string
    {
        // “prediction” simple : mapping sujets -> UE
        $map = [
            'statistique descriptive' => 'UE2',
            'statistique inferentielle' => 'UE7',
            'inferentielle' => 'UE7',
            'test statistique' => 'UE7',
            'p value' => 'UE7',
            'epidemiologie' => 'UE3',
            'biais' => 'UE3',
            'validite' => 'UE3',
            'recherche clinique' => 'UE4',
            'essais cliniques' => 'UE4',
            'bibliographique' => 'UE6',
            'lecture critique' => 'UE6',
            'questionnaire' => 'UE9',
            'kobo' => 'UE9',
            'googleform' => 'UE9',
            'methodologie' => 'UE10',
            'qualitative' => 'UE15',
            'regression' => 'UE16',
            'survie' => 'UE16',
            'series temporelles' => 'UE16',
            'meta analyse' => 'UE17',
            'revue systematique' => 'UE17',
            'redaction memoire' => 'UE18',
            'soutenance' => 'UE18',
        ];

        foreach ($map as $topic => $ueCode) {
            if (str_contains($qNorm, $this->normalizeText($topic))) {
                $ans = $this->answerProgrammeByCode($ueCode, $topic, $user);
                if ($ans) {
                    return "Selon votre question, le cours le plus pertinent est probablement :\n\n" . $ans;
                }
            }
        }

        return null;
    }

    private function appendUsefulDocsHint(Programme $p, ?User $user = null): string
    {
        // Ajoute une mini suggestion doc si on trouve des docs
        $docs = $this->findDocumentsForProgramme($p, $user, 3);
        if ($docs === null || count($docs) === 0) return '';

        $lines = [];
        foreach ($docs as $d) {
            $lines[] = "- {$d['title']} ({$d['url']})";
        }

        return "\n\nDocuments liés :\n" . implode("\n", $lines);
    }

    private function answerDocuments(string $question, ?User $user = null): ?string
    {
        $tokens = $this->tokens($question);
        if (empty($tokens)) return null;

        $q = Document::query()->where('is_archive', false);

        // Si étudiant: filtrer par niveau/parcours via ta méthode canAccess (à défaut, filtrage simple)
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('student')) {
            $q->where('is_actif', true);

            if ($user->niveau_id) $q->where(function ($sub) use ($user) {
                $sub->whereNull('niveau_id')->orWhere('niveau_id', $user->niveau_id);
            });

            if ($user->parcour_id) $q->where(function ($sub) use ($user) {
                $sub->whereNull('parcour_id')->orWhere('parcour_id', $user->parcour_id);
            });
        }

        $q->where(function ($sub) use ($tokens) {
            foreach (array_slice($tokens, 0, 6) as $t) {
                $sub->orWhere('title', 'like', '%' . $t . '%');
                $sub->orWhere('original_filename', 'like', '%' . $t . '%');
            }
        });

        $docs = $q->orderByDesc('created_at')->limit(5)->get();
        if ($docs->isEmpty()) return null;

        $lines = [];
        foreach ($docs as $d) {
            $lines[] = "- {$d->title} : " . $this->documentSafeUrl($d);
        }

        return
            "Voici des documents qui correspondent à votre demande :\n\n" .
            implode("\n", $lines) .
            "\n\nSi vous me donnez le nom exact de l’UE/EC (ex: UE7), je peux affiner.";
    }

    private function findDocumentsForProgramme(Programme $p, ?User $user = null, int $limit = 3): ?array
    {
        try {
            $q = Document::query()->where('is_archive', false);

            if ($user && method_exists($user, 'hasRole') && $user->hasRole('student')) {
                $q->where('is_actif', true);

                if ($user->niveau_id) $q->where(function ($sub) use ($user) {
                    $sub->whereNull('niveau_id')->orWhere('niveau_id', $user->niveau_id);
                });

                if ($user->parcour_id) $q->where(function ($sub) use ($user) {
                    $sub->whereNull('parcour_id')->orWhere('parcour_id', $user->parcour_id);
                });
            }

            // lien direct programme_id
            if (SchemaHas::column('documents', 'programme_id')) {
                $q2 = (clone $q)->where('programme_id', $p->id)->limit($limit)->get();
                if ($q2->isNotEmpty()) {
                    return $q2->map(fn($d) => [
                        'title' => (string) $d->title,
                        'url' => $this->documentSafeUrl($d),
                    ])->all();
                }
            }

            // fallback: match title
            $tokens = $this->tokens($p->name);
            if (empty($tokens)) return null;

            $q->where(function ($sub) use ($tokens) {
                foreach (array_slice($tokens, 0, 4) as $t) {
                    $sub->orWhere('title', 'like', '%' . $t . '%');
                }
            });

            $docs = $q->limit($limit)->get();
            if ($docs->isEmpty()) return null;

            return $docs->map(fn($d) => [
                'title' => (string) $d->title,
                'url' => $this->documentSafeUrl($d),
            ])->all();

        } catch (Throwable $e) {
            Log::warning('findDocumentsForProgramme failed', ['e' => $e->getMessage()]);
            return null;
        }
    }

    private function documentSafeUrl(Document $d): string
    {
        // Ton modèle Document a déjà externalReadUrl()
        try {
            if (method_exists($d, 'isExternalLink') && $d->isExternalLink()) {
                if (method_exists($d, 'externalReadUrl')) return (string) $d->externalReadUrl();
                return (string) $d->file_path;
            }
        } catch (Throwable) {
            // ignore
        }

        // Si tu as une route viewer: document.viewer
        try {
            return route('document.viewer', $d);
        } catch (Throwable) {
            return (string) ($d->file_path ?? '');
        }
    }

    private function tokens(string $text): array
    {
        $norm = $this->normalizeText($text);
        if ($norm === '') return [];

        $parts = preg_split('/\s+/', $norm) ?: [];
        $stop = ['de','la','le','les','des','du','un','une','et','ou','a','au','aux','en','dans','pour','sur','avec','par','ce','cette','ces','est','sont','quoi','comment','svp'];
        $out = [];

        foreach ($parts as $p) {
            $p = trim($p);
            if ($p === '' || strlen($p) < 3) continue;
            if (in_array($p, $stop, true)) continue;
            $out[] = $p;
        }

        return array_values(array_unique($out));
    }

    // -------------------------------------------------------------------------
    // 3) Fallback FAQ JSON + IA
    // -------------------------------------------------------------------------

    protected function loadFaqData(): void
    {
        $this->faqData = null;

        $disk = (string) ($this->faqDisk ?? '');
        $disk = $disk !== '' ? $disk : (string) config('filesystems.default', 'local');

        $diskConfigKey = sprintf('filesystems.disks.%s', $disk);
        $diskConfig = config($diskConfigKey);

        if (!is_array($diskConfig)) {
            Log::error('FAQ disk non configuré', [
                'disk' => $disk,
                'config_key' => $diskConfigKey,
                'available_disks' => array_keys((array) config('filesystems.disks', [])),
            ]);

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

        $path = (string) ($this->faqPath ?? '');
        if ($path === '') {
            Log::error('FAQ path vide/non défini', ['disk' => $disk]);
            return;
        }

        try {
            $fs = Storage::disk($disk);

            if (!$fs->exists($path)) {
                Log::warning('FAQ JSON introuvable', ['disk' => $disk, 'path' => $path]);
                return;
            }

            $jsonContent = (string) $fs->get($path);
            $jsonContent = ltrim($jsonContent, "\xEF\xBB\xBF");

            $data = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($data) || empty($data['responses']) || !is_array($data['responses'])) {
                Log::error('FAQ JSON structure invalide', ['disk' => $disk, 'path' => $path]);
                return;
            }

            $this->faqData = $data;

        } catch (JsonException $e) {
            Log::error('FAQ JSON invalide', ['disk' => $disk, 'path' => $path, 'error' => $e->getMessage()]);
        } catch (Throwable $e) {
            Log::error('Erreur chargement FAQ', ['disk' => $disk, 'path' => $path, 'error' => $e->getMessage()]);
        }
    }

    protected function getMockResponse(array $messages): string
    {
        if (!$this->faqData || empty($this->faqData['responses'])) {
            return "Désolé, le système de FAQ n'est pas disponible. Contactez le support.";
        }

        $question = $this->extractLastUserMessage($messages);
        if ($question === '') {
            return $this->faqData['default_response'] ?? "Posez-moi une question.";
        }

        $normalizedQ = $this->normalizeText($question);

        // Profil forcé (même en mock)
        if ($this->isProfileQuestion($normalizedQ)) {
            return $this->getAssistantProfileText(Auth::user());
        }

        $bestResponse = null;
        $bestScore = 0;

        foreach ($this->faqData['responses'] as $faq) {
            $keywords = $faq['keywords'] ?? [];
            if (!is_array($keywords)) continue;

            $score = 0;

            foreach ($keywords as $kw) {
                $kwNorm = $this->normalizeText((string) $kw);
                if ($kwNorm === '') continue;

                if (str_contains($normalizedQ, $kwNorm)) {
                    $score += 3;
                    continue;
                }

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

    private function callAnthropic(array $messages, string $systemPrompt): ?string
    {
        try {
            $payload = [
                'model' => $this->model,
                'max_tokens' => 1024,
                'messages' => $messages,
                'system' => $systemPrompt,
            ];

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
        } catch (Throwable $e) {
            Log::error('Claude AI Service Exception', ['message' => $e->getMessage()]);
            return null;
        }
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
}

/**
 * Helper: vérifier existence colonne sans casser si pas de Schema importé partout.
 */
final class SchemaHas
{
    public static function column(string $table, string $column): bool
    {
        try {
            return \Illuminate\Support\Facades\Schema::hasColumn($table, $column);
        } catch (Throwable) {
            return false;
        }
    }
}
