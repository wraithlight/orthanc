import { describe, it, expect, vi, beforeEach } from "vitest";
import { LoginClient } from "./login.client";
import {
  GameMode,
  HeaderNames,
  HeaderValueAccept,
} from "../domain";
import { Environment } from "../environment";
import { RuntimeContext } from "../runtime-context";
import { InterceptorCache } from "../http";
import * as framework from "../framework";

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
          [HeaderNames.Platform]: Environment.platform,
          [HeaderNames.Device]: RuntimeContext.device,
          [HeaderNames.RequestId]: "guid-123",
          [HeaderNames.Accept]:
            HeaderValueAccept.ApplicationJson,
        },
      }
    );

    expect(interceptor).toHaveBeenCalled();
    expect(result).toEqual({ username: "guest123" });
  });
});
