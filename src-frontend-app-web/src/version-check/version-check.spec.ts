import { describe, it, expect, vi, beforeEach, beforeAll, afterEach, afterAll } from "vitest";
import { doVersionCheck } from "./version-check";
import { HeaderNames } from "../domain";

const openDialogSpy = vi.fn();
const locationReloadSpy = vi.fn();
const readFromConfigStateSpy = vi.fn();
const consoleWarnSpy = vi.spyOn(console, "warn").mockImplementation(() => undefined);

vi.mock("../services", () => ({
  DialogQueueService: {
    getInstance: () => ({
      openDialog: openDialogSpy
    })
  }
}));

vi.mock("../state", () => ({
  readFromConfigState: (...args: any[]) => readFromConfigStateSpy(...args)
}));

describe("doVersionCheckSpecs", () => {
  afterEach(() => {
    vi.resetModules();
  });
  describe("when i call `doVersionCheck()`", () => {
    describe("and read the version from the configuration", () => {
      beforeAll(() => {
        readFromConfigStateSpy.mockImplementationOnce(() => "1.0.0");
        doVersionCheck("1.0.0");
      });
      afterAll(() => {
        readFromConfigStateSpy.mockReset();
      });
      it("should call `readFromConfigState()`", () => {
        expect(readFromConfigStateSpy).toHaveBeenCalled();
        expect(readFromConfigStateSpy).toHaveBeenCalledTimes(1);
        expect(readFromConfigStateSpy).toHaveBeenCalledWith(expect.any(Function), "1.0.0");
      });
      it("should call `readFromConfigState()` with a proper arrow function", () => {
        const selector = readFromConfigStateSpy.mock.calls[0][0];
        expect(selector({ version: "1.0.0" })).toBe("1.0.0")
      });
    });
    describe("and the current version is nil", () => {
      beforeAll(() => {
        doVersionCheck(null as any);
      });
      it("should show a warning", () => {
        expect(consoleWarnSpy).toHaveBeenCalled();
        expect(consoleWarnSpy).toHaveBeenCalledWith(`The header '${HeaderNames.PlatformVersion}' was not present on the response!`);
      });
      it("should not read the config", () => {
        expect(readFromConfigStateSpy).not.toHaveBeenCalled();
      });
      it("should not open a dialog", () => {
        expect(openDialogSpy).not.toHaveBeenCalled();
      });
    });
    describe("and the current version is valid", () => {
      describe("and it matches the config version", () => {
        beforeAll(() => {
          readFromConfigStateSpy.mockImplementationOnce(() => "1.0.0");
          doVersionCheck("1.0.0");
        });
        it("should not open a new dialog", () => {
          expect(openDialogSpy).not.toHaveBeenCalled();
        });
      });
      describe("and it does not match the config version", () => {
        beforeAll(() => {
          readFromConfigStateSpy.mockImplementationOnce(() => "1.0.0");
          doVersionCheck("2.0.0");
        });
        describe("and a dialog is already visible", () => {
          beforeEach(() => {
            doVersionCheck("2.0.0");
          });
          it("should not open a new dialog", () => {
            expect(openDialogSpy).toHaveBeenCalledTimes(1);
          });
        });
        describe("and a dialog is not visible yet", () => {
          it("should open a new dialog", () => {
            expect(openDialogSpy).toHaveBeenCalled();
            expect(openDialogSpy).toHaveBeenCalledTimes(1);
          });
        });
      });
    });
  });
  describe("when a dialog is already open", () => {
    describe("and i click on `Refresh`", () => {
      beforeEach(() => {
        readFromConfigStateSpy.mockImplementationOnce(() => "1.0.0");
        doVersionCheck("2.0.0");

        globalThis.location = { ...window.location, reload: locationReloadSpy };
        const { onClick } = openDialogSpy.mock.calls[0][3][0];
        onClick();
      });
      it("should reload the location", () => {
        expect(locationReloadSpy).toHaveBeenCalled();
      })
    });
  });
});
