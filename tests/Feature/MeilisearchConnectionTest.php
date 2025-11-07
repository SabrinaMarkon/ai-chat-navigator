<?php

use MeiliSearch\Client;

test('meilisearch is accessible and healthy', function () {
    $client = new Client('http://localhost:7700');
    $health = $client->health();

    expect($health['status'])->toBe('available');
});
