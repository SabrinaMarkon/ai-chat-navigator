<?php

namespace App\Services\Import;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

class ChatGPTImporter implements ConversationImporterInterface
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

        return isset($firstItem['title'])
            && isset($firstItem['create_time'])
            && isset($firstItem['mapping']);
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
        return 'chatgpt';
    }

    private function importConversation(array $data, int $userId): ?int
    {
        return DB::transaction(function () use ($data, $userId) {
            $conversation = Conversation::create([
                'user_id' => $userId,
                'platform' => $this->getPlatform(),
                'title' => $data['title'] ?? 'Untitled Conversation',
                'conversation_date' => $data['create_time']
                    ? date('Y-m-d H:i:s', $data['create_time'])
                    : now(),
            ]);

            $this->importMessages($conversation->id, $data['mapping'] ?? []);

            return $conversation->id;
        });
    }

    private function importMessages(int $conversationId, array $mapping): void
    {
        $messages = [];
        $position = 0;

        foreach ($mapping as $messageData) {
            if (!isset($messageData['message'])) {
                continue;
            }

            $message = $messageData['message'];

            if (!isset($message['content']['parts']) || !isset($message['author']['role'])) {
                continue;
            }

            $content = implode("\n", $message['content']['parts']);
            $role = $message['author']['role'] === 'user' ? 'user' : 'assistant';

            $messages[] = [
                'conversation_id' => $conversationId,
                'content' => $content,
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
