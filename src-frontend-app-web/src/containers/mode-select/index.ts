import ko from "knockout";

import { ModeSelectContainer } from "./mode-select.component";
import { SELECTOR } from "./mode-select.selector";
import TEMPLATE from "./mode-select.template.html?raw";

ko.components.register(
  SELECTOR,
  {
    viewModel: ModeSelectContainer,
    template: TEMPLATE
  }
);
