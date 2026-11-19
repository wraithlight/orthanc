CREATE TABLE migrations (
  id CHAR(36) NOT NULL,
  version VARCHAR(5) NOT NULL,
  status ENUM(
    'MIGRATION_START',
    'MIGRATION_PROGRESS',
    'MIGRATION_FAIL',
    'MIGRATION_SUCCESS'
  ) NOT NULL,
  migrationFromVersion VARCHAR(5) NOT NULL,
  migrationToVersionRequest VARCHAR(5) NOT NULL,
  migrationToVersionActual VARCHAR(5) NOT NULL,
  migrationStartUtc DATETIME(6) NULL,
  migrationEndUtc DATETIME(6) NULL,
  migrationMessage TEXT NULL,
  PRIMARY KEY (id)
);
