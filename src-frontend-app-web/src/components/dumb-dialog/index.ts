import ko from "knockout";

import { OrthancDialogComponent } from "./dumb-dialog.component";
import { SELECTOR } from "./dumb-dialog.selector";
import TEMPLATE from "./dumb-dialog.template.html?raw";
import "./dumb-dialog.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: OrthancDialogComponent,
    template: TEMPLATE
  }
);
