import { AfterInterceptor } from "./after-interceptor.type";

export class InterceptorCache {

  private static readonly _instance = new InterceptorCache();

  public static getInstance(): InterceptorCache {
    return this._instance;
  }
  
  private _afterInterceptors: Array<AfterInterceptor> = [];

  private constructor() {}

  public addAfterInterceptor<T>(interceptor: AfterInterceptor): void {
    this._afterInterceptors.push(interceptor);
  }

  public getAfterInterceptors(): ReadonlyArray<AfterInterceptor> {
    return this._afterInterceptors;
  }

}
