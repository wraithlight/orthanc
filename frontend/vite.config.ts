import { defineConfig } from 'vite';
import { resolve, join } from 'path';
import { readFileSync, writeFileSync } from 'fs';

export default defineConfig({
  root: '.',
  base: './',
  build: {
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: resolve(__dirname, 'index.html'),
    },
  },
  server: {
    port: 3000,
    open: true,
  },
  plugins: [
    {
      name: "update-config-json",
      apply: "build",
      writeBundle() {
        const configFilePath = resolve(__dirname, join("dist", "config.json"));
        const content = JSON.parse(readFileSync(configFilePath, "utf-8"));
        content.apiUrl = "";
        writeFileSync(configFilePath, JSON.stringify(content), "utf-8");
      }
    }
  ]
});
