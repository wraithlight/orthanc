import ko from "knockout";

import { GameScreenComponent } from "./game-screen.component";
import { SELECTOR } from "./game-screen.selector";
import TEMPLATE from "./game-screen.template.html?raw";
import "./game-screen.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameScreenComponent,
    template: TEMPLATE
  }
);
