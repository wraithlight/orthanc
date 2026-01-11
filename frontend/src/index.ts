import { Route, Router } from "@profiscience/knockout-contrib-router";
import ko from "knockout";

import { SELECTOR as CHARACTER_CREATION_SELECTOR } from './containers/character-creation/character-creation.selector';
import { SELECTOR as GAME_SELECTOR } from './containers/game/game.selector';
import { SELECTOR as LOGIN_SELECTOR } from './containers/login/login.selector';

import { Application } from "./app.component";
import { SELECTOR } from "./app.selector";
import TEMPLATE from "./app.template.html?raw";
import { State } from "./state";

import "./normalize.scss";
import "./global.scss";

import.meta.glob('./components/**/index.ts', { eager: true });
import.meta.glob('./containers/**/index.ts', { eager: true });

ko.components.register(
  SELECTOR,
  {
    viewModel: Application,
    template: TEMPLATE
  }
);

const createRoute = (selector: string) => `/${selector}`;

Router.useRoutes([
  new Route('/', LOGIN_SELECTOR),
  new Route(createRoute(CHARACTER_CREATION_SELECTOR), CHARACTER_CREATION_SELECTOR),
  new Route(createRoute(GAME_SELECTOR), GAME_SELECTOR),
]);

async function bootstrap() {
  const response = await fetch('/config.json');
  if (!response.ok) throw new Error('Failed to load configuration');
  const config = await response.json();

  State.config(config);
  ko.applyBindings("body");
}

bootstrap();
