import ko from "knockout";

import { GameEquipmentComponent } from "./game-equipment.component";
import { SELECTOR } from "./game-equipment.selector";
import TEMPLATE from "./game-equipment.template.html?raw";
import "./game-equipment.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameEquipmentComponent,
    template: TEMPLATE
  }
);
