import { describe, it, expect, vi, beforeEach } from "vitest";
import type { BeforeInterceptor } from "./before-interceptor.type";

const addBeforeInterceptorMock = vi.fn();

vi.mock("./interceptor.cache", () => {
  return {
    InterceptorCache: {
      getInstance: vi.fn(() => ({
        addBeforeInterceptor: addBeforeInterceptorMock,
      })),
    },
  };
});

import { createBeforeInterceptor } from "./create-before-interceptor.factory";
import { InterceptorCache } from "./interceptor.cache";

describe("createBeforeInterceptorSpecs", () => {
  beforeEach(() => {
    vi.clearAllMocks();
  });

  it("should call InterceptorCache.getInstance", () => {
    const interceptor = (() => {}) as BeforeInterceptor;

    createBeforeInterceptor(interceptor);

    expect(InterceptorCache.getInstance).toHaveBeenCalledTimes(1);
  });

  it("should call addBeforeInterceptor with the provided interceptor", () => {
    const interceptor = (() => {}) as BeforeInterceptor;

    createBeforeInterceptor(interceptor);

    expect(addBeforeInterceptorMock).toHaveBeenCalledTimes(1);
    expect(addBeforeInterceptorMock).toHaveBeenCalledWith(interceptor);
  });

  it("should pass through different interceptors correctly", () => {
    const interceptor1 = (() => "a") as BeforeInterceptor;
    const interceptor2 = (() => "b") as BeforeInterceptor;

    createBeforeInterceptor(interceptor1);
    createBeforeInterceptor(interceptor2);

    expect(addBeforeInterceptorMock).toHaveBeenNthCalledWith(1, interceptor1);
    expect(addBeforeInterceptorMock).toHaveBeenNthCalledWith(2, interceptor2);
  });
});
