CREATE TABLE LocalizationTranslationKey (
  `id` CHAR(36) NOT NULL PRIMARY KEY,
  `key` VARCHAR(255) NOT NULL UNIQUE,
  `createdAt` DATETIME(6) NOT NULL,
  `updatedAt` DATETIME(6) NOT NULL
);
