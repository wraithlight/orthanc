import ko from "knockout";

import { GameStatisticsComponent } from "./game-statistics.component";
import { SELECTOR } from "./game-statistics.selector";
import TEMPLATE from "./game-statistics.template.html?raw";
import "./game-statistics.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameStatisticsComponent,
    template: TEMPLATE
  }
);
