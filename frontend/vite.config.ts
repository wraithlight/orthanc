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
      name: "update-config-json",
      apply: "build",
      writeBundle() {
        const args = process.argv[4];
        const environment = args
          ? args.split("=")[1]
          : "local-development"
        ;

        const configFilePath = resolve(__dirname, join("dist", "config.json"));
        const gitHash = execSync("git rev-parse --short HEAD").toString().trim();
        const configContent = JSON.parse(readFileSync(configFilePath, "utf-8"));
        configContent.apiUrl = "";
        configContent.version += `-${gitHash}`;
        configContent.environment = environment;
        writeFileSync(configFilePath, JSON.stringify(configContent), "utf-8");
      }
    }
  ]
});
