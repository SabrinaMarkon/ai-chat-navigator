<?php

namespace App\Services\Import;

use Exception;

class ImportService
{
    private array $importers;

    public function __construct()
    {
        $this->importers = [
            new ChatGPTImporter(),
            new ClaudeImporter(),
        ];
    }

    /**
     * Import conversations from a JSON file
     *
     * @throws Exception
     */
    public function import(string $filePath, int $userId): array
    {
        // Validate file exists
        if (!file_exists($filePath)) {
            throw new Exception('File does not exist');
        }

        // Validate file extension
        if (pathinfo($filePath, PATHINFO_EXTENSION) !== 'json') {
            throw new Exception('File must be JSON');
        }

        // Validate file is not empty
        if (filesize($filePath) === 0) {
            throw new Exception('File is empty');
        }

        // Read and decode JSON
        $contents = file_get_contents($filePath);
        $data = json_decode($contents, true);

        // Validate JSON is valid
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON');
        }

        // Detect format and import
        $importer = $this->detectFormat($data);

        if (!$importer) {
            throw new Exception('Unknown format');
        }

        $conversationIds = $importer->import($data, $userId);

        return [
            'imported' => count($conversationIds),
            'conversation_ids' => $conversationIds,
            'platform' => $importer->getPlatform(),
        ];
    }

    private function detectFormat(array $data): ?ConversationImporterInterface
    {
        foreach ($this->importers as $importer) {
            if ($importer->validate($data)) {
                return $importer;
            }
        }

        return null;
    }
}
