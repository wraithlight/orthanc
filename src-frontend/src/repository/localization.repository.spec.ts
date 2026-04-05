import { LocalizationRepository } from "./localization.repository";
import { unwrap } from "knockout";

import { describe, it, expect, beforeEach } from "vitest";

describe("LocalizationRepositorySpecs", () => {

  let repo: LocalizationRepository;

  beforeEach(() => {
    repo = LocalizationRepository.getInstance();
    repo.setLocalization({}); 
  });

  it("should return the same singleton instance", () => {
    const instance1 = LocalizationRepository.getInstance();
    const instance2 = LocalizationRepository.getInstance();

    expect(instance1).toBe(instance2);
  });

  it("should return default value when dictionary is empty", () => {
    const result = repo.getOrDefault("missing", "default");

    expect(unwrap(result)).toBe("default");
  });

  it("should return default value when key does not exist", () => {
    repo.setLocalization({ hello: "world" });

    const result = repo.getOrDefault("missing", "default");

    expect(unwrap(result)).toBe("default");
  });

  it("should return value when key exists", () => {
    repo.setLocalization({ hello: "world" });

    const result = repo.getOrDefault("hello", "default");

    expect(unwrap(result)).toBe("world");
  });

  it("should overwrite dictionary when setLocalization is called again", () => {
    repo.setLocalization({ hello: "world" });
    repo.setLocalization({ hello: "new" });

    const result = repo.getOrDefault("hello", "default");

    expect(unwrap(result)).toBe("new");
  });

});