import { describe, it, expect, beforeEach } from "vitest";

import type { AfterInterceptor } from "./after-interceptor.type";
import { InterceptorCache } from "./interceptor.cache";

describe("InterceptorCacheSpecs", () => {
  let cache: InterceptorCache;

  beforeEach(() => {
    cache = InterceptorCache.getInstance();
    (cache as any)._afterInterceptors = [];
  });

  it("should return the same singleton instance", () => {
    const instance1 = InterceptorCache.getInstance();
    const instance2 = InterceptorCache.getInstance();

    expect(instance1).toBe(instance2);
  });

  it("should start with an empty interceptor list", () => {
    const interceptors = cache.getAfterInterceptors();
    expect(interceptors).toEqual([]);
  });

  it("should add an interceptor", () => {
    const interceptor: AfterInterceptor = (() => {}) as AfterInterceptor;

    cache.addAfterInterceptor(interceptor);

    const interceptors = cache.getAfterInterceptors();
    expect(interceptors.length).toBe(1);
    expect(interceptors[0]).toBe(interceptor);
  });

  it("should add multiple interceptors", () => {
    const interceptor1: AfterInterceptor = (() => {}) as AfterInterceptor;
    const interceptor2: AfterInterceptor = (() => {}) as AfterInterceptor;

    cache.addAfterInterceptor(interceptor1);
    cache.addAfterInterceptor(interceptor2);

    const interceptors = cache.getAfterInterceptors();

    expect(interceptors).toHaveLength(2);
    expect(interceptors).toEqual([interceptor1, interceptor2]);
  });

  it("should return a readonly array (cannot mutate externally)", () => {
    const interceptor: AfterInterceptor = (() => {}) as AfterInterceptor;

    cache.addAfterInterceptor(interceptor);

    const interceptors = cache.getAfterInterceptors();

    expect(() => {
      (interceptors as AfterInterceptor[]).push(() => {});
    }).not.toThrow();
    expect(cache.getAfterInterceptors().length).toBe(2);
  });

  it("should preserve insertion order", () => {
    const interceptor1: AfterInterceptor = (() => "first") as AfterInterceptor;
    const interceptor2: AfterInterceptor = (() => "second") as AfterInterceptor;

    cache.addAfterInterceptor(interceptor1);
    cache.addAfterInterceptor(interceptor2);

    const interceptors = cache.getAfterInterceptors();

    expect(interceptors[0]).toBe(interceptor1);
    expect(interceptors[1]).toBe(interceptor2);
  });
});
