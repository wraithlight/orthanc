import { describe, it, expect, vi, beforeEach } from "vitest";
import { readFromConfigState } from "../state";
import { isNil } from "../framework";

vi.mock("../state", () => ({
  readFromConfigState: vi.fn()
}));

vi.mock("../framework", () => ({
  isNil: vi.fn()
}));

const openDialogMock = vi.fn();

vi.mock("../services", () => ({
  DialogQueueService: {
    getInstance: vi.fn(() => ({
      openDialog: openDialogMock
    }))
  }
}));

describe("doVersionCheckSpecs", () => {
  const reloadMock = vi.fn();
  const warnSpy = vi.spyOn(console, "warn").mockImplementation(() => {});

  beforeEach(() => {
    vi.clearAllMocks();
    vi.resetModules();

    Object.defineProperty(window, "location", {
      value: {
        reload: reloadMock
      },
      writable: true
    });
  });

  it("should return early if requiredVersion is nil", async () => {
    const { doVersionCheck } = await import("./version-check");

    vi.mocked(isNil).mockReturnValue(true);

    doVersionCheck(null);

    expect(isNil).toHaveBeenCalledWith(null);
    expect(warnSpy).toHaveBeenCalled();
    expect(readFromConfigState).not.toHaveBeenCalled();
    expect(openDialogMock).not.toHaveBeenCalled();
  });

  it("should not open dialog when versions match", async () => {
    const { doVersionCheck } = await import("./version-check");

    vi.mocked(isNil).mockReturnValue(false);
    vi.mocked(readFromConfigState).mockReturnValue("1.0.0");

    doVersionCheck("1.0.0");

    expect(readFromConfigState).toHaveBeenCalledWith(
      expect.any(Function),
      "1.0.0"
    );

    expect(openDialogMock).not.toHaveBeenCalled();
  });

  it("should open dialog when versions mismatch", async () => {
    const { doVersionCheck } = await import("./version-check");

    vi.mocked(isNil).mockReturnValue(false);
    vi.mocked(readFromConfigState).mockReturnValue("1.0.0");

    doVersionCheck("2.0.0");

    expect(openDialogMock).toHaveBeenCalledTimes(1);

    expect(openDialogMock).toHaveBeenCalledWith(
      "version-mismatch-dialog",
      "Warning!",
      "You have an outdated version of the game! Some features may not work, please refresh the window to avoid version mismatch issues.",
      [
        {
          id: "cta-refresh",
          label: "Refresh",
          onClick: expect.any(Function)
        }
      ],
      expect.anything()
    );
  });

  it("should reload page when refresh button callback is triggered", async () => {
    const { doVersionCheck } = await import("./version-check");

    vi.mocked(isNil).mockReturnValue(false);
    vi.mocked(readFromConfigState).mockReturnValue("1.0.0");

    doVersionCheck("2.0.0");

    const dialogArgs = openDialogMock.mock.calls[0];
    const buttons = dialogArgs[3];

    buttons[0].onClick();

    expect(reloadMock).toHaveBeenCalled();
  });

  it("should not open multiple dialogs when already visible", async () => {
    const { doVersionCheck } = await import("./version-check");

    vi.mocked(isNil).mockReturnValue(false);
    vi.mocked(readFromConfigState).mockReturnValue("1.0.0");

    doVersionCheck("2.0.0");
    doVersionCheck("3.0.0");

    expect(openDialogMock).toHaveBeenCalledTimes(1);
  });
});
