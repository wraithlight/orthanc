import ko, { Observable, ObservableArray, unwrap } from "knockout";

import { readFromConfigState } from "../../state";
// TODO: Export this properly or move to domain.
import { Locale } from "../../services/locale/locale.type";

interface CommonLanguageSelectorComponentProps {
  currentLocale: Observable<Locale>;
}

export class CommonLanguageSelectorComponent implements CommonLanguageSelectorComponentProps {

  public readonly currentLocale: Observable<Locale>;
  public readonly availableLocales: ObservableArray<any>;

  constructor(params: CommonLanguageSelectorComponentProps) {
    const availableLocales = readFromConfigState<ReadonlyArray<string>, ReadonlyArray<string>>(m => m.availableLocales, ["en"])  
    this.currentLocale = params.currentLocale;
    this.availableLocales = ko.observableArray([...availableLocales.map(m => ({
      value: m,
      label: m
    }))]);

    this.currentLocale.subscribe(m => this.setCurrentLocale(m));
  }

  public setCurrentLocale(locale: string): void {
    // TODO: Typeguard for locale.
    if (!unwrap(this.availableLocales).includes(locale)) {
      return;
    }
    this.currentLocale(locale as Locale);
  }


}
