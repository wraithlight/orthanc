import { describe, it, expect, beforeEach } from "vitest";

import type { AfterInterceptor } from "./after-interceptor.type";
import { BeforeInterceptor } from "./before-interceptor.type";
import { InterceptorCache } from "./interceptor.cache";

describe("InterceptorCacheSpecs", () => {
  let cache: InterceptorCache;

  beforeEach(() => {
    cache = InterceptorCache.getInstance();
    (cache as any)._afterInterceptors = [];
    (cache as any)._beforeInterceptors = [];
  });

  it("should return the same singleton instance", () => {
    const instance1 = InterceptorCache.getInstance();
    const instance2 = InterceptorCache.getInstance();

    expect(instance1).toBe(instance2);
  });

  it("should start with an empty after interceptor list", () => {
    const interceptors = cache.getAfterInterceptors();
    expect(interceptors).toEqual([]);
  });

  it("should start with an empty before interceptor list", () => {
    const interceptors = cache.getBeforeInterceptors();
    expect(interceptors).toEqual([]);
  });

  it("should add an after interceptor", () => {
    const interceptor: AfterInterceptor = (() => {}) as AfterInterceptor;

    cache.addAfterInterceptor(interceptor);

    const interceptors = cache.getAfterInterceptors();
    expect(interceptors.length).toBe(1);
    expect(interceptors[0]).toBe(interceptor);
  });

  it("should add an before interceptor", () => {
    const interceptor: BeforeInterceptor = (() => {}) as BeforeInterceptor;

    cache.addBeforeInterceptor(interceptor);

    const interceptors = cache.getBeforeInterceptors();
    expect(interceptors.length).toBe(1);
    expect(interceptors[0]).toBe(interceptor);
  });
});
