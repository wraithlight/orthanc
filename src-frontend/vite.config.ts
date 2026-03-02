import { defineConfig } from "vite";
import { resolve, join } from "path";
import { readFileSync, writeFileSync } from "fs";
import { execSync } from "child_process";

export default defineConfig({
  root: ".",
  base: "./",
  build: {
    outDir: "dist",
    emptyOutDir: true,
    rollupOptions: {
      input: resolve(__dirname, "index.html"),
    },
  },
  server: {
    port: 3000,
    open: true,
  },
  plugins: [
    {
      name: "use-env-serve",
      apply: "serve",
      configResolved() {
        const environmentsPath = join(__dirname, "src", "environment");
        const developmentConfig = "environment.local-dev.ts";
        const defaultConfig = "environment.ts";

        const devContent = readFileSync(join(environmentsPath, developmentConfig), "utf-8");
        writeFileSync(join(environmentsPath, defaultConfig), devContent, "utf-8");
      }
    },
    {
      name: "use-env-build",
      apply: "build",
      configResolved() {
        const targetEnv = process.argv[4] || "local-development";

        const environmentsPath = join(__dirname, "src", "environment");
        const developmentConfig = `environment.${targetEnv}.ts`;
        const defaultConfig = "environment.ts";

        const devContent = readFileSync(join(environmentsPath, developmentConfig), "utf-8");
        writeFileSync(join(environmentsPath, defaultConfig), devContent, "utf-8");
      }
    }
  ]
});
