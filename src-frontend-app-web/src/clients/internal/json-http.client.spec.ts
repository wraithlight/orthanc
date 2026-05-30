import { describe, it, expect, vi, beforeEach } from "vitest";
import { JsonHttpClient } from "./json-http.client";
import { OperationResultFactory } from "../../framework";
import {
  HeaderNames,
  HeaderValueAccept,
  HeaderValuesContentType,
} from "../../domain";

const mockGet = vi.fn();
const mockPost = vi.fn();
const mockPatch = vi.fn();
const mockDelete = vi.fn();

vi.mock("./base-http.client", () => ({
  BaseHttpClient: class {
    constructor(protected readonly _baseUrl: string) {}

    protected get(path: string) {
      return mockGet(path);
    }

    protected post(path: string, payload?: string) {
      return mockPost(path, payload);
    }

    protected patch(path: string, payload?: string) {
      return mockPatch(path, payload);
    }

    protected delete(path: string) {
      return mockDelete(path);
    }
  },
}));

class TestJsonHttpClient extends JsonHttpClient {
  protected beforeRequest() {
    return Promise.resolve(true);
  }

  protected afterRequest() {
    return Promise.resolve(true);
  }

  protected getCredentialsMode() {
    return null;
  }

  public callGet<T>(path: string) {
    return this.get<T>(path);
  }

  public callPost(path: string, payload?: unknown) {
    return this.post(path, payload);
  }

  public callPatch(path: string, payload?: unknown) {
    return this.patch(path, payload);
  }

  public callPut(path: string, payload?: unknown) {
    return this.put(path, payload);
  }

  public callDelete(path: string) {
    return this.delete(path);
  }

  public callHeaders() {
    return this.getHttpHeaders();
  }
}

describe("JsonHttpClientSpecs", () => {
  let client: TestJsonHttpClient;

  beforeEach(() => {
    client = new TestJsonHttpClient("http://api.test");

    vi.clearAllMocks();
  });

  it("should create JSON headers", () => {
    const headers = client.callHeaders();

    expect(headers).toEqual({
      [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
      [HeaderNames.ContentType]:
        HeaderValuesContentType.ApplicationJson,
    });
  });

  it("should handle GET success", async () => {
    mockGet.mockResolvedValue({
      payload: '{"id":1}',
      errors: [],
      isWarnTC: () => false,
      isErrorTC: () => false,
    });

    const result = await client.callGet<{ id: number }>("/users");

    expect(mockGet).toHaveBeenCalledWith("/users");
    expect(result.isSuccessTC() && result.payload).toEqual({ id: 1 });
  });

  it("should serialize POST payload", async () => {
    mockPost.mockResolvedValue({
      payload: '{"ok":true}',
      errors: [],
      isWarnTC: () => false,
      isErrorTC: () => false,
    });

    await client.callPost("/users", { name: "john" });

    expect(mockPost).toHaveBeenCalledWith(
      "/users",
      JSON.stringify({ name: "john" })
    );
  });

  it("should serialize PATCH payload", async () => {
    mockPatch.mockResolvedValue({
      payload: '{"ok":true}',
      errors: [],
      isWarnTC: () => false,
      isErrorTC: () => false,
    });

    await client.callPatch("/users", { active: true });

    expect(mockPatch).toHaveBeenCalledWith(
      "/users",
      JSON.stringify({ active: true })
    );
  });

  it("should use patch() internally for PUT", async () => {
    mockPatch.mockResolvedValue({
      payload: '{"ok":true}',
      errors: [],
      isWarnTC: () => false,
      isErrorTC: () => false,
    });

    await client.callPut("/users", { role: "admin" });

    // verifies current implementation behavior
    expect(mockPatch).toHaveBeenCalledWith(
      "/users",
      JSON.stringify({ role: "admin" })
    );
  });

  it("should call DELETE", async () => {
    mockDelete.mockResolvedValue({
      payload: '{"removed":true}',
      errors: [],
      isWarnTC: () => false,
      isErrorTC: () => false,
    });

    await client.callDelete("/users");

    expect(mockDelete).toHaveBeenCalledWith("/users");
  });

  it("should return warning result", async () => {
    mockGet.mockResolvedValue({
      payload: '{"message":"warn"}',
      errors: ["W_1"],
      isWarnTC: () => true,
      isErrorTC: () => false,
    });

    const result = await client.callGet("/users");

    expect(result.isWarnTC()).toBe(true);
  });

  it("should return error result unchanged", async () => {
    const errorResult = {
      payload: "",
      errors: ["E_FAIL"],
      isWarnTC: () => false,
      isErrorTC: () => true,
    };

    mockGet.mockResolvedValue(errorResult);

    const result = await client.callGet("/users");

    expect(result).toBe(errorResult);
  });
});
