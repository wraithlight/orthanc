import { KnipConfig } from "knip";

const config: KnipConfig = {
  entry: [
    "src/**/index.ts",
    "src/environment/environment.*.ts"
  ],
  ignoreDependencies: [
    "@types/knockout"
  ],
  ignore: [
    "src/dal/enum/error-code.enum.ts",  // TODO: Remove once used.
    "src/domain/game-mode.enum.ts",     // TODO: Remove once used.
  ]
};

export default config;