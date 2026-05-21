import { describe, it, expect, vi, beforeEach } from "vitest";
import { CookieRepository } from "../../repository";
import { LocaleService } from "./locale.service";
import {
  LOCALE_COOKIE_NAME,
  LOCALE_COOKIE_EXPIRY_DAYS
} from "./locale.const";
import type { Locale } from "./locale.type";

describe("LocaleService", () => {
  let service: LocaleService;
  let repo: CookieRepository;

  beforeEach(() => {
    service = new LocaleService();

    repo = (service as any)._cookieRepository;

    vi.spyOn(repo, "create");
    vi.spyOn(repo, "getOrDefault");
  });

  it("setCurrentLocale should call repository.create with correct args", () => {
    const locale: Locale = "en";

    service.setCurrentLocale(locale);

    expect(repo.create).toHaveBeenCalledWith(
      LOCALE_COOKIE_NAME,
      locale,
      LOCALE_COOKIE_EXPIRY_DAYS
    );
  });

  it("setCurrentLocale should return the input locale", () => {
    const locale: Locale = "en";

    const result = service.setCurrentLocale(locale);

    expect(result).toBe(locale);
  });

  it("getCurrentLocale should call repository.getOrDefault with correct args", () => {
    vi.mocked(repo.getOrDefault).mockReturnValue("en");

    service.getCurrentLocale("en");

    expect(repo.getOrDefault).toHaveBeenCalledWith(
      LOCALE_COOKIE_NAME,
      "en"
    );
  });

  it("getCurrentLocale should return repository value", () => {
    const expectedLocale: Locale = "hu";

    vi.mocked(repo.getOrDefault).mockReturnValue(expectedLocale);

    const result = service.getCurrentLocale("en");

    expect(result).toBe(expectedLocale);
  });
});