# Test Fixtures

This directory contains sample JSON files for testing the import functionality.

## Files

### chatgpt-sample.json
Sample ChatGPT conversation export with 2 conversations:
- "Laravel Testing Best Practices" - 4 messages
- "React 19 New Features" - 2 messages

### claude-sample.json
Sample Claude conversation export with 2 conversations:
- "TypeScript Best Practices" - 4 messages
- "Tailwind CSS 4 Changes" - 2 messages

## Testing the Import Feature

### Via Browser
1. Start the server: `php artisan serve`
2. Navigate to `http://localhost:8000/import`
3. Upload one of these files
4. See the import results

### Via Tests
These fixtures are used in automated tests:
```bash
php artisan test --filter ConversationImportTest
```

## Creating Your Own Test Files

To test with your actual ChatGPT/Claude data:

### ChatGPT
1. Go to ChatGPT Settings → Data Controls → Export Data
2. Receive email with download link
3. Extract the ZIP file
4. Upload `conversations.json`

### Claude
1. Go to Claude Settings → Export Data
2. Download the JSON file
3. Upload it

## File Formats

### ChatGPT Format
```json
[
  {
    "title": "Conversation Title",
    "create_time": 1234567890,
    "mapping": {
      "msg_id": {
        "message": {
          "content": {"parts": ["Message text"]},
          "author": {"role": "user"},
          "create_time": 1234567890
        }
      }
    }
  }
]
```

### Claude Format
```json
[
  {
    "name": "Conversation Title",
    "created_at": "2024-01-01T00:00:00Z",
    "chat_messages": [
      {
        "text": "Message text",
        "sender": "human",
        "created_at": "2024-01-01T00:00:00Z"
      }
    ]
  }
]
```
