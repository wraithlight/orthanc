#!/bin/sh

INPUT_FILE=docs-api/swagger.yaml
OUTPUT_DIR=src-server/src/app/dal-generated
LANGUAGE=PHP

cargo run \
  --manifest-path src-tools/dalinfo-generator/Cargo.toml \
  -- \
  "$INPUT_FILE" \
  "$OUTPUT_DIR" \
  "$LANGUAGE"
