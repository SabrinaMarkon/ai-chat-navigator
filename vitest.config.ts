import { defineConfig } from 'vitest/config';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [react()],
    test: {
        globals: true,
        environment: 'happy-dom',
        setupFiles: './resources/js/test/setup.ts',
        pool: 'threads',
        poolOptions: {
            threads: {
                singleThread: true,
            },
        },
    },
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
