import ko from "knockout";

import { OrthancOptionsDialogGameModeComponent } from "./dialog-options-language.component";
import { SELECTOR } from "./dialog-options-language.selector";
import TEMPLATE from "./dialog-options-language.template.html?raw";
import "./dialog-options-language.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: OrthancOptionsDialogGameModeComponent,
    template: TEMPLATE
  }
);
