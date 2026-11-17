CREATE TABLE LocalizationLocale (
  `id` CHAR(36) NOT NULL PRIMARY KEY,
  `isoCode` VARCHAR(5) NOT NULL UNIQUE,
  `name` VARCHAR(16) NOT NULL,
  `displayName` VARCHAR(255) NOT NULL,
  `nativeName` VARCHAR(255) NOT NULL,
  `direction` ENUM('LTR', 'RTL') NOT NULL,
  `isEnabled` BOOLEAN NOT NULL,
  `createdAt` DATETIME(6) NOT NULL,
  `updatedAt` DATETIME(6) NOT NULL
);
