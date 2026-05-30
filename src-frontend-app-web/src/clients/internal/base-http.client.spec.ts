import { describe, it, expect, vi, beforeEach } from "vitest";

import { BaseHttpClient } from "./base-http.client";
import { Nullable } from "../../framework";

console.warn = vi.fn();

class TestHttpClient extends BaseHttpClient {
  public headers = { "Content-Type": "application/json" };
  public credentials: Nullable<"include"> = "include";

  public beforeRequestMock = vi.fn(async <T>(_url: string, _init: T) => true);
  public afterRequestMock = vi.fn(async <T>(_response: T) => true);

  protected getHttpHeaders(): HeadersInit {
    return this.headers;
  }

  protected beforeRequest(
    url: string,
    init: RequestInit
  ): Promise<boolean> {
    return this.beforeRequestMock(url, init);
  }

  protected afterRequest(
    response: Response
  ): Promise<boolean> {
    return this.afterRequestMock(response);
  }

  protected getCredentialsMode() {
    return this.credentials;
  }

  public callGet(path: string) {
    return this.get(path);
  }

  public callPost(path: string, payload?: string) {
    return this.post(path, payload);
  }

  public callPatch(path: string, payload?: string) {
    return this.patch(path, payload);
  }

  public callPut(path: string, payload?: string) {
    return this.put(path, payload);
  }

  public callDelete(path: string) {
    return this.delete(path);
  }
}

describe("BaseHttpClientSpecs", () => {
  let client: TestHttpClient;

  beforeEach(() => {
    client = new TestHttpClient("https://api.test.com");

    globalThis.fetch = vi.fn();
    vi.clearAllMocks();
  });

  it("should perform GET request successfully", async () => {
    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("ok"),
    });

    const result = await client.callGet("/users");

    expect(fetch).toHaveBeenCalledWith(
      "https://api.test.com/users",
      expect.objectContaining({
        method: "GET",
        headers: client.headers,
        credentials: "include",
      })
    );

    expect(result.isSuccessTC()).toBe(true);
    expect(result.isSuccessTC() && result.payload).toBe("ok");
  });

  it("should perform POST with payload", async () => {
    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("created"),
    });

    await client.callPost("/users", '{"name":"john"}');

    expect(fetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        method: "POST",
        body: '{"name":"john"}',
      })
    );
  });

  it("should perform PATCH request", async () => {
    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("patched"),
    });

    await client.callPatch("/users", "data");

    expect(fetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        method: "PATCH",
      })
    );
  });

  it("should perform PUT request", async () => {
    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("updated"),
    });

    await client.callPut("/users", "payload");

    expect(fetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        method: "PUT",
      })
    );
  });

  it("should perform DELETE request", async () => {
    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("deleted"),
    });

    await client.callDelete("/users");

    expect(fetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        method: "DELETE",
      })
    );
  });

  it("should return warning when beforeRequest returns false", async () => {
    client.beforeRequestMock.mockResolvedValue(false);

    const result = await client.callGet("/users");

    expect(fetch).not.toHaveBeenCalled();
    expect(result.isWarnTC()).toBe(true);
    expect(result.isWarnTC() && result.errors).toStrictEqual(["W_CANCELED_BEFORE"]);
  });

  it("should continue when beforeRequest throws", async () => {
    client.beforeRequestMock.mockRejectedValue(new Error("before failed"));

    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("ok"),
    });

    const result = await client.callGet("/users");

    expect(fetch).toHaveBeenCalled();
    expect(result.isSuccessTC()).toBe(true);
  });

  it("should return warning when afterRequest returns false", async () => {
    client.afterRequestMock.mockResolvedValue(false);

    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("ok"),
    });

    const result = await client.callGet("/users");

    expect(result.isWarnTC()).toBe(true);
    expect(result.isWarnTC() && result.errors).toStrictEqual(["W_CANCELED_AFTER"]);
  });

  it("should continue when afterRequest throws", async () => {
    client.afterRequestMock.mockRejectedValue(new Error("after failed"));

    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("ok"),
    });

    const result = await client.callGet("/users");

    expect(result.isSuccessTC()).toBe(true);
  });

  it("should return warning for HTTP status >= 400", async () => {
    (fetch as any).mockResolvedValue({
      status: 404,
      text: vi.fn().mockResolvedValue("not found"),
    });

    const result = await client.callGet("/users");

    expect(result.isWarnTC()).toBe(true);
    expect(result.isWarnTC() && result.errors).toStrictEqual(["404"]);
    expect(result.isWarnTC() && result.payload).toBe("not found");
  });

  it("should return error when fetch throws", async () => {
    (fetch as any).mockRejectedValue(new Error("network"));

    const result = await client.callGet("/users");

    expect(result.isErrorTC()).toBe(true);
    expect(result.isErrorTC() && result.errors).toStrictEqual(["E_FETCH_FAILED"]);
  });

  it("should omit body when payload is nil", async () => {
    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("ok"),
    });

    await client.callPost("/users");

    expect(fetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        body: undefined,
      })
    );
  });

  it("should omit credentials when credentials mode is nil", async () => {
    client.credentials = null;

    (fetch as any).mockResolvedValue({
      status: 200,
      text: vi.fn().mockResolvedValue("ok"),
    });

    await client.callGet("/users");

    expect(fetch).toHaveBeenCalledWith(
      expect.any(String),
      expect.objectContaining({
        credentials: undefined,
      })
    );
  });
});