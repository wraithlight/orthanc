import ko from "knockout";

import { LoginContainer } from "./login.component";
import { SELECTOR } from "./login.selector";
import TEMPLATE from "./login.template.html?raw";

ko.components.register(
  SELECTOR,
  {
    viewModel: LoginContainer,
    template: TEMPLATE
  }
);
