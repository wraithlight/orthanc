import { Computed, computed, Observable, observable } from "knockout";

export class DeviceService {

  private static _instance = new DeviceService();
  private readonly _isMobile = observable(false);

  public static getInstance(): DeviceService {
    return this._instance;
  }

  private constructor() {
    const media = window.matchMedia("(max-width: 768px)");
    this._isMobile(media.matches);
    media.addEventListener("change", e => {
      this._isMobile(e.matches);
    });
  }

  public isMobile(): boolean {
    return this._isMobile();
  }

  public isDesktop(): boolean {
    return !this._isMobile();
  }

  public isMobile$(): Observable<boolean> {
    return this._isMobile;
  }

  public isDesktop$(): Computed<boolean> {
    return computed(() => !this._isMobile());
  }

}
