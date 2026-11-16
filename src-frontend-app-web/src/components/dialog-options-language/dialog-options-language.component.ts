import { Observable, observable, Subscribable, unwrap } from "knockout";
import { LocaleService, LocalizationService } from "../../services";
import { readFromConfigState } from "../../state";
import { Locale } from "../../services/locale/locale.type";
import { LocalizationRepository } from "../../repository";

interface OrthancOptionsDialogGameModeComponentProps {
  saveSubscription: Subscribable;
}

export class OrthancOptionsDialogGameModeComponent implements OrthancOptionsDialogGameModeComponentProps {

  public readonly saveSubscription: Subscribable;
  public readonly currentLocale: Observable<string>;
  private readonly _localeService = new LocaleService();
  private readonly _localizationService = new LocalizationService();
  private readonly _localizationRepository = LocalizationRepository.getInstance();

  constructor(params: OrthancOptionsDialogGameModeComponentProps) {
    this.saveSubscription = params.saveSubscription;
    this.saveSubscription.subscribe(() => this.onSave());

    const availableLocales = readFromConfigState<ReadonlyArray<string>, ReadonlyArray<string>>(m => m.availableLocales, ["en"]);
    this.currentLocale = observable(this.getCurrentLocale(availableLocales));
  }

  private async onSave(): Promise<void> {
    this._localeService.setCurrentLocale(unwrap(this.currentLocale) as Locale);
    const result = await this._localizationService.getLocalization(unwrap(this.currentLocale) as Locale);
    this._localizationRepository.setLocalization(result);
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
