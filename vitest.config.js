import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';
import path from 'path';
import { vi } from 'vitest';

const LinkStub = {
    props: ['href', 'class'],
    template: '<a :href="href" :class="class"><slot /></a>',
};

export default defineConfig({
    plugins: [vue()],
    alias: {
        '@inertiajs/vue3': path.resolve(__dirname, './tests/__mocks__/@inertiajs/vue3.js'),
    },
    test: {
        globals: true,
        environment: 'jsdom',
        coverage: {
            provider: 'v8',
            reporter: ['text', 'html', 'lcov'],
        },
    },
    resolve: {
        alias: {
            '@': path.resolve(__dirname, './resources/js'),
        },
    },
});