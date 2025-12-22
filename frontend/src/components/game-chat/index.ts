import ko from "knockout";

import { GameChatComponent } from "./game-chat.component";
import { SELECTOR } from "./game-chat.selector";
import TEMPLATE from "./game-chat.template.html?raw";
import "./game-chat.component.css";

ko.components.register(
  SELECTOR,
  {
    viewModel: GameChatComponent,
    template: TEMPLATE
  }
);
