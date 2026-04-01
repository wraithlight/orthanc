import { CookieRepository } from "../../repository";

import {
  LOCALE_COOKIE_EXPIRY_DAYS,
  LOCALE_COOKIE_NAME
} from "./locale.const";
import { Locale } from "./locale.type";

export class LocaleService {

  private readonly _cookieRepository = new CookieRepository();

  public setCurrentLocale(locale: Locale): Locale {
    this._cookieRepository.create(
      LOCALE_COOKIE_NAME,
      locale,
      LOCALE_COOKIE_EXPIRY_DAYS
    );
    return locale;
  }

  public getCurrentLocale(
    defaultLocale: Locale
  ): Locale {
    return this._cookieRepository.getOrDefault(
      LOCALE_COOKIE_NAME,
      defaultLocale
    );
  }

}
