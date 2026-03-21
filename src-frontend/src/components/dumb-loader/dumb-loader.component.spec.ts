import { describe, it, expect, beforeEach } from "vitest";
import * as ko from "knockout";

import { OrthancLoaderComponent } from "./dumb-loader.component";
import { SELECTOR } from "./dumb-loader.selector";
import TEMPLATE from "./dumb-loader.template.html?raw";

ko.components.register(SELECTOR, {
  viewModel: OrthancLoaderComponent,
  template: TEMPLATE
});

describe("OrthancLoaderComponent", () => {
  let container: HTMLElement;

  beforeEach(() => {
    container = document.createElement("div");
    container.innerHTML = `<${SELECTOR}></${SELECTOR}>`;
    document.body.appendChild(container);

    ko.applyBindings({}, container);
  });

  it("should render the component", () => {
    const loader = container.querySelector(".orthanc-loader__container");
    expect(loader).not.toBeNull();
  });

  it("should render opening and closing brackets", () => {
    const spans = container.querySelectorAll("span");

    expect(spans[0].textContent).toBe("[");
    expect(spans[spans.length - 1].textContent).toBe("]");
  });

  it("should render 8 asterisks", () => {
    const asterisks = container.querySelectorAll(".asterisk");
    expect(asterisks.length).toBe(8);

    asterisks.forEach(el => {
      expect(el.textContent).toBe("*");
    });
  });

  it("should have correct total number of spans", () => {
    const spans = container.querySelectorAll("span");
    expect(spans.length).toBe(10);
  });

  it("should apply correct container class", () => {
    const div = container.querySelector("div");
    expect(div?.classList.contains("orthanc-loader__container")).toBe(true);
  });
});