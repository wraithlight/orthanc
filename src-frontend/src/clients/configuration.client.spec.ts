import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";

import { ConfiugartionClient } from "./configuration.client";

vi.mock("../framework", () => ({
  newGuid: vi.fn(),
}));

vi.mock("../environment", () => ({
  Environment: {
    platform: "test-platform",
  },
}));

import { newGuid } from "../framework";
import { Environment } from "../environment";
import { HeaderNames } from "../domain";

describe("ConfiugartionClient", () => {
  const baseUrl = "http://test.com";

  beforeEach(() => {
    vi.clearAllMocks();
  });

  afterEach(() => {
    vi.restoreAllMocks();
  });

  it("should call fetch with correct url, method and headers", async () => {
    (newGuid as any).mockReturnValue("test-guid");

    const fetchMock = vi.fn().mockResolvedValue({
      text: vi.fn().mockResolvedValue('{"payload":{"key":"value"}}'),
    });

    globalThis.fetch = fetchMock as any;

    const parseSpy = vi
      .spyOn(JSON, "parse")
      .mockReturnValue({ payload: { key: "value" } });

    const client = new ConfiugartionClient(baseUrl);

    const result = await client.getConfiguration();

    expect(fetchMock).toHaveBeenCalledWith(
      `${baseUrl}/api/v1/configuration`,
      {
        method: "GET",
        headers: {
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.RequestId]: "test-guid",
          [HeaderNames.Accept]: "application/json",
        },
      }
    );

    expect(parseSpy).toHaveBeenCalledWith(
      '{"payload":{"key":"value"}}'
    );

    expect(result).toEqual({ key: "value" });
  });

  it("should return payload from parsed response", async () => {
    (newGuid as any).mockReturnValue("guid-2");

    globalThis.fetch = vi.fn().mockResolvedValue({
      text: vi.fn().mockResolvedValue("raw-response"),
    }) as any;

    vi.spyOn(JSON, "parse").mockReturnValue({
      payload: { foo: "bar" },
    });

    const client = new ConfiugartionClient(baseUrl);

    const result = await client.getConfiguration();

    expect(result).toEqual({ foo: "bar" });
  });
});