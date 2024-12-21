import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  transpileDependencies: true,
  css: {
    preprocessorOptions: {
      scss: {
        additionalData: `@use "resources/styles/styles.scss" as *;`
      }
    }
  },
  plugins: [
    laravel({
      input: ['resources/styles/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue(),
  ],
  resolve: {
    alias: {
      vue: 'vue/dist/vue.esm-bundler.js',
    }
  }
})
