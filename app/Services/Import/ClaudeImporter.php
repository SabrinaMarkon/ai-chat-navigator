<?php

namespace App\Services\Import;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ClaudeImporter implements ConversationImporterInterface
{
    public function validate(array $data): bool
    {
        if (!is_array($data) || empty($data)) {
            return false;
        }

        $firstItem = is_array($data) ? reset($data) : null;

        if (!$firstItem) {
            return false;
        }

        return isset($firstItem['name'])
            && isset($firstItem['created_at'])
            && isset($firstItem['chat_messages']);
    }

    public function import(array $data, int $userId): array
    {
        $conversationIds = [];

        foreach ($data as $conversationData) {
            $conversationId = $this->importConversation($conversationData, $userId);
            if ($conversationId) {
                $conversationIds[] = $conversationId;
            }
        }

        return $conversationIds;
    }

    public function getPlatform(): string
    {
        return 'claude';
    }

    private function importConversation(array $data, int $userId): ?int
    {
        return DB::transaction(function () use ($data, $userId) {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'platform' => $this->getPlatform(),
                'title' => $data['name'] ?? 'Untitled Conversation',
                'conversation_date' => $data['created_at'] ?? now(),
            ]);

            $this->importMessages($conversation->id, $data['chat_messages'] ?? []);

            return $conversation->id;
        });
    }

    private function importMessages(int $conversationId, array $chatMessages): void
    {
        $messages = [];
        $position = 0;

        foreach ($chatMessages as $messageData) {
            if (!isset($messageData['text']) || !isset($messageData['sender'])) {
                continue;
            }

            $role = $messageData['sender'] === 'human' ? 'user' : 'assistant';

            $messages[] = [
                'conversation_id' => $conversationId,
                'content' => $messageData['text'],
                'role' => $role,
                'position' => $position++,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($messages)) {
            Message::insert($messages);
        }
    }
}
