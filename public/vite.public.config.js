import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
  root: './public', // where index.html is
  plugins: [vue()],
  build: {
    outDir: 'dist', // relative to root
    emptyOutDir: true,
    rollupOptions: {
      output: {
        entryFileNames: 'bundle.js',
        format: 'iife', // Use IIFE to ensure compatibility with WordPress's global scope
        globals: {
          'wp': 'wp', // Map `wp` to the global window.wp object
          '@wordpress/i18n': 'wp.i18n', // Ensure wp.i18n is available
          '@wordpress/data': 'wp.data', // Ensure wp.data is available
        },
      },
    },
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
});