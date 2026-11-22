ALTER TABLE `LocalizationTranslation`
ADD CONSTRAINT `FK_LocalizationTranslation_LocalizationTranslationKey` FOREIGN KEY (`localizationTranslationKeyId`) REFERENCES `LocalizationTranslationKey`(`id`);