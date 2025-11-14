# AI Chat Navigator

Finding specific information in long AI conversations is frustrating. You remember getting a useful answer three weeks ago but can't remember which conversation it was in or where in that 50-message thread it appeared.

This application lets you import your ChatGPT or Claude conversation history, search across all of it, and bookmark specific messages for quick access later. Navigate between bookmarks with keyboard shortcuts, filter by platform or date, and organize conversations with tags.

## The Problem

AI assistants are verbose. They write walls of text, provide excessive context, and turn straightforward answers into repetitive essays. When you need to reference something from a previous conversation, you're forced to scroll through pages of that verbosity to find the one message that actually mattered.

Existing chat interfaces have no way to mark or navigate to important moments within a conversation. This tool adds that missing functionality.

## What It Does

Import your ChatGPT or Claude conversation history, search across everything you've ever discussed with an AI, and bookmark the messages that matter. Filter by platform, date range, or tags. Jump between bookmarks with keyboard shortcuts.

The browser extension (coming soon) will inject bookmark icons directly into ChatGPT and Claude's UI, so you can mark messages as you go.

## Tech Stack

- **Backend:** Laravel 12, PHP 8.2
- **Frontend:** React 19, TypeScript, Inertia.js
- **Styling:** Tailwind CSS 4
- **Search:** Meilisearch with Laravel Scout
- **Testing:** Pest (backend), Vitest (frontend)
- **Database:** SQLite (dev), PostgreSQL (production)

## Features

**Current:**

- Import ChatGPT and Claude conversation exports (JSON)
- Auto-detect format and parse conversations
- User authentication with Laravel Breeze

**In Progress:**

- Full-text search with advanced filtering
- Bookmark management
- Tag organization
- Browser extension for live bookmarking

**Planned:**

- Next/previous bookmark navigation
- Keyboard shortcuts
- Support for Gemini and Perplexity
- Export bookmarked snippets

## Testing

The project follows TDD with comprehensive test coverage:

- 46 backend tests (Pest)
- 10 frontend tests (Vitest)
- Integration tests for external services

Run tests:

```bash
php artisan test                    # Backend
npm test -- --run                   # Frontend
php artisan test --group=integration # Only integration tests
```

## Installation

```bash
# Clone and install dependencies
composer install
npm install --legacy-peer-deps

# Set up environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Start Meilisearch (Docker)
docker run -d -p 7700:7700 \
  -v $(pwd)/meili_data:/meili_data \
  getmeili/meilisearch:latest

# Build frontend assets
npm run build

# Start development server
php artisan serve
```

Visit `http://localhost:8000` and register an account to start importing conversations.

## Project Structure

```
app/
├── Http/Controllers/ImportController.php  # File upload handling
├── Services/Import/                       # Import parsers (adapter pattern)
│   ├── ChatGPTImporter.php
│   ├── ClaudeImporter.php
│   └── ImportService.php
└── Models/                                # Eloquent models
    ├── Conversation.php
    ├── Message.php
    ├── Bookmark.php
    └── Tag.php

resources/js/
└── Pages/
    └── Import/Index.tsx                   # Import UI

tests/
├── Feature/                               # HTTP and integration tests
├── Unit/                                  # Business logic tests
└── fixtures/                              # Sample JSON exports
```

## Why This Project

This solves a real problem I encountered: hundreds of AI conversations with valuable information scattered across them, and no efficient way to retrieve it. Building this demonstrates modern full-stack development practices while creating something genuinely useful.
