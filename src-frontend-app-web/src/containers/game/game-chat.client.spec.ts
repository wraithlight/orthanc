import { describe, it, expect, vi, beforeEach } from "vitest";
import { GameChatClient } from "./game-chat.client";
import {
  HeaderNames,
  HeaderValueAccept,
} from "../../domain";
import { Environment } from "../../environment";
import { RuntimeContext } from "../../runtime-context";
import { InterceptorCache } from "../../http";
import * as framework from "../../framework";

vi.mock("../../framework", () => ({
  newGuid: vi.fn(),
}));

describe("GameChatClientSpecs", () => {
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

  it("sendMessage calls fetch and runs interceptors", async () => {
    fetchMock.mockResolvedValue({});

    const client = new GameChatClient("https://api.test");
    await client.sendMessage("hello");

    expect(fetchMock).toHaveBeenCalledWith(
      "https://api.test/api/v1/chat/send",
      {
        method: "POST",
        credentials: "include",
        body: JSON.stringify({ message: "hello" }),
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
  });

  it("poll fetches, parses, runs interceptors, and returns payload", async () => {
    fetchMock.mockResolvedValue({
      text: vi.fn().mockResolvedValue(
        JSON.stringify({
          payload: { messages: ["hi"] },
        })
      ),
    });

    const client = new GameChatClient("https://api.test");
    const result = await client.poll();

    expect(fetchMock).toHaveBeenCalledWith(
      "https://api.test/api/v1/chat/poll",
      {
        method: "GET",
        credentials: "include",
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
    expect(result).toEqual({ messages: ["hi"] });
  });
});
