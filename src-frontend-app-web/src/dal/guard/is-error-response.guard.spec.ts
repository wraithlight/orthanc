import { describe, it, expect, vi, beforeEach } from "vitest";

vi.mock("./is-base-response.guard", () => ({
  isBaseResponse: vi.fn(),
}));

import { isErrorResponse } from "./is-error-response.guard";
import { isBaseResponse } from "./is-base-response.guard";

describe("isErrorResponse", () => {
  const mockIsBaseResponse = isBaseResponse as unknown as ReturnType<typeof vi.fn>;

  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("should return true when base response is valid", () => {
    mockIsBaseResponse.mockReturnValue(true);

    const input = { errorCode: "MOCK_E", message: "data" };

    const result = isErrorResponse(input);

    expect(result).toBe(true);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(input);
  });

  it("should return false when base response is invalid", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const input = { some: "data" };

    const result = isErrorResponse(input);

    expect(result).toBe(false);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(input);
  });

  it("should handle null input", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const result = isErrorResponse(null);

    expect(result).toBe(false);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(null);
  });

  it("should handle undefined input", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const result = isErrorResponse(undefined);

    expect(result).toBe(false);
    expect(mockIsBaseResponse).toHaveBeenCalledWith(undefined);
  });

  it("should handle primitive input", () => {
    mockIsBaseResponse.mockReturnValue(false);

    const result = isErrorResponse("string");

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

    const result = isErrorResponse(malformedInput);

    expect(result).toBe(true);
  });
});