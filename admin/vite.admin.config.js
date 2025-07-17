// filepath: admin/vite.admin.config.js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  root: './src',
  plugins: [vue()],
  build: {
    outDir: './dist',
    emptyOutDir: true,
    rollupOptions: {
      input: './src/main.js'
    }
  }
})