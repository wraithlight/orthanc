import { KnipConfig } from "knip";

const config: KnipConfig = {
  entry: [
    "src/index.ts",
    "src/containers/*/index.ts",
    "src/components/*/index.ts",
  ],
  ignoreDependencies: [
    "@types/knockout"
  ],
};

export default config;