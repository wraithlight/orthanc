import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";

vi.mock("../../../facade", () => ({
  deepmerge: vi.fn(),
}));

import { deepmerge as deepmergeFacade } from "../../../facade";
import { deepmerge } from "./deepmerge";

const MOCK_LEFT = { propertyLeft: "value" };
const MOCK_RIGHT = { propertyRight: "value" };

describe("deepmerge re-export", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("should call facade deepmerge", () => {
    (deepmergeFacade as any).mockReturnValue({});

    deepmerge(MOCK_LEFT, MOCK_RIGHT);

    expect(deepmergeFacade).toHaveBeenCalled();
    expect(deepmergeFacade).toHaveBeenCalledTimes(1);
    expect(deepmergeFacade).toHaveBeenCalledWith(MOCK_LEFT, MOCK_RIGHT);
  });

  it("should return the value from facade deepmerge", () => {
    const merged = { a: 1, b: 2 };

    (deepmergeFacade as any).mockReturnValue(merged);

    const result = deepmerge({ a: 1 }, { b: 2 });

    expect(result).toBe(merged);
  });
});