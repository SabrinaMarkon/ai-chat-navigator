<?php

use MeiliSearch\Client;
use MeiliSearch\Exceptions\CommunicationException;

test('meilisearch is accessible and healthy', function () {
    $client = new Client('http://localhost:7700');

    try {
        $health = $client->health();
        expect($health['status'])->toBe('available');
    } catch (CommunicationException $e) {
        $this->markTestSkipped('Meilisearch is not running on localhost:7700');
    }
})->group('integration');
