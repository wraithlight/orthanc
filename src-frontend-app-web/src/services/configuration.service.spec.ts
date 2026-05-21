import { describe, it, expect, vi, beforeEach } from "vitest";

vi.mock("../environment", () => ({
  Environment: {
    apiBaseUrl: "http://mock-api"
  }
}));

import { ConfigurationService } from "./configuration.service";
import { ConfigurationClient } from "../clients";

describe("ConfigurationServiceSpecs", () => {
  beforeEach(() => {
    vi.restoreAllMocks();
  });

  it("should call ConfigurationClient.getConfiguration when fetchConfiguration is invoked", async () => {
    const mockTuple: [string | null, any] = [null, { some: "config" }];

    const spy = vi
      .spyOn(ConfigurationClient.prototype, "getConfiguration")
      .mockResolvedValue(mockTuple);

    const service = new ConfigurationService();

    await service.fetchConfiguration();

    expect(spy).toHaveBeenCalledTimes(1);
  });

  it("should return the tuple received from the client", async () => {
    const mockTuple: [string | null, any] = ["error", { key: "value" }];

    vi.spyOn(ConfigurationClient.prototype, "getConfiguration")
      .mockResolvedValue(mockTuple);

    const service = new ConfigurationService();

    const result = await service.fetchConfiguration();

    expect(result).toBe(mockTuple);
    expect(Array.isArray(result)).toBe(true);
    expect(result).toHaveLength(2);
    expect(result[0]).toBe(mockTuple[0]);
    expect(result[1]).toBe(mockTuple[1]);
  });
});
