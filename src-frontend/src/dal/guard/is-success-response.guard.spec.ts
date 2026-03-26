import { describe, it, expect, vi, beforeEach } from "vitest";

// Mock the module BEFORE importing the function under test
vi.mock("./is-base-response.guard", () => ({
  isBaseResponse: vi.fn(),
}));

import { isSuccessResponse } from "./is-success-response.guard";
import { isBaseResponse } from "./is-base-response.guard";

describe("isSuccessResponse", () => {
  const mockIsBaseResponse = isBaseResponse as unknown as ReturnType<typeof vi.fn>;

  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("should return true when base response is valid", () => {
    mockIsBaseResponse.mockReturnValue(true);

    const input = { some: "data" };

    const result = isSuccessResponse(input);

    expect(result).toBe(true);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(input);
  });

  it("should return false when base response is invalid", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const input = { some: "data" };

    const result = isSuccessResponse(input);

    expect(result).toBe(false);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(input);
  });

  it("should handle null input", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const result = isSuccessResponse(null);

    expect(result).toBe(false);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(null);
  });

  it("should handle undefined input", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const result = isSuccessResponse(undefined);

    expect(result).toBe(false);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(undefined);
  });

  it("should handle primitive input", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const result = isSuccessResponse("string");

    expect(result).toBe(false);
    expect(mockIsBaseResponse).toHaveBeenCalledWith("string");
  });

  it("should still return true even if payload/error/message are not validated (current implementation)", () => {
    mockIsBaseResponse.mockReturnValue(true);

    const malformedInput = {
      payload: null,
      errorCode: undefined,
      message: 123,
    };

    const result = isSuccessResponse(malformedInput);

    expect(result).toBe(true);
  });
});