import ko from "knockout";

import { GameActionsComponent } from "./game-actions.component";
import { SELECTOR } from "./game-actions.selector";
import TEMPLATE from "./game-actions.template.html?raw";
import "./game-actions.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameActionsComponent,
    template: TEMPLATE
  }
);
