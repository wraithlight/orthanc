vi.mock("deepmerge-ts", () => ({ deepmerge: vi.fn() }));

import { vi, describe, it, expect } from "vitest";

import { deepmerge } from "./deepmerge";
import { deepmerge as deepmergeSpy } from "deepmerge-ts";

describe("isNilSpecs", () => {

  const MOCK_LEFT = { a: [1], b: 2 };
  const MOCK_RIGHT = { a: [2], c: 3 }

  it("must call isNilCore", () => {
    deepmerge<any>(MOCK_LEFT, MOCK_RIGHT);
    expect(deepmergeSpy).toHaveBeenCalled();
    expect(deepmergeSpy).toHaveBeenCalledTimes(1);
    expect(deepmergeSpy).toHaveBeenCalledWith(MOCK_LEFT, MOCK_RIGHT);
  });
});
