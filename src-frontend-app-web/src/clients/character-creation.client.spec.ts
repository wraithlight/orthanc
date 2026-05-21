import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";

import { HeaderNames, HeaderValueAccept } from "../domain";

import { CharacterCreationClient } from "./character-creation.client";

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

describe("CharacterCreationClientSpecs", () => {
  let client: CharacterCreationClient;

  beforeEach(() => {
    client = new CharacterCreationClient("https://api.test.com");
    globalThis.fetch = vi.fn();
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("should call generate endpoint and return payload", async () => {
    const payload = {
      strength: 10,
      agility: 12,
      intelligence: 14,
    };

    const fetchResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload })
      ),
    };

    (fetch as any).mockResolvedValue(fetchResponse);

    const result = await client.generateStats();

    expect(fetch).toHaveBeenCalledTimes(1);

    const [url, options] = (fetch as any).mock.calls[0];

    expect(url).toBe(
      "https://api.test.com/api/v1/character-creation/generate"
    );

    expect(options).toMatchObject({
      method: "POST",
      credentials: "include",
    });

    expect(options.headers).toMatchObject({
      [HeaderNames.Platform]: "test-platform",
      [HeaderNames.Device]: "test-device",
      [HeaderNames.RequestId]: "test-guid",
      [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
    });

    expect(fetchResponse.text).toHaveBeenCalled();

    expect(getAfterInterceptorsMock).toHaveBeenCalled();
    expect(afterInterceptorMock).toHaveBeenCalledWith(fetchResponse);

    expect(result).toEqual(payload);
  });

  it("should handle empty payload", async () => {
    const fetchResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload: null })
      ),
    };

    (fetch as any).mockResolvedValue(fetchResponse);

    const result = await client.generateStats();

    expect(result).toBeNull();
  });

  it("should correctly parse complex stats object", async () => {
    const payload = {
      strength: 1,
      agility: 2,
      intelligence: 3,
      charisma: 4,
      luck: 5,
    };

    const fetchResponse = {
      text: vi.fn().mockResolvedValue(
        JSON.stringify({ payload })
      ),
    };

    (fetch as any).mockResolvedValue(fetchResponse);

    const result = await client.generateStats();

    expect(result).toEqual(payload);
  });
});