import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";

import { GameActionClient } from "./game-action.client";

import { HeaderNames, AcceptValues } from "../dal-generated";

vi.mock("../framework", () => ({
  newGuid: vi.fn(() => "test-guid"),
}));

vi.mock("../environment", () => ({
  Environment: {
    platform: "test-platform",
  },
}));

vi.mock("../runtime-context", () => ({
  RuntimeContext: {
    device: "test-device",
  },
}));

const afterInterceptorMock = vi.fn();
const getAfterInterceptorsMock = vi.fn(() => [afterInterceptorMock]);

vi.mock("../http", () => ({
  InterceptorCache: {
    getInstance: vi.fn(() => ({
      getAfterInterceptors: getAfterInterceptorsMock,
    })),
  },
}));

describe("GameActionClientSpecs", () => {
  let client: GameActionClient;

  beforeEach(() => {
    client = new GameActionClient("https://api.test.com");
    globalThis.fetch = vi.fn();
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("should send action with payload and return parsed payload", async () => {
    const responsePayload = { success: true };

    const fetchResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload: responsePayload })
      ),
    };

    (fetch as any).mockResolvedValue(fetchResponse);

    const result = await client.onAction({ action: "MOVE", payload: "left" });

    expect(fetch).toHaveBeenCalledTimes(1);

    const [url, options] = (fetch as any).mock.calls[0];

    expect(url).toBe("https://api.test.com/api/v1/game/action");

    expect(options).toMatchObject({
      method: "POST",
      credentials: "include",
    });

    expect(JSON.parse(options.body)).toEqual({
      action: "MOVE",
      payload: "left",
    });

    expect(options.headers).toMatchObject({
      [HeaderNames.PLATFORM]: "test-platform",
      [HeaderNames.DEVICE]: "test-device",
      [HeaderNames.REQUESTID]: "test-guid",
      [HeaderNames.ACCEPTSAPPJSON]: AcceptValues.ApplicationJson,
    });

    expect(fetchResponse.text).toHaveBeenCalled();

    expect(getAfterInterceptorsMock).toHaveBeenCalled();
    expect(afterInterceptorMock).toHaveBeenCalledWith(fetchResponse);

    expect(result).toEqual(responsePayload);
  });

  it("should handle null payload correctly", async () => {
    const fetchResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload: null })
      ),
    };

    (fetch as any).mockResolvedValue(fetchResponse);

    const result = await client.onAction({ action: "INITIAL_IN_GAME", payload: undefined });

    const [, options] = (fetch as any).mock.calls[0];

    expect(JSON.parse(options.body)).toEqual({
      action: "INITIAL_IN_GAME",
      payload: undefined,
    });

    expect(result).toBeNull();
  });

  it("should still call interceptors even if payload is empty object", async () => {
    const fetchResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload: {} })
      ),
    };

    (fetch as any).mockResolvedValue(fetchResponse);

    const result = await client.onAction({ action: "INITIAL_IN_GAME", payload: undefined });

    expect(afterInterceptorMock).toHaveBeenCalledWith(fetchResponse);
    expect(result).toEqual({});
  });
});