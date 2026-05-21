import type { Meta, StoryObj } from 'storybook/html';
import ko from 'knockout';

import "./index";
import { SELECTOR } from './dumb-loader.selector';

const meta = {
  title: 'Components/Core/OrthancLoader',
} satisfies Meta;

export default meta;

type Story = StoryObj;

export const Loader: Story = {
  render: () => {
    const container = document.createElement('div');

    container.innerHTML = createElement(SELECTOR);

    queueMicrotask(() => {
      const el = container.querySelector(SELECTOR);

      if (el) {
        ko.cleanNode(el);
        ko.applyBindings({}, el);
      }
    });

    return container;
  },
};

const createElement = (selector: string): string => `<${selector}></${selector}>`;