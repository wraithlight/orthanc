import { existsSync, mkdirSync, readdirSync, readFileSync, writeFileSync } from "fs";
import { join } from "path";
import { EOL } from "os";

const FOLDER_TABLES = join("src", "tables");
const FOLDER_KEYS = join("src", "keys");
const FOLDER_MIGRATIONS = join("src", "migrations");

const FOLDER_OUTPUT_DATABASE = join("dist-database");
const FOLDER_OUTPUT_MIGRATIONS = join("dist-migrations");

[
  FOLDER_OUTPUT_DATABASE,
  FOLDER_OUTPUT_MIGRATIONS,
].filter(m => !existsSync(m)).forEach(m => mkdirSync(m));

const dbContent = [
  FOLDER_TABLES,
  FOLDER_KEYS,
]
  .map(m => readdirSync(m).map(o => join(m, o)))
  .flat()
  .filter(m => m.endsWith(".sql"))
  .map(m => [m, readFileSync(m, "utf-8")])
  .map(([m, o]) => `--${m}${EOL}${o}`)
  .join(EOL)
;
writeFileSync(join(FOLDER_OUTPUT_DATABASE, "database.sql"), dbContent);

const migrationContent = [
  FOLDER_MIGRATIONS,
]
  .map(m => readdirSync(m).map(o => join(m, o)))
  .flat()
  .filter(m => m.endsWith(".sql"))
  .map(m => [m, readFileSync(m, "utf-8")])
  .map(([m, o]) => `--${m}${EOL}${o}`)
  .join(EOL)
;
writeFileSync(join(FOLDER_OUTPUT_MIGRATIONS, "migrations.sql"), migrationContent);
