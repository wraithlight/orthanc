import { describe, it, expect, beforeEach, vi } from "vitest";

import { KeyboardEventService } from "./keyboard-event.service";

describe("KeyboardEventService", () => {

  beforeEach(() => {
    // @ts-ignore access private static
    KeyboardEventService._instance = undefined;
  });

  it("should return the same instance (singleton)", () => {
    const instance1 = KeyboardEventService.getInstance();
    const instance2 = KeyboardEventService.getInstance();

    expect(instance1).toBe(instance2);
  });

  it("should subscribe and trigger callback on keydown", () => {
    const service = KeyboardEventService.getInstance();

    const callback = vi.fn();
    service.subscribe("KeyA" as any, callback);

    document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyA" }));

    expect(callback).toHaveBeenCalledTimes(1);
  });

  it("should normalize key to lowercase when subscribing", () => {
    const service = KeyboardEventService.getInstance();

    const callback = vi.fn();
    service.subscribe("KEYB" as any, callback);

    document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyB" }));

    expect(callback).toHaveBeenCalledTimes(1);
  });

  it("should not throw if no listener exists", () => {
    KeyboardEventService.getInstance();

    expect(() => {
      document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyZ" }));
    }).not.toThrow();
  });

  it("should not emit when disabled", () => {
    const service = KeyboardEventService.getInstance();

    const callback = vi.fn();
    service.subscribe("KeyC" as any, callback);

    service.disableEmit();

    document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyC" }));

    expect(callback).not.toHaveBeenCalled();
  });

  it("should emit again after enableEmit", () => {
    const service = KeyboardEventService.getInstance();

    const callback = vi.fn();
    service.subscribe("KeyD" as any, callback);

    service.disableEmit();
    service.enableEmit();

    document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyD" }));

    expect(callback).toHaveBeenCalledTimes(1);
  });

  it("should overwrite existing listener when subscribing same key", () => {
    const service = KeyboardEventService.getInstance();

    const first = vi.fn();
    const second = vi.fn();

    service.subscribe("KeyE" as any, first);
    service.subscribe("KeyE" as any, second);

    document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyE" }));

    expect(first).not.toHaveBeenCalled();
    expect(second).toHaveBeenCalledTimes(1);
  });

  it("should unsubscribe and remove listener", () => {
    const service = KeyboardEventService.getInstance();

    const callback = vi.fn();
    service.subscribe("KeyF" as any, callback);

    service.unsubscribe("KeyF" as any);

    document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyF" }));

    expect(callback).not.toHaveBeenCalled();
  });

  it("should normalize key to lowercase when unsubscribing", () => {
    const service = KeyboardEventService.getInstance();

    const callback = vi.fn();
    service.subscribe("KeyG" as any, callback);

    service.unsubscribe("KEYG" as any);

    document.dispatchEvent(new KeyboardEvent("keydown", { code: "KeyG" }));

    expect(callback).not.toHaveBeenCalled();
  });

  it("should not throw when unsubscribing non-existing key", () => {
    const service = KeyboardEventService.getInstance();

    expect(() => {
      service.unsubscribe("KeyUnknown" as any);
    }).not.toThrow();
  });

});
