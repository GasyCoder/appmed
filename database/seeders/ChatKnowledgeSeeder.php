<?php 

namespace Database\Seeders;

use App\Models\ChatKnowledgeItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ChatKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $path = 'chatbot/faq.json';
        if (!Storage::disk('local')->exists($path)) return;

        $data = json_decode(Storage::disk('local')->get($path), true);
        if (!is_array($data) || empty($data['responses'])) return;

        foreach ($data['responses'] as $row) {
            $keywords = $row['keywords'] ?? [];
            $response = (string)($row['response'] ?? '');

            ChatKnowledgeItem::updateOrCreate(
                [
                    'type' => 'faq',
                    'title' => is_array($keywords) ? implode(', ', array_slice($keywords, 0, 5)) : null,
                ],
                [
                    'content' => $response,
                    'tags' => $keywords,
                    'source_label' => 'FAQ EPIRC',
                    'is_active' => true,
                ]
            );
        }
    }
}