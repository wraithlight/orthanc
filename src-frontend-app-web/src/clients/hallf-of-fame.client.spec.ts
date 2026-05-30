import { describe, it, expect, vi, beforeAll } from "vitest";

import { Environment } from "../environment";
import { GameMode, HeaderNames, HeaderValueAccept } from "../domain";
import { RuntimeContext } from "../runtime-context";

import { HallOfFameClient } from "./hall-of-fame.client";

const MOCK_TEXT = { prop: "value" };

const MOCK_BASE_URL = "http://base.api";
const MOCK_GAME_MODE = GameMode.Retail;
const MOCK_GUID = "mock-guid";
const MOCK_RESPONSE = {
  text: async () => JSON.stringify(MOCK_TEXT)
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

describe("HallOfFameClientSpecs", () => {
  let service: HallOfFameClient;
  describe("given the client is initalized", () => {
    beforeAll(() => {
      service = new HallOfFameClient(MOCK_BASE_URL);
    });

    describe("when i call `getHallOfFame()`", () => {

      beforeAll(async () => {
        await service.getHallOfFame(MOCK_GAME_MODE);
      });

      it("should call native fetch with the proper parameters", () => {
        expect(fetchSpy).toHaveBeenCalled();
        expect(fetchSpy).toHaveBeenCalledTimes(1);
        expect(fetchSpy).toHaveBeenCalledWith(
          `${MOCK_BASE_URL}/api/v1/main/hall-of-fame?game-mode=${MOCK_GAME_MODE}`,
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