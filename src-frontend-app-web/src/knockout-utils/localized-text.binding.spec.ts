import ko, { AllBindings, BindingContext } from "knockout";
import { describe, expect, it, vi, beforeEach } from "vitest";
import { LocalizationRepository } from "../repository";
import "./localized-text.binding";

const getOrDefault = vi.fn();

vi.mock("../repository", () => ({
  LocalizationRepository: {
    getInstance: vi.fn(() => ({
      getOrDefault,
    })),
  },
}));

const MOCK_ALL_BINDINGS = Object.assign(
  vi.fn(),
  {
    get: vi.fn(),
    has: vi.fn(),
  },
) as AllBindings;

const MOCK_BINDING_CONTEXT = {} as BindingContext;

describe("localizedTextSpecs", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("init returns descendant bindings config", () => {
    expect(ko.bindingHandlers.localizedText).toBeDefined();
    expect(ko.bindingHandlers.localizedText.init).toBeDefined();

    if (!ko.bindingHandlers.localizedText.init) {
      expect(true).toBeFalsy();
      throw "`ko.bindingHandlers.localizedText.init` is nil!";
    }

    expect(
      ko.bindingHandlers.localizedText.init(undefined, () => undefined, MOCK_ALL_BINDINGS, undefined, MOCK_BINDING_CONTEXT)
    ).toEqual({ controlsDescendantBindings: true });
  });

  it("updates localized text", () => {
    getOrDefault.mockReturnValue("hello");

    const element = document.createElement("div");
    const setTextContent = vi.spyOn(ko.utils, "setTextContent");

    expect(ko.bindingHandlers.localizedText).toBeDefined();
    expect(ko.bindingHandlers.localizedText.update).toBeDefined();

    if (!ko.bindingHandlers.localizedText.update) {
      expect(true).toBeFalsy();
      throw "`ko.bindingHandlers.localizedText.update` is nil!";
    }

    ko.bindingHandlers.localizedText.update(
      element,
      () => "key",
      MOCK_ALL_BINDINGS,
      undefined,
      MOCK_BINDING_CONTEXT
    );

    expect(LocalizationRepository.getInstance).toHaveBeenCalled();
    expect(getOrDefault).toHaveBeenCalledWith("key", "NF: key");
    expect(setTextContent).toHaveBeenCalledWith(element, "hello");
  });
});
