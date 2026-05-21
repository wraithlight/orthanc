import ko from "knockout";

import { GameCharasteristics } from "./game-charasteristics.component";
import { SELECTOR } from "./game-charasteristics.selector";
import TEMPLATE from "./game-charasteristics.template.html?raw";
import "./game-charasteristics.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameCharasteristics,
    template: TEMPLATE
  }
);
