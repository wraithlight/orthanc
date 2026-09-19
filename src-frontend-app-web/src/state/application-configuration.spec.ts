import { describe, it, expect, vi } from "vitest";

import { ApplicationConfigurationState } from "./application-configuration";

describe("ApplicationConfigurationStateSpecs", () => {
  it("returns predicate result", () => {
    const config = {} as any;
    const predicate = vi.fn().mockReturnValue("value");
    expect(new ApplicationConfigurationState(config).getOrDefault(predicate, "default")).toBe("value");
    expect(predicate).toHaveBeenCalledWith(config);
  });

  it("returns default when predicate returns nullish", () => {
    const config = {} as any;
    expect(new ApplicationConfigurationState(config).getOrDefault(vi.fn().mockReturnValue(undefined), "default")).toBe("default");
    expect(new ApplicationConfigurationState(config).getOrDefault(vi.fn().mockReturnValue(null), "default")).toBe("default");
  });
});
