import ko from "knockout";

import { LoginFormComponent } from "./login.component";
import { SELECTOR } from "./login.selector";
import TEMPLATE from "./login.template.html?raw";
import "./login.component.css";

ko.components.register(
  SELECTOR,
  {
    viewModel: LoginFormComponent,
    template: TEMPLATE
  }
);
