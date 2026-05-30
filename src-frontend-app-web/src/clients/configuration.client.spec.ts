import { describe, it, expect, vi, beforeAll } from "vitest";

import { Environment } from "../environment";
import { HeaderNames, HeaderValueAccept } from "../domain";
import { RuntimeContext } from "../runtime-context";

import { ConfigurationClient } from "./configuration.client";

const MOCK_TEXT = { prop: "value" };

const MOCK_BASE_URL = "http://base.api";
const MOCK_GUID = "mock-guid";
const MOCK_RESPONSE = {
  text: async () => JSON.stringify(MOCK_TEXT),
  headers: {
    get: (headerName: string) => headerName
  }
} as Response;

vi.mock("../framework", () => ({
  newGuid: vi.fn().mockImplementation(() => MOCK_GUID),
}));

const jsonParseSpy = vi.spyOn(JSON, "parse");
const fetchSpy = vi.spyOn(globalThis, "fetch");
fetchSpy.mockImplementation(() => new Promise((resolve, _reject) => resolve(MOCK_RESPONSE)));
const afterInterceptorSpy = vi.fn();
const getAfterInterceptorsSpy = vi.fn(() => [afterInterceptorSpy]);

vi.mock("../http", () => ({
  InterceptorCache: {
    getInstance: vi.fn(() => ({
      getAfterInterceptors: getAfterInterceptorsSpy,
    })),
  },
}));

describe("ConfigurationClientSpecs", () => {
  let service: ConfigurationClient;
  describe("given the client is initalized", () => {
    beforeAll(() => {
      service = new ConfigurationClient(MOCK_BASE_URL);
    });

    describe("when i call `getConfiguration()`", () => {

      beforeAll(async () => {
        await service.getConfiguration();
      });

      it("should call native fetch with the proper parameters", () => {
        expect(fetchSpy).toHaveBeenCalled();
        expect(fetchSpy).toHaveBeenCalledTimes(1);
        expect(fetchSpy).toHaveBeenCalledWith(
          `${MOCK_BASE_URL}/api/v1/configuration`,
          {
            method: "GET",
            headers: {
              [HeaderNames.Platform]: Environment.platform,
              [HeaderNames.Device]: RuntimeContext.device,
              [HeaderNames.RequestId]: MOCK_GUID,
              [HeaderNames.Accept]: HeaderValueAccept.ApplicationJson,
            }
          }
        );
      });

      it("should parse the response to JSON", () => {
        expect(jsonParseSpy).toHaveBeenCalled();
        expect(jsonParseSpy).toHaveBeenCalledTimes(1);
        expect(jsonParseSpy).toHaveBeenCalledWith(JSON.stringify(MOCK_TEXT));
      });

      it("should get the interceptors", () => {
        expect(getAfterInterceptorsSpy).toHaveBeenCalled();
        expect(getAfterInterceptorsSpy).toHaveBeenCalledTimes(1);
      });

      it("should run each interceptor", () => {
        expect(getAfterInterceptorsSpy).toHaveBeenCalled();
        expect(getAfterInterceptorsSpy).toHaveBeenCalledTimes(1);
      });
    });
  });
});