import ko from "knockout";

import { CommonLanguageSelectorComponent } from "./common-language-selector.component";
import { SELECTOR } from "./common-language-selector.selector";
import TEMPLATE from "./common-language-selector.template.html?raw";
import "./common-language-selector.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: CommonLanguageSelectorComponent,
    template: TEMPLATE
  }
);
