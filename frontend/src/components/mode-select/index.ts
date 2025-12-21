import ko from "knockout";

import { ModeSelectComponent } from "./mode-select.component";
import { SELECTOR } from "./mode-select.selector";
import TEMPLATE from "./mode-select.template.html?raw";
import "./mode-select.component.css";

ko.components.register(
  SELECTOR,
  {
    viewModel: ModeSelectComponent,
    template: TEMPLATE
  }
);
