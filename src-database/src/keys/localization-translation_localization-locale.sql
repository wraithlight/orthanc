ALTER TABLE `LocalizationTranslation`
ADD CONSTRAINT `FK_LocalizationTranslation_LocalizationLocale` FOREIGN KEY (`localizationLocaleId`) REFERENCES `LocalizationLocale`(`id`);
