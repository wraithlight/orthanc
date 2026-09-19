#!/bin/sh

INPUT_FILE=docs-api/swagger.yaml
OUTPUT_DIR=src-frontend-app-web/src/dal-generated
LANGUAGE=TYPESCRIPT

cargo run \
  --manifest-path src-tools/dalinfo-generator/Cargo.toml \
  -- \
  "$INPUT_FILE" \
  "$OUTPUT_DIR" \
  "$LANGUAGE"
