import { describe, it, expect, vi, beforeEach } from "vitest";
import type { AfterInterceptor } from "./after-interceptor.type";

const addAfterInterceptorMock = vi.fn();

vi.mock("./interceptor.cache", () => {
  return {
    InterceptorCache: {
      getInstance: vi.fn(() => ({
        addAfterInterceptor: addAfterInterceptorMock,
      })),
    },
  };
});

import { createAfterInterceptor } from "./create-after-interceptor.factory";
import { InterceptorCache } from "./interceptor.cache";

describe("createAfterInterceptorSpecs", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("should call InterceptorCache.getInstance", () => {
    const interceptor = (() => {}) as AfterInterceptor;

    createAfterInterceptor(interceptor);

    expect(InterceptorCache.getInstance).toHaveBeenCalledTimes(1);
  });

  it("should call addAfterInterceptor with the provided interceptor", () => {
    const interceptor = (() => {}) as AfterInterceptor;

    createAfterInterceptor(interceptor);

    expect(addAfterInterceptorMock).toHaveBeenCalledTimes(1);
    expect(addAfterInterceptorMock).toHaveBeenCalledWith(interceptor);
  });

  it("should pass through different interceptors correctly", () => {
    const interceptor1 = (() => "a") as AfterInterceptor;
    const interceptor2 = (() => "b") as AfterInterceptor;

    createAfterInterceptor(interceptor1);
    createAfterInterceptor(interceptor2);

    expect(addAfterInterceptorMock).toHaveBeenNthCalledWith(1, interceptor1);
    expect(addAfterInterceptorMock).toHaveBeenNthCalledWith(2, interceptor2);
  });
});
