import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import { copyFileSync, mkdirSync, existsSync } from 'fs'

function copyZxingWasm() {
  return {
    name: 'copy-zxing-wasm',
    buildStart() {
      const src = path.resolve(__dirname, 'node_modules/zxing-wasm/dist/reader/zxing_reader.wasm')
      const destDir = path.resolve(__dirname, 'public/wasm')
      const dest = path.resolve(destDir, 'zxing_reader.wasm')

      if (!existsSync(destDir)) {
        mkdirSync(destDir, { recursive: true })
      }

      if (existsSync(src)) {
        copyFileSync(src, dest)
      }
    }
  }
}

export default defineConfig({
  plugins: [
    vue({
      template: {
        compilerOptions: {
          comments: false 
        }
      }
    }),
    copyZxingWasm(),
  ],
  
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },

  esbuild: {
    legalComments: 'none', 
    drop: ['console', 'debugger'], 
  },
})