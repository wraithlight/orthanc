import ko from "knockout";

import { GameMinimapComponent } from "./game-minimap.component";
import { SELECTOR } from "./game-minimap.selector";
import TEMPLATE from "./game-minimap.template.html?raw";
import "./game-minimap.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: {
      createViewModel: (params: any) => new GameMinimapComponent(params)
    },
    template: TEMPLATE
  }
);
