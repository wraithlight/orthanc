import { describe, it, expect, vi, beforeEach, afterEach } from "vitest";

import { GameMode, HallOfFameListModel } from "../domain";

import { HallOfFameService } from "./hall-of-fame.service";

vi.mock("../environment", () => ({
  Environment: {
    apiBaseUrl: "http://mock-api"
  }
}));

const getHallOfFameSpy = vi.fn();
vi.mock("../clients", () => {
  return {
    HallOfFameClient: class {
      getHallOfFame = getHallOfFameSpy
      constructor(_baseUrl: string) {
      }
    }
  };
});

describe("HallOfFameService", () => {
  let service: HallOfFameService;

  beforeEach(() => {
    service = new HallOfFameService();
  });
  afterEach(() => {
    vi.resetAllMocks();
  })

  it("should call getHallOfFame on the client with the provided game mode", async () => {
    const mockData: HallOfFameListModel = {
      items: [
        {
          name: "Alice",
          level: 42,
          sessionStartAtUtc: 1677628800000,
          sessionEndAtUtc: 1677632400000,
          sessionLengthInMs: 3600 * 1000,
          experiencePoints: 15000,
          experienceFromKillsPercentage: 65,
          gameVersion: "1.3.2",
          numberOfMoves: 120,
          numberOfActions: 45,
          gameMode: GameMode.Retail
        }
      ]
    };

    getHallOfFameSpy.mockResolvedValue(mockData);

    const result = await service.fetchHallOfFame(GameMode.Retail);

    expect(getHallOfFameSpy).toHaveBeenCalledTimes(1);
    expect(getHallOfFameSpy).toHaveBeenCalledWith(GameMode.Retail);
    expect(result).toEqual(mockData);
  });

  it("should propagate errors from the client", async () => {
    const error = new Error("Network error");
    getHallOfFameSpy.mockRejectedValue(error);

    await expect(service.fetchHallOfFame(GameMode.Retail)).rejects.toThrow("Network error");
    expect(getHallOfFameSpy).toHaveBeenCalledTimes(1);
  });
});
