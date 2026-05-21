import ko from "knockout";

import { GameEventsComponent } from "./game-events.component";
import { SELECTOR } from "./game-events.selector";
import TEMPLATE from "./game-events.template.html?raw";
import "./game-events.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: {
      createViewModel: (params: any) => new GameEventsComponent(params)
    },
    template: TEMPLATE
  }
);
