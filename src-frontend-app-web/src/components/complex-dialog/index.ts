import ko from "knockout";

import { OrthancDialogsComponent } from "./complex-dialog.component";
import { SELECTOR } from "./complex-dialog.selector";
import TEMPLATE from "./complex-dialog.template.html?raw";

ko.components.register(
  SELECTOR,
  {
    viewModel: OrthancDialogsComponent,
    template: TEMPLATE
  }
);
