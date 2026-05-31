import { beforeAll, beforeEach, describe, expect, it, vi } from "vitest";

import { GameMode } from "../domain";
import { Nullable } from "../framework";

const { ctorSpy, postOrthancSpy, MockOrthancClient } = vi.hoisted(() => {
  const ctorSpy = vi.fn()
  const postOrthancSpy = vi.fn()

  class MockOrthancClient {
    constructor(...args: any[]) {
      ctorSpy(...args)
    }

    postOrthanc(...args: any[]) {
      return postOrthancSpy(...args)
    }
  }

  return {
    MockOrthancClient,
    ctorSpy,
    postOrthancSpy,
  }
});

vi.mock('./internal', () => ({
  OrthancHttpClient: MockOrthancClient,
}));


import { LoginClient } from "./login.client";

const MOCK_GAME_MODE = GameMode.Retail;
const MOCK_BASE_URL = "http://base.api";

describe("LoginClientSpecs", () => {
  let service: LoginClient;
  describe("given the client is initalized", () => {
    beforeAll(() => {
      service = new LoginClient(MOCK_BASE_URL);
    });

    it("should create the base service with the given baseurl", () => {
      expect(ctorSpy).toHaveBeenCalled();
      expect(ctorSpy).toHaveBeenCalledTimes(1);
      expect(ctorSpy).toHaveBeenCalledWith(MOCK_BASE_URL);
    });

    describe("when i call `getCredentialsMode()`", () => {
      let mode: Nullable<string>;
      beforeEach(() => {
        mode = service['getCredentialsMode']();
      });
      it("must be `include`", () => {
        expect(mode).toBe("include");
      });
    });

    describe("when i call `loginGuest()`", () => {
      beforeAll(async () => {
        await service.loginGuest(MOCK_GAME_MODE);
      });
      it("should call the underlying `postOrhtanc()` method", () => {
        expect(postOrthancSpy).toHaveBeenCalled();
        expect(postOrthancSpy).toHaveBeenCalledTimes(1);
      });
    });

  });
});
