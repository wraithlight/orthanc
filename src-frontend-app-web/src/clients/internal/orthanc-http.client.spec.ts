import { describe, it, expect, vi, beforeEach } from "vitest";

import { HeaderNames } from "../../domain";
import { newGuid, Nullable } from "../../framework";

import { OrthancHttpClient } from "./orthanc-http.client";
import { Credentials } from "./base-http.model";

const mockGetJsonHeaders = vi.fn();

vi.mock("./json-http.client", () => ({
  JsonHttpClient: class {
    constructor(protected readonly _baseUrl: string) {}

    protected getJsonHeaders() {
      return mockGetJsonHeaders();
    }
  },
}));

vi.mock("../../environment", () => ({
  Environment: {
    platform: "TEST_PLATFORM",
  },
}));

vi.mock("../../framework", () => ({
  newGuid: vi.fn(() => "GUID-123"),
}));

vi.mock("../../runtime-context", () => ({
  RuntimeContext: {
    device: "TEST_DEVICE",
  },
}));

const interceptor1 = vi.fn();
const interceptor2 = vi.fn();

const mockGetAfterInterceptors = vi.fn();

vi.mock("../../http", () => ({
  InterceptorCache: {
    getInstance: vi.fn(() => ({
      getAfterInterceptors: mockGetAfterInterceptors,
    })),
  },
}));

class TestOrthancHttpClient extends OrthancHttpClient {

  protected getCredentialsMode(): Nullable<Credentials> {
    return undefined;
  }

  public callBeforeRequest(
    url: string,
    init: RequestInit
  ) {
    return this.beforeRequest(url, init);
  }

  public callAfterRequest(
    response: Response
  ) {
    return this.afterRequest(response);
  }

  public callHeaders() {
    return this.getHttpHeaders();
  }
}

describe("OrthancHttpClientSpecs", () => {
  let client: TestOrthancHttpClient;

  beforeEach(() => {
    client = new TestOrthancHttpClient(
      "http://api.test"
    );

    vi.clearAllMocks();

    mockGetJsonHeaders.mockReturnValue({
      Accept: "application/json",
      "Content-Type": "application/json",
    });

    mockGetAfterInterceptors.mockReturnValue([
      interceptor1,
      interceptor2,
    ]);
  });

  it("should return true from beforeRequest", async () => {
    const result =
      await client.callBeforeRequest(
        "/users",
        {}
      );

    expect(result).toBe(true);
  });

  it("should execute after interceptors", async () => {
    const response = {} as Response;

    const result =
      await client.callAfterRequest(response);

    expect(mockGetAfterInterceptors).toHaveBeenCalled();
    expect(interceptor1).toHaveBeenCalledWith(response);
    expect(interceptor2).toHaveBeenCalledWith(response);
    expect(result).toBe(true);
  });

  it("should merge json and orthanc headers", () => {
    const headers = client.callHeaders();

    expect(
      mockGetJsonHeaders
    ).toHaveBeenCalled();

    expect(headers).toEqual({
      Accept: "application/json",
      "Content-Type": "application/json",
      [HeaderNames.Device]: "TEST_DEVICE",
      [HeaderNames.Platform]: "TEST_PLATFORM",
      [HeaderNames.RequestId]: "GUID-123",
    });
  });

  it("should generate fresh request id header", () => {
    client.callHeaders();

    expect(newGuid).toHaveBeenCalledTimes(1);
  });
});
