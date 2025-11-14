<?php

namespace App\Services\Import;

interface ConversationImporterInterface
{
    /**
     * Validate if the data structure matches this importer's format
     */
    public function validate(array $data): bool;

    /**
     * Import conversations from the data
     *
     * @return array Array of created conversation IDs
     */
    public function import(array $data, int $userId): array;

    /**
     * Get the platform name for this importer
     */
    public function getPlatform(): string;
}
