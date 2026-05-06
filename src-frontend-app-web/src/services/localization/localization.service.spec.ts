import { describe, it, expect, vi, beforeEach } from "vitest";
import { LocalizationService } from "./localization.service";
import { LocalizationClient } from "../../clients";

describe("LocalizationServiceSpecs", () => {
  const getLocalizationMock = vi.fn();

  beforeEach(() => {
    vi.clearAllMocks();

    vi.spyOn(LocalizationClient.prototype, "getLocalization").mockImplementation(
      getLocalizationMock as any
    );
  });

  it("should call client.getLocalization with locale and return result", async () => {
    getLocalizationMock.mockResolvedValue({
      hello: "world"
    });

    const service = new LocalizationService();

    const result = await service.getLocalization("en");

    expect(getLocalizationMock).toHaveBeenCalledWith("en");
    expect(result).toEqual({
      hello: "world"
    });
  });
});