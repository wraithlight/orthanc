import { isNil } from "../framework";

export class CookieRepository {

  private buildExpires(validityInDays: number): string {
    const date = new Date();
    date.setTime(date.getTime() + validityInDays * 24 * 60 * 60 * 1000);
    return date.toUTCString();
  }

  public create<T>(
    name: string,
    value: T,
    validityInDays: number
  ): void {
    const expires = this.buildExpires(validityInDays);
    const serialized = encodeURIComponent(JSON.stringify(value));
    document.cookie = `${name}=${serialized}; expires=${expires}; path=/`;
  }

  public getOrDefault<T>(
    name: string,
    defaultValue: T
  ): T {
    const cookie = document.cookie
      .split("; ")
      .map(m => m.split("="))
      .find(([k, v]) => k === name)
    ;

    if (isNil(cookie)) {
      return defaultValue;
    }

    return JSON.parse(decodeURIComponent(cookie[1])) as T;
  }

  public delete(name: string): void {
    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/`;
  }

}