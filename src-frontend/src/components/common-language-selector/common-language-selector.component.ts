import ko, { Observable, Subscribable, unwrap } from "knockout";

import { readFromConfigState } from "../../state";
import { LocaleService } from "../../services";

interface CommonLanguageSelectorComponentProps {
  onLocaleChange: Subscribable<string>;
}

export class CommonLanguageSelectorComponent implements CommonLanguageSelectorComponentProps {

  public readonly currentLocale: Observable;
  public readonly availableLocales: Observable;
  public readonly onLocaleChange: Subscribable<string>;

  private readonly _localeService = new LocaleService();

  constructor(params: CommonLanguageSelectorComponentProps) {
    this.onLocaleChange = params.onLocaleChange;

    const availableLocales = readFromConfigState<ReadonlyArray<string>, ReadonlyArray<string>>(m => m.availableLocales, ["en"])  
    const currentLocale = this.getCurrentLocale(availableLocales);
    this.currentLocale = ko.observable(currentLocale);
    this.availableLocales = ko.observable(availableLocales);
  }

  public setCurrentLocale(locale: string): void {
    if (!unwrap(this.availableLocales).includes(locale)) {
      return;
    }
    this._localeService.setCurrentLocale(locale as any);
    this.currentLocale(locale);
    this.onLocaleChange.notifySubscribers(locale);
  }

  private getCurrentLocale(
    allowedLocales: ReadonlyArray<string>
  ): string {
    const configDefaultLocale = readFromConfigState(m => m.featureStates.applicationDefaultLanguageDefault, allowedLocales[0]! as any);
    const cookieLocale = this._localeService.getCurrentLocale(configDefaultLocale as any);

    const currentLocale = allowedLocales.includes(cookieLocale)
      ? cookieLocale
      : configDefaultLocale
    ;

    return currentLocale as string;
  }

}
