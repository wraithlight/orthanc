import ko from "knockout";

import { OrthancOptionsDialogLanguageComponent } from "./dialog-options-game-mode.component";
import { SELECTOR } from "./dialog-options-game-mode.selector";
import TEMPLATE from "./dialog-options-game-mode.template.html?raw";
import "./dialog-options-game-mode.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: OrthancOptionsDialogLanguageComponent,
    template: TEMPLATE
  }
);