import ko from "knockout";

import { OrthancOptionsDialogComponent } from "./dialog-options.component";
import { SELECTOR } from "./dialog-options.selector";
import TEMPLATE from "./dialog-options.template.html?raw";
import "./dialog-options.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: OrthancOptionsDialogComponent,
    template: TEMPLATE
  }
);
