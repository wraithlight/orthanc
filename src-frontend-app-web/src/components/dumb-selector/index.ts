import ko from "knockout";

import { OrthancSelectorComponent } from "./dumb-selector.component";
import { SELECTOR } from "./dumb-selector.selector";
import TEMPLATE from "./dumb-selector.template.html?raw";
import "./dumb-selector.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: OrthancSelectorComponent,
    template: TEMPLATE
  }
);
