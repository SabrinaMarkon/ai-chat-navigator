# Import Page Tests

## Status
⚠️ **Tests written but not running due to Vitest configuration issue**

## Issue
Vitest is timing out with "Timeout starting threads runner" error.
- Tests are not being discovered
- Likely WSL-related or configuration issue
- Backend tests (Pest) work fine

## Test File
`Index.test.tsx` - Contains 10 tests for Import page component

## Tests Included
1. Renders import page title
2. Renders upload instructions
3. Renders file input
4. Renders import button
5. Shows ChatGPT export instructions
6. Shows Claude export instructions
7. Displays success message when provided
8. Displays error message when provided
9. Does not display success message when not provided
10. Does not display error message when not provided

## TODO
- Debug Vitest configuration
- Possibly related to WSL environment
- Consider alternative: Run tests in Docker
- Backend functionality is fully tested with Pest

## Running Tests (when fixed)
```bash
npm test
```
