import { describe, it, expect, vi } from "vitest";

import { unwrap } from "./unwrap.predicate";

type Predicate<T, U> = (input: T) => U;

describe("unwrap", () => {
  it("should return the result of the predicate", () => {
    const obj = { value: 5 };

    const predicate: Predicate<typeof obj, number> = (input) => input.value * 2;

    const result = unwrap(obj, predicate);

    expect(result).toBe(10);
  });

  it("should call the predicate with the provided object", () => {
    const obj = { foo: "bar" };
    const predicate = vi.fn((input) => input.foo);

    unwrap(obj, predicate);

    expect(predicate).toHaveBeenCalledTimes(1);
    expect(predicate).toHaveBeenCalledWith(obj);
  });

  it("should work with primitive values", () => {
    const predicate: Predicate<number, number> = (n) => n + 1;

    const result = unwrap(1, predicate);

    expect(result).toBe(2);
  });

  it("should work with different return types", () => {
    const obj = { id: 1 };

    const predicate: Predicate<typeof obj, string> = (input) =>
      `ID-${input.id}`;

    const result = unwrap(obj, predicate);

    expect(result).toBe("ID-1");
  });

  it("should propagate errors thrown by the predicate", () => {
    const obj = { value: 1 };

    const predicate = () => {
      throw new Error("Test error");
    };

    expect(() => unwrap(obj, predicate)).toThrow("Test error");
  });

  it("should handle null input if predicate supports it", () => {
    const predicate: Predicate<null, string> = () => "null handled";

    const result = unwrap(null, predicate);
    expect(result).toBe("null handled");
  });

  it("should handle undefined input if predicate supports it", () => {
    const predicate: Predicate<undefined, string> = () => "undefined handled";

    const result = unwrap(undefined, predicate);
    expect(result).toBe("undefined handled");
  });

  it("should work with complex transformations", () => {
    const obj = { items: [1, 2, 3] };
    const predicate: Predicate<typeof obj, number[]> = (input) => input.items.map((x) => x * 2);

    const result = unwrap(obj, predicate);
    expect(result).toEqual([2, 4, 6]);
  });
});