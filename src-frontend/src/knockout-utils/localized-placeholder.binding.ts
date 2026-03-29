import ko from "knockout";

import { LocalizationRepository } from "../repository";

ko.bindingHandlers.localizedPlaceholder = {
  init: function() {
    return { 'controlsDescendantBindings': true };
  },
  update: function(element: Node, valueAccessor) {
    if (!(element instanceof HTMLInputElement)) {
      throw `Element is not an HTMLInputElement!`;
    }
    const key = ko.unwrap(valueAccessor());
    const repository = LocalizationRepository.getInstance();
    const value = repository.getOrDefault(key, `NF: ${key}`);
    element.placeholder = ko.unwrap(value);
  }
};
