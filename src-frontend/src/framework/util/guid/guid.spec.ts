import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";

import { newGuid } from "./guid";

describe("newGuid", () => {
  const mockUuid = "123e4567-e89b-12d3-a456-426614174000";

  beforeEach(() => {
    vi.spyOn(globalThis.crypto, "randomUUID").mockReturnValue(mockUuid);
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("should call crypto.randomUUID", () => {
    newGuid();

    expect(crypto.randomUUID).toHaveBeenCalled();
    expect(crypto.randomUUID).toHaveBeenCalledTimes(1);
    expect(crypto.randomUUID).toHaveBeenCalledWith();
  });

  it("should return the generated UUID", () => {
    const result = newGuid();

    expect(result).toBe(mockUuid);
  });
});