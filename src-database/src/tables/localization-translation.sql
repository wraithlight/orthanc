CREATE TABLE LocalizationTranslation (
  `id` CHAR(36) NOT NULL PRIMARY KEY,
  `localizationTranslationKeyId` CHAR(36) NOT NULL,
  `localizationLocaleId` CHAR(36) NOT NULL,
  `label` TEXT NOT NULL,
  `createdAt` DATETIME(6) NOT NULL,
  `updatedAt` DATETIME(6) NOT NULL
);
