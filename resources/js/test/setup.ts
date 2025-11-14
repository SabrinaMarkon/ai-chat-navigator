import "@testing-library/jest-dom";

/**
 What setup.ts does:

It runs before every test and adds custom matchers to your tests.

Specifically:
import '@testing-library/jest-dom';   

This adds helpful DOM assertions like:
- .toBeInTheDocument() - element exists in DOM
- .toHaveAttribute('type', 'file') - element has attribute
- .toHaveTextContent('hello') - element contains text
- .toBeVisible() - element is visible
- .toBeDisabled() - button is disabled

Without it:
expect(element).toBe(document.body.contains(element)) // ugly

With it:
expect(element).toBeInTheDocument() // clean!

It's imported in vitest.config.ts with:
setupFiles: './resources/js/test/setup.ts'
*/
