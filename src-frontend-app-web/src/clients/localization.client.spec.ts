import { describe, it, expect, vi, beforeEach } from "vitest";
import { HeaderNames, HeaderValueAccept } from "../domain";
import { newGuid } from "../framework";

import { LocalizationClient } from "./localization.client";

vi.mock("../environment", () => ({
  Environment: {
    platform: "test-platform"
  }
}));

vi.mock("../runtime-context", () => ({
  RuntimeContext: {
    device: "test-device"
  }
}));

vi.mock("../framework", () => ({
  newGuid: vi.fn()
}));

const afterInterceptorMock = vi.fn();

vi.mock("../http", () => ({
  InterceptorCache: {
    getInstance: vi.fn(() => ({
      getAfterInterceptors: () => [afterInterceptorMock]
    }))
  }
}));

describe("LocalizationClientSpecs", () => {
  const fetchMock = vi.fn();

  beforeEach(() => {
    vi.clearAllMocks();

    globalThis.fetch = fetchMock as any;
    vi.mocked(newGuid).mockReturnValue("test-guid");
  });

  it("should call fetch with correct headers and return payload", async () => {
    const client = new LocalizationClient("http://base-url");

    fetchMock.mockResolvedValue({
      text: async () => JSON.stringify({ payload: { hello: "world" } })
    });

    const result = await client.getLocalization("en");

    expect(fetchMock).toHaveBeenCalledWith(
      "http://base-url/api/v1/localization/en",
      {
        method: "GET",
        headers: {
          [HeaderNames.Platform]: "test-platform",
          [HeaderNames.Device]: "test-device",
          [HeaderNames.RequestId]: "test-guid",
          [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson
        }
      }
    );

    expect(result).toEqual({ hello: "world" });
  });

  it("should execute after interceptors with response", async () => {
    const client = new LocalizationClient("http://base-url");

    const responseMock = {
      text: async () => JSON.stringify({ payload: {} })
    };

    fetchMock.mockResolvedValue(responseMock);

    await client.getLocalization("en");

    expect(afterInterceptorMock).toHaveBeenCalledWith(responseMock);
  });
});