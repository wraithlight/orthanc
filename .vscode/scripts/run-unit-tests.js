#!/usr/bin/env node

const { spawn } = require("child_process");
const { sep, resolve } = require("path");

const file = process.env.VSCODE_FILE;
const workspace = process.env.WORKSPACE

if (!file) {
  console.error("No file provided");
  process.exit(1);
}

const normalized = file.split(sep).join("/");

function run(cmd, args, cwd) {
  const child = spawn(cmd, args, {
    stdio: "inherit",
    cwd
  });

  child.on("exit", code => {
    process.exit(code)
  });
}

if (normalized.endsWith(".spec.ts")) {
  console.log("🧪 Running Vitest\n");

  run(
    "node",
    ["node_modules/vitest/vitest.mjs", "run", file],
    resolve(workspace, "frontend")
  );

} else if (normalized.endsWith(".spec.php")) {
  console.log("🐘 Running PHPUnit\n");

  run(
    "vendor/bin/phpunit",
    ["--colors=always", "--testdox", file],
    resolve(workspace, "server")
  );

} else {
  console.error("Open a *.spec.ts or *.spec.php file");
  process.exit(1);
}
