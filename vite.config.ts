import path from 'node:path'
import fs from 'node:fs'
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import tailwindcss from '@tailwindcss/vite'

function copy404Plugin() {
  return {
    name: 'copy-404',
    closeBundle() {
      const outDir = path.resolve(import.meta.dirname, 'public_html')
      const index = path.join(outDir, 'index.html')
      const dest = path.join(outDir, '404.html')
      if (fs.existsSync(index)) {
        fs.copyFileSync(index, dest)
        console.log('✓ 404.html generated from index.html for Hostinger SPA fallback')
      }
    },
  } as const
}

// https://vite.dev/config/
export default defineConfig({
  plugins: [react(), tailwindcss(), copy404Plugin()],
  resolve: {
    alias: {
      '@': path.resolve(import.meta.dirname, './src'),
    },
  },
  server: {
    host: true,
    port: 5173,
    // Polling keeps HMR working inside Docker bind mounts on Windows/macOS
    watch: {
      usePolling: process.env.CHOKIDAR_USEPOLLING === 'true',
    },
  },
  optimizeDeps: {
    // Fixes "Class extends value undefined" from framer-motion/motion v13:
    // motion-dom is a dual CJS/ESM package that Vite's dep optimizer bundles
    // incorrectly. Load these as native ESM instead of pre-bundling.
    exclude: ['framer-motion', 'motion', 'motion-dom', 'motion-utils'],
  },
  build: {
    // Hostinger shared hosting: build output goes directly into public_html
    outDir: 'public_html',
    emptyOutDir: true,
  },
})