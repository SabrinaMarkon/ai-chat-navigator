<?php

use App\Models\User;
use App\Models\Conversation;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// Authentication Tests
test('import page requires authentication', function () {
    $response = $this->get('/import');
    $response->assertRedirect('/login');
});

test('import upload requires authentication', function () {
    $response = $this->post('/import');
    $response->assertRedirect('/login');
});

// Import Page Tests
test('authenticated user can view import page', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/import');

    $response->assertStatus(200);
})->skip('Requires Vite build - test manually or in browser');

// Upload Validation Tests
test('upload requires file', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/import', []);

    $response->assertSessionHasErrors('file');
});

test('upload requires json file', function () {
    $user = User::factory()->create();
    Storage::fake('local');

    $file = UploadedFile::fake()->create('document.txt', 100);

    $response = $this->actingAs($user)->post('/import', [
        'file' => $file,
    ]);

    $response->assertSessionHasErrors('file');
});

test('upload rejects files larger than 10MB', function () {
    $user = User::factory()->create();
    Storage::fake('local');

    $file = UploadedFile::fake()->create('huge.json', 11000);

    $response = $this->actingAs($user)->post('/import', [
        'file' => $file,
    ]);

    $response->assertSessionHasErrors('file');
});

// Successful Import Tests
test('successfully imports chatgpt export', function () {
    $user = User::factory()->create();
    Storage::fake('local');

    $data = [
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

    $file = UploadedFile::fake()->createWithContent(
        'conversations.json',
        json_encode($data)
    );

    $response = $this->actingAs($user)->post('/import', [
        'file' => $file,
    ]);

    $response->assertRedirect('/import');
    $response->assertSessionHas('success');

    expect(Conversation::count())->toBe(1);
    expect(Conversation::first()->user_id)->toBe($user->id);
});

test('successfully imports claude export', function () {
    $user = User::factory()->create();
    Storage::fake('local');

    $data = [
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

    $file = UploadedFile::fake()->createWithContent(
        'conversations.json',
        json_encode($data)
    );

    $response = $this->actingAs($user)->post('/import', [
        'file' => $file,
    ]);

    $response->assertRedirect('/import');
    $response->assertSessionHas('success');

    expect(Conversation::count())->toBe(1);
    expect(Conversation::first()->platform)->toBe('claude');
});

// Error Handling Tests
test('handles invalid json gracefully', function () {
    $user = User::factory()->create();
    Storage::fake('local');

    $file = UploadedFile::fake()->createWithContent(
        'invalid.json',
        'not valid json'
    );

    $response = $this->actingAs($user)->post('/import', [
        'file' => $file,
    ]);

    $response->assertRedirect('/import');
    $response->assertSessionHas('error');

    expect(Conversation::count())->toBe(0);
});

test('handles unknown format gracefully', function () {
    $user = User::factory()->create();
    Storage::fake('local');

    $file = UploadedFile::fake()->createWithContent(
        'unknown.json',
        json_encode(['unknown' => 'format'])
    );

    $response = $this->actingAs($user)->post('/import', [
        'file' => $file,
    ]);

    $response->assertRedirect('/import');
    $response->assertSessionHas('error');

    expect(Conversation::count())->toBe(0);
});
