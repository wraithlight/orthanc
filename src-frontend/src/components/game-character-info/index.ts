import ko from "knockout";

import { GameCharacterInfoComponent } from "./game-character-info.component";
import { SELECTOR } from "./game-character-info.selector";
import TEMPLATE from "./game-character-info.template.html?raw";
import "./game-character-info.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameCharacterInfoComponent,
    template: TEMPLATE
  }
);
