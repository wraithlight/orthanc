import { isNil, Nullable } from "../framework";

export class LocalizationRepository {

  private static readonly _instance = new LocalizationRepository();

  public static getInstance(): LocalizationRepository {
    return this._instance;
  }

  private _dictionary: Nullable<Record<string, string>>;

  public setLocalization(
    dictionary: Record<string, string>
  ): void {
    this._dictionary = dictionary;
  }

  public getOrDefault(
    key: string,
    defaultValue: string
  ): string {
    const result = this._dictionary?.[key];
    if (isNil(result)) {
      return defaultValue;
    }
    return result;
  }

}
