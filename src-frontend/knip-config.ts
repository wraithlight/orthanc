import { KnipConfig } from "knip";

const config: KnipConfig = {
  entry: [
    "src/index.ts",
    "src/containers/*/index.ts",
    "src/components/*/index.ts",
    "src/dal/index.ts",
    "src/framework/index.ts",
  ],
  ignoreDependencies: [
    "@types/knockout"
  ],
  ignore: [
    "src/dal/enum/error-code.enum.ts"
  ]
};

export default config;