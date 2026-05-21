import { describe, it, expect, beforeEach, vi } from "vitest";

let changeListener: ((e: MediaQueryListEvent) => void) | undefined;

const matchMediaMock = vi.fn();

function setupMatchMedia(matches: boolean) {
  matchMediaMock.mockImplementation(() => ({
    matches,
    addEventListener: vi.fn((event, cb) => {
      if (event === "change") {
        changeListener = cb;
      }
    }),
    removeEventListener: vi.fn()
  }));

  Object.defineProperty(window, "matchMedia", {
    writable: true,
    value: matchMediaMock
  });
}

describe("DeviceService", () => {

  beforeEach(() => {
    vi.resetModules();
    changeListener = undefined;
  });

  it("initializes as mobile when media query matches", async () => {
    setupMatchMedia(true);

    const { DeviceService } = await import("./device.service");

    const service = DeviceService.getInstance();

    expect(service.isMobile()).toBe(true);
    expect(service.isDesktop()).toBe(false);
  });

  it("initializes as desktop when media query does not match", async () => {
    setupMatchMedia(false);

    const { DeviceService } = await import("./device.service");

    const service = DeviceService.getInstance();

    expect(service.isMobile()).toBe(false);
    expect(service.isDesktop()).toBe(true);
  });

  it("updates when media query change event fires", async () => {
    setupMatchMedia(false);

    const { DeviceService } = await import("./device.service");

    const service = DeviceService.getInstance();

    expect(service.isMobile()).toBe(false);

    changeListener?.({ matches: true } as MediaQueryListEvent);

    expect(service.isMobile()).toBe(true);
  });

  it("returns observable via isMobile$", async () => {
    setupMatchMedia(true);

    const { DeviceService } = await import("./device.service");

    const service = DeviceService.getInstance();

    const obs = service.isMobile$();

    expect(obs()).toBe(true);
  });

  it("returns computed observable via isDesktop$", async () => {
    setupMatchMedia(true);

    const { DeviceService } = await import("./device.service");

    const service = DeviceService.getInstance();

    const computed = service.isDesktop$();

    expect(computed()).toBe(false);
  });

  it("getInstance returns singleton", async () => {
    setupMatchMedia(true);

    const { DeviceService } = await import("./device.service");

    const a = DeviceService.getInstance();
    const b = DeviceService.getInstance();

    expect(a).toBe(b);
  });

});
