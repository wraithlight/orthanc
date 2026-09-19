import ko, { AllBindings, BindingContext } from "knockout";
import { describe, expect, it, vi, beforeEach } from "vitest";
import { LocalizationRepository } from "../repository";
import "./localized-placeholder.binding";

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

describe("localizedPlaceholderSpecs", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("init returns descendant bindings config", () => {
    expect(ko.bindingHandlers.localizedPlaceholder).toBeDefined();
    expect(ko.bindingHandlers.localizedPlaceholder.init).toBeDefined();

    if (!ko.bindingHandlers.localizedPlaceholder.init) {
      expect(true).toBeFalsy();
      throw "`ko.bindingHandlers.localizedPlaceholder.init` is nil!";
    }

    expect(
      ko.bindingHandlers.localizedPlaceholder.init(undefined, () => undefined, MOCK_ALL_BINDINGS, undefined, MOCK_BINDING_CONTEXT)
    ).toEqual({ controlsDescendantBindings: true });
  });

  it("validates the input element", () => {
    const element = document.createElement("div");

    expect(ko.bindingHandlers.localizedPlaceholder).toBeDefined();
    expect(ko.bindingHandlers.localizedPlaceholder.update).toBeDefined();

    if (!ko.bindingHandlers.localizedPlaceholder.update) {
      expect(true).toBeFalsy();
      throw "`ko.bindingHandlers.localizedPlaceholder.update` is nil!";
    }

    const update = ko.bindingHandlers.localizedPlaceholder.update;

    expect(() => update(
      element,
      () => "key",
      MOCK_ALL_BINDINGS,
      undefined,
      MOCK_BINDING_CONTEXT
    )).toThrowError(`Element is not an HTMLInputElement!`);

  });

  it("updates localized text", () => {
    getOrDefault.mockReturnValue("hello");

    const element = document.createElement("input");

    expect(ko.bindingHandlers.localizedPlaceholder).toBeDefined();
    expect(ko.bindingHandlers.localizedPlaceholder.update).toBeDefined();

    if (!ko.bindingHandlers.localizedPlaceholder.update) {
      expect(true).toBeFalsy();
      throw "`ko.bindingHandlers.localizedPlaceholder.update` is nil!";
    }

    ko.bindingHandlers.localizedPlaceholder.update(
      element,
      () => "key",
      MOCK_ALL_BINDINGS,
      undefined,
      MOCK_BINDING_CONTEXT
    );

    expect(LocalizationRepository.getInstance).toHaveBeenCalled();
    expect(getOrDefault).toHaveBeenCalledWith("key", "NF: key");
  });
});
