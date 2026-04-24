import { describe, it, expect, vi, beforeEach } from "vitest";

import { HallOfFameClient } from "./hall-of-fame.client";

vi.mock("../environment", () => ({
  Environment: {
    platform: "test-platform",
  },
}));

vi.mock("../framework", () => ({
  newGuid: vi.fn(() => "test-guid"),
}));

const mockInterceptors: any[] = [];
const getAfterInterceptorsMock = vi.fn(() => mockInterceptors);

vi.mock("../http", () => ({
  InterceptorCache: {
    getInstance: vi.fn(() => ({
      getAfterInterceptors: getAfterInterceptorsMock,
    })),
  },
}));

describe("HallOfFameClientSpecs", () => {
  const baseUrl = "http://test-api";
  const gameMode = "classic" as any;

  beforeEach(() => {
    vi.clearAllMocks();
    mockInterceptors.length = 0;
  });

  it("should call fetch with correct URL and headers", async () => {
    const mockResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload: { data: [1, 2, 3] } })
      ),
    };

    globalThis.fetch = vi.fn().mockResolvedValue(mockResponse as any);

    const client = new HallOfFameClient(baseUrl);
    await client.getHallOfFame(gameMode);

    expect(fetch).toHaveBeenCalledWith(
      `${baseUrl}/api/v1/main/hall-of-fame?game-mode=${gameMode}`,
      {
        method: "GET",
        headers: {
          "X-ORTHANC-PLATFORM": "test-platform",
          "X-ORTHANC-REQUEST-ID": "test-guid",
          Accept: "application/json",
        },
      }
    );
  });

  it("should return parsed payload", async () => {
    const payload = { leaderboard: [1, 2, 3] };

    const mockResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload })
      ),
    };

    globalThis.fetch = vi.fn().mockResolvedValue(mockResponse as any);

    const client = new HallOfFameClient(baseUrl);
    const result = await client.getHallOfFame(gameMode);

    expect(result).toEqual(payload);
  });

  it("should execute all interceptors with response", async () => {
    const interceptor1 = vi.fn();
    const interceptor2 = vi.fn();

    mockInterceptors.push(interceptor1, interceptor2);

    const mockResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload: {} })
      ),
    };

    globalThis.fetch = vi.fn().mockResolvedValue(mockResponse as any);

    const client = new HallOfFameClient(baseUrl);
    await client.getHallOfFame(gameMode);

    expect(getAfterInterceptorsMock).toHaveBeenCalled();

    expect(interceptor1).toHaveBeenCalledWith(mockResponse);
    expect(interceptor2).toHaveBeenCalledWith(mockResponse);
  });

  it("should handle empty interceptor list", async () => {
    const mockResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload: {} })
      ),
    };

    globalThis.fetch = vi.fn().mockResolvedValue(mockResponse as any);

    const client = new HallOfFameClient(baseUrl);
    const result = await client.getHallOfFame(gameMode);

    expect(result).toEqual({});
    expect(getAfterInterceptorsMock).toHaveBeenCalled();
  });
});
