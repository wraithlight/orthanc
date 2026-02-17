import ko from "knockout";

import { GameContainer } from "./game.component";
import { SELECTOR } from "./game.selector";
import TEMPLATE from "./game.template.html?raw";
import "./game.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameContainer,
    template: TEMPLATE
  }
);
