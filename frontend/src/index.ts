import ko from "knockout";

import { Application } from "./app.component";
import { SELECTOR } from "./app.selector";
import TEMPLATE from "./app.template.html?raw";

import "./normalize.css";
import "./global.css";
import { setConfig } from "./state";

import.meta.glob('./components/**/index.ts', { eager: true });
import.meta.glob('./containers/**/index.ts', { eager: true });

ko.components.register(
  SELECTOR,
  {
    viewModel: Application,
    template: TEMPLATE
  }
);

async function bootstrap() {
  const response = await fetch('/config.json');
  if (!response.ok) throw new Error('Failed to load configuration');
  const config = await response.json();

  setConfig(config);
  ko.applyBindings("body");
}

bootstrap();
