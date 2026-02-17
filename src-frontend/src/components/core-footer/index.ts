import ko from "knockout";

import { CoreFooterComponent } from "./core-footer.component";
import { SELECTOR } from "./core-footer.selector";
import TEMPLATE from "./core-footer.template.html?raw";
import "./core-footer.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: CoreFooterComponent,
    template: TEMPLATE
  }
);
