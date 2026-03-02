import ko from "knockout";

import { GameSpellsComponent } from "./game-spells.component";
import { SELECTOR } from "./game-spells.selector";
import TEMPLATE from "./game-spells.template.html?raw";
import "./game-spells.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: {
      createViewModel: (params: any) => new GameSpellsComponent(params)
    },
    template: TEMPLATE
  }
);
