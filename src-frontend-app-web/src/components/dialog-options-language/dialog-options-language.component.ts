import { Observable, observable, Subscribable, unwrap } from "knockout";
import { LocaleService } from "../../services";
import { readFromConfigState } from "../../state";
import { Locale } from "../../services/locale/locale.type";

interface OrthancOptionsDialogGameModeComponentProps {
  saveSubscription: Subscribable;
}

export class OrthancOptionsDialogGameModeComponent implements OrthancOptionsDialogGameModeComponentProps {

  public readonly saveSubscription: Subscribable;
  public readonly currentLocale: Observable<string>;
  private readonly _localeService = new LocaleService();

  constructor(params: OrthancOptionsDialogGameModeComponentProps) {
    this.saveSubscription = params.saveSubscription;
    this.saveSubscription.subscribe(() => this.onSave());

    const availableLocales = readFromConfigState<ReadonlyArray<string>, ReadonlyArray<string>>(m => m.availableLocales, ["en"]);
    this.currentLocale = observable(this.getCurrentLocale(availableLocales));
    this.currentLocale.subscribe(m => console.log(m));
  }

  private onSave(): void {
    this._localeService.setCurrentLocale(unwrap(this.currentLocale) as Locale);
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
