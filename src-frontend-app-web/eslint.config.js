import tseslint from "typescript-eslint";

const INITIAL_COMMON_RULES = {
  "@typescript-eslint/ban-ts-comment": "warn",
  "@typescript-eslint/no-empty-object-type": "warn",
  "@typescript-eslint/no-explicit-any": "warn",
  "@typescript-eslint/no-non-null-assertion": "warn",
  "@typescript-eslint/no-unused-expressions": "warn",
  "@typescript-eslint/no-unused-vars": "warn",
  "quotes": ["warn", "double", { "allowTemplateLiterals": true }],
  "no-magic-numbers": ["warn", { "ignoreArrayIndexes": true, "ignore": [0,1] }],
  "indent": ["warn", 2, { "SwitchCase": 1 }],
  "max-len": ["warn", { "code": 80, "ignoreUrls": true, "ignoreStrings": true, "ignoreTemplateLiterals": true }],
  "no-undefined": "warn",
  "no-var": "warn",
  "no-eq-null": "warn",
  "no-multiple-empty-lines": "warn",
  "no-trailing-spaces": "warn",
  "no-console": "warn",
  "no-alert": "warn",
};

const COMMON_RULES_WARN = {
  ...INITIAL_COMMON_RULES,
};

const COMMON_RULES_ERROR = {

};

export default [
  ...tseslint.configs.recommended,
  {
    ignores: ["dist/**", "node_modules/**"],
  },
  {
    files: ["**/*.ts"],
    rules: {
      ...COMMON_RULES_WARN,
      ...COMMON_RULES_ERROR,
    },
  },
  {
    files: ["**/*.spec.ts"],
    languageOptions: {
      globals: {
        describe: "readonly",
        test: "readonly",
        expect: "readonly",
        beforeEach: "readonly",
        afterEach: "readonly",
      },
    },
    rules: {
      ...COMMON_RULES_WARN,
      ...COMMON_RULES_ERROR,
    },
  },
];