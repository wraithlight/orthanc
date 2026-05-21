import { describe, it, expect } from "vitest";

import { hasProperty } from "./has-property.predicate";

describe("hasPropertyPredicateSpecs", () => {
  it("returns false for non-objects", () => {
    expect(hasProperty(42, "a")).toBe(false);
    expect(hasProperty("str", "length")).toBe(false);
    expect(hasProperty(undefined, "a")).toBe(false);
  });

  it("returns false for null", () => {
    expect(hasProperty(null, "a")).toBe(false);
  });

  it("returns false when property does not exist", () => {
    expect(hasProperty({}, "a")).toBe(false);
  });

  it("returns true when property exists", () => {
    expect(hasProperty({ a: 1 }, "a")).toBe(true);
  });

  it("works with symbol keys", () => {
    const key = Symbol();
    const obj = { [key]: 123 };
    expect(hasProperty(obj, key)).toBe(true);
  });
});
