<?php

use App\Services\Import\ChatGPTImporter;
use App\Services\Import\ClaudeImporter;
use App\Services\Import\ImportService;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// File Validation Tests
test('import rejects non-existent file', function () {
    $user = \App\Models\User::factory()->create();
    $service = new ImportService();

    expect(fn() => $service->import('/fake/path/file.json', $user->id))
        ->toThrow(\Exception::class, 'File does not exist');
});

test('import rejects non-json file', function () {
    $user = \App\Models\User::factory()->create();
    Storage::fake('local');
    $file = UploadedFile::fake()->create('document.txt', 100);
    $path = $file->store('imports');

    $service = new ImportService();

    expect(fn() => $service->import(Storage::path($path), $user->id))
        ->toThrow(\Exception::class, 'File must be JSON');
});

test('import rejects empty file', function () {
    $user = \App\Models\User::factory()->create();
    Storage::fake('local');
    $file = UploadedFile::fake()->create('empty.json', 0);
    $path = $file->store('imports');

    $service = new ImportService();

    expect(fn() => $service->import(Storage::path($path), $user->id))
        ->toThrow(\Exception::class, 'File is empty');
});

test('import rejects invalid json', function () {
    $user = \App\Models\User::factory()->create();
    Storage::fake('local');
    Storage::put('imports/invalid.json', 'not valid json at all');

    $service = new ImportService();

    expect(fn() => $service->import(Storage::path('imports/invalid.json'), $user->id))
        ->toThrow(\Exception::class, 'Invalid JSON');
});

// ChatGPT Format Tests
test('chatgpt importer validates required fields', function () {
    $importer = new ChatGPTImporter();
    $invalidData = ['random' => 'data'];

    expect($importer->validate($invalidData))->toBeFalse();
});

test('chatgpt importer accepts valid structure', function () {
    $importer = new ChatGPTImporter();
    $validData = [
        [
            'title' => 'Test Conversation',
            'create_time' => 1234567890,
            'mapping' => [
                'msg1' => [
                    'message' => [
                        'content' => ['parts' => ['Hello']],
                        'author' => ['role' => 'user'],
                        'create_time' => 1234567890
                    ]
                ]
            ]
        ]
    ];

    expect($importer->validate($validData))->toBeTrue();
});

test('chatgpt importer parses conversation correctly', function () {
    $user = \App\Models\User::factory()->create();

    $importer = new ChatGPTImporter();
    $data = [
        [
            'title' => 'Laravel Discussion',
            'create_time' => 1234567890,
            'mapping' => [
                'msg1' => [
                    'message' => [
                        'content' => ['parts' => ['What is Laravel?']],
                        'author' => ['role' => 'user'],
                        'create_time' => 1234567890
                    ]
                ],
                'msg2' => [
                    'message' => [
                        'content' => ['parts' => ['Laravel is a PHP framework.']],
                        'author' => ['role' => 'assistant'],
                        'create_time' => 1234567891
                    ]
                ]
            ]
        ]
    ];

    $result = $importer->import($data, $user->id);

    expect($result)->toHaveCount(1);
    expect(Conversation::count())->toBe(1);
    expect(Message::count())->toBe(2);

    $conversation = Conversation::first();
    expect($conversation->title)->toBe('Laravel Discussion');
    expect($conversation->platform)->toBe('chatgpt');
    expect($conversation->user_id)->toBe($user->id);

    $messages = $conversation->messages()->orderBy('position')->get();
    expect($messages[0]->content)->toBe('What is Laravel?');
    expect($messages[0]->role)->toBe('user');
    expect($messages[1]->content)->toBe('Laravel is a PHP framework.');
    expect($messages[1]->role)->toBe('assistant');
});

// Claude Format Tests
test('claude importer validates required fields', function () {
    $importer = new ClaudeImporter();
    $invalidData = ['random' => 'data'];

    expect($importer->validate($invalidData))->toBeFalse();
});

test('claude importer accepts valid structure', function () {
    $importer = new ClaudeImporter();
    $validData = [
        [
            'name' => 'Test Conversation',
            'created_at' => '2025-01-01T00:00:00Z',
            'chat_messages' => [
                [
                    'text' => 'Hello',
                    'sender' => 'human',
                    'created_at' => '2025-01-01T00:00:00Z'
                ]
            ]
        ]
    ];

    expect($importer->validate($validData))->toBeTrue();
});

test('claude importer parses conversation correctly', function () {
    $user = \App\Models\User::factory()->create();

    $importer = new ClaudeImporter();
    $data = [
        [
            'name' => 'React Discussion',
            'created_at' => '2025-01-01T00:00:00Z',
            'chat_messages' => [
                [
                    'text' => 'What is React?',
                    'sender' => 'human',
                    'created_at' => '2025-01-01T00:00:00Z'
                ],
                [
                    'text' => 'React is a JavaScript library.',
                    'sender' => 'assistant',
                    'created_at' => '2025-01-01T00:00:01Z'
                ]
            ]
        ]
    ];

    $result = $importer->import($data, $user->id);

    expect($result)->toHaveCount(1);
    expect(Conversation::count())->toBe(1);
    expect(Message::count())->toBe(2);

    $conversation = Conversation::first();
    expect($conversation->title)->toBe('React Discussion');
    expect($conversation->platform)->toBe('claude');
    expect($conversation->user_id)->toBe($user->id);

    $messages = $conversation->messages()->orderBy('position')->get();
    expect($messages[0]->content)->toBe('What is React?');
    expect($messages[0]->role)->toBe('user');
    expect($messages[1]->content)->toBe('React is a JavaScript library.');
    expect($messages[1]->role)->toBe('assistant');
});

// Import Service Tests
test('import service auto-detects chatgpt format', function () {
    Storage::fake('local');
    $user = \App\Models\User::factory()->create();

    $data = [
        [
            'title' => 'Test',
            'create_time' => 1234567890,
            'mapping' => [
                'msg1' => [
                    'message' => [
                        'content' => ['parts' => ['Hello']],
                        'author' => ['role' => 'user'],
                        'create_time' => 1234567890
                    ]
                ]
            ]
        ]
    ];

    Storage::put('imports/chatgpt.json', json_encode($data));

    $service = new ImportService();
    $result = $service->import(Storage::path('imports/chatgpt.json'), $user->id);

    expect($result['imported'])->toBe(1);
    expect(Conversation::where('platform', 'chatgpt')->count())->toBe(1);
});

test('import service auto-detects claude format', function () {
    Storage::fake('local');
    $user = \App\Models\User::factory()->create();

    $data = [
        [
            'name' => 'Test',
            'created_at' => '2025-01-01T00:00:00Z',
            'chat_messages' => [
                [
                    'text' => 'Hello',
                    'sender' => 'human',
                    'created_at' => '2025-01-01T00:00:00Z'
                ]
            ]
        ]
    ];

    Storage::put('imports/claude.json', json_encode($data));

    $service = new ImportService();
    $result = $service->import(Storage::path('imports/claude.json'), $user->id);

    expect($result['imported'])->toBe(1);
    expect(Conversation::where('platform', 'claude')->count())->toBe(1);
});

test('import service rejects unknown format', function () {
    Storage::fake('local');
    $user = \App\Models\User::factory()->create();

    $data = ['unknown' => 'format'];
    Storage::put('imports/unknown.json', json_encode($data));

    $service = new ImportService();

    expect(fn() => $service->import(Storage::path('imports/unknown.json'), $user->id))
        ->toThrow(\Exception::class, 'Unknown format');
});
