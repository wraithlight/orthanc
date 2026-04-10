import ko from "knockout";

import { OrthancLoaderComponent } from "./dumb-loader.component";
import { SELECTOR } from "./dumb-loader.selector";
import TEMPLATE from "./dumb-loader.template.html?raw";
import "./dumb-loader.component.scss";

ko.components.register(
  SELECTOR,
  {
    viewModel: OrthancLoaderComponent,
    template: TEMPLATE
  }
);
