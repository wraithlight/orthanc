import { Observable, observable, unwrap } from "knockout";
import { isNil, Nullable } from "../framework";

export class LocalizationRepository {

  private static readonly _instance = new LocalizationRepository();

  public static getInstance(): LocalizationRepository {
    return this._instance;
  }

  private _dictionary = observable<Nullable<Record<string, string>>>();

  public setLocalization(
    dictionary: Record<string, string>
  ): void {
    this._dictionary(dictionary);
  }

  public getOrDefault(
    key: string,
    defaultValue: string
  ): Observable<string> {
    const result = unwrap(this._dictionary)?.[key];
    if (isNil(result)) {
      return observable(defaultValue);
    }
    return observable(result);
  }

}
