import ko from "knockout";

import { LoginFormComponent } from "./login.component";
import { SELECTOR } from "./login.selector";
import TEMPLATE from "./login.template.html?raw";
import "./login.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: LoginFormComponent,
    template: TEMPLATE
  }
);
