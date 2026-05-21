import { describe, it, expect, beforeEach, vi } from "vitest";

import { CookieRepository } from "./cookie.repository";

describe("CookieRepository", () => {
  let repo: CookieRepository;

  let cookieStore: Record<string, string>;

  beforeEach(() => {
    repo = new CookieRepository();
    cookieStore = {};

    Object.defineProperty(document, "cookie", {
      configurable: true,
      get() {
        return Object.entries(cookieStore)
          .map(([k, v]) => `${k}=${v}`)
          .join("; ");
      },
      set(value: string) {
        const [pair] = value.split(";");
        const [name, val] = pair.split("=");

        if (val === "") {
          delete cookieStore[name];
        } else {
          cookieStore[name] = val;
        }
      }
    });
  });

  it("should create a cookie", () => {
    repo.create("test", { a: 1 }, 1);

    expect(document.cookie).toContain("test=");
    expect(cookieStore["test"]).toBeDefined();
  });

  it("should correctly serialize and deserialize value", () => {
    const value = { a: 1, b: "x" };

    repo.create("data", value, 1);
    const result = repo.getOrDefault("data", null);

    expect(result).toEqual(value);
  });

  it("should return default value if cookie not found", () => {
    const result = repo.getOrDefault("missing", { fallback: true });

    expect(result).toEqual({ fallback: true });
  });

  it("should handle primitive values", () => {
    repo.create("num", 42, 1);

    const result = repo.getOrDefault("num", 0);

    expect(result).toBe(42);
  });

  it("should handle string values", () => {
    repo.create("str", "hello", 1);

    const result = repo.getOrDefault("str", "");

    expect(result).toBe("hello");
  });

  it("should delete a cookie", () => {
    repo.create("toDelete", { x: 1 }, 1);
    expect(cookieStore["toDelete"]).toBeDefined();

    repo.delete("toDelete");

    expect(cookieStore["toDelete"]).toBeUndefined();
  });

  it("should overwrite existing cookie with same name", () => {
    repo.create("dup", { a: 1 }, 1);
    repo.create("dup", { a: 2 }, 1);

    const result = repo.getOrDefault("dup", null);

    expect(result).toEqual({ a: 2 });
  });

  it("should handle multiple cookies", () => {
    repo.create("a", 1, 1);
    repo.create("b", 2, 1);

    expect(repo.getOrDefault("a", 0)).toBe(1);
    expect(repo.getOrDefault("b", 0)).toBe(2);
  });

  it("should handle encoded characters", () => {
    const value = { text: "hello world !@#$%^&*()" };

    repo.create("encoded", value, 1);
    const result = repo.getOrDefault("encoded", null);

    expect(result).toEqual(value);
  });

  it("should use buildExpires (indirect coverage)", () => {
    const spy = vi.spyOn(Date.prototype, "toUTCString");

    repo.create("expiry", { a: 1 }, 1);

    expect(spy).toHaveBeenCalled();

    spy.mockRestore();
  });
});
