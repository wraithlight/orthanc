import { describe, expect, it, vi } from "vitest";
import { HeaderNames } from "../domain";
import { createVersionCheckerInterceptor } from "./version-checker.interceptor";
import { createAfterInterceptor } from "../http";
import { doVersionCheck } from "../version-check";

let interceptor: (res: Response) => void;

vi.mock("../http", () => ({
  createAfterInterceptor: vi.fn((cb) => {
    interceptor = cb;
  }),
}));

vi.mock("../version-check", () => ({
  doVersionCheck: vi.fn(),
}));

describe("createVersionCheckerInterceptorSpecs", () => {
  it("registers and executes the interceptor", () => {
    createVersionCheckerInterceptor();

    expect(createAfterInterceptor).toHaveBeenCalledWith(expect.any(Function));

    interceptor({
      headers: {
        get: vi.fn(() => "1.2.3"),
      },
      // TODO: fw-level cast<T>(a: unknown): T
    } as unknown as Response);

    expect(doVersionCheck).toHaveBeenCalledWith("1.2.3");
  });

  it("passes null version", () => {
    createVersionCheckerInterceptor();

    interceptor({
      headers: {
        get: vi.fn(() => null),
      },
      // TODO: fw-level cast<T>(a: unknown): T
    } as unknown as Response);

    expect(doVersionCheck).toHaveBeenCalledWith(null);
  });

  it("uses HeaderNames enum", () => {
    createVersionCheckerInterceptor();

    const get = vi.fn();

    interceptor({
      headers: { get },
      // TODO: fw-level cast<T>(a: unknown): T
    } as unknown as Response);

    expect(get).toHaveBeenCalledWith(
      HeaderNames.PlatformVersion.toLowerCase()
    );
  });
});