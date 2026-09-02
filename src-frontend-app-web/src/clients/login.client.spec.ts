import { describe, it, expect, vi, beforeEach } from "vitest";
import { LoginClient } from "./login.client";
import {
  GameMode,
} from "../domain";
import { Environment } from "../environment";
import { RuntimeContext } from "../runtime-context";
import { InterceptorCache } from "../http";
import * as framework from "../framework";

import { HeaderNames, AcceptValues } from '../dal-generated';

vi.mock("../framework", () => ({
  newGuid: vi.fn(),
}));

describe("LoginClientSpecs", () => {
  const fetchMock = vi.fn();
  const interceptor = vi.fn();

  beforeEach(() => {
    vi.clearAllMocks();

    globalThis.fetch = fetchMock;
    vi.mocked(framework.newGuid).mockReturnValue("guid-123");

    vi.spyOn(InterceptorCache, "getInstance").mockReturnValue({
      getAfterInterceptors: () => [interceptor],
    } as unknown as InterceptorCache);
  });

  it("logs in guest and returns payload", async () => {
    fetchMock.mockResolvedValue({
      text: vi.fn().mockResolvedValue(
        JSON.stringify({
          payload: { username: "guest123" },
        })
      ),
    });

    const client = new LoginClient("https://api.test");
    const result = await client.loginGuest(GameMode.Retail);

    expect(fetchMock).toHaveBeenCalledWith(
      "https://api.test/api/v1/login/guest",
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({
          gameMode: GameMode.Retail,
        }),
        headers: {
          [HeaderNames.PLATFORM]: Environment.platform,
          [HeaderNames.DEVICE]: RuntimeContext.device,
          [HeaderNames.REQUESTID]: "guid-123",
          [HeaderNames.ACCEPTSAPPJSON]:
            AcceptValues.ApplicationJson,
        },
      }
    );

    expect(interceptor).toHaveBeenCalled();
    expect(result).toEqual({ username: "guest123" });
  });
});
