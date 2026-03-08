import { defineConfig } from "vitest/config";

export default defineConfig({
  test: {
    environment: "jsdom",
    coverage: {
      provider: "v8",
      reporter: ["text", "html"],
      thresholds: {
        // statements: 85,          // TODO
        // branches: 85,            // TODO
        // functions: 85,           // TODO
        // lines: 85                // TODO
      },
      include: ["src/**/*.ts"],
      exclude: [
        "src/**/*.selector.ts",
        "src/**/*/index.ts",
        "src/**/*.model.ts",
        "src/**/*.type.ts",
        "src/environment/**/*.ts",
      ],
    }
  }
});
