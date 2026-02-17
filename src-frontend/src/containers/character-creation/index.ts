import ko from "knockout";

import { CharacterCreationContainer } from "./character-creation.component";
import { SELECTOR } from "./character-creation.selector";
import TEMPLATE from "./character-creation.template.html?raw";
import "./character-creation.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: CharacterCreationContainer,
    template: TEMPLATE
  }
);
