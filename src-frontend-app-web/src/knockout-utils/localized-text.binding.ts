import ko from "knockout";

import { LocalizationRepository } from "../repository";

ko.bindingHandlers.localizedText = {
  init: function() {
    return { 'controlsDescendantBindings': true };
  },
  update: function(element, valueAccessor) {
    const key = ko.unwrap(valueAccessor());
    const repository = LocalizationRepository.getInstance();
    const value = repository.getOrDefault(key, `NF: ${key}`);
    ko.utils.setTextContent(element, value);
  }
};
