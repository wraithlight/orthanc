import { AfterInterceptor } from "./after-interceptor.type";
import { BeforeInterceptor } from "./before-interceptor.type";

export class InterceptorCache {

  private static readonly _instance = new InterceptorCache();

  public static getInstance(): InterceptorCache {
    return this._instance;
  }
  
  private _afterInterceptors: Array<AfterInterceptor> = [];
  private _beforeInterceptors: Array<BeforeInterceptor> = [];

  private constructor() {}

  public addAfterInterceptor<T>(interceptor: AfterInterceptor): void {
    this._afterInterceptors.push(interceptor);
  }

  public getAfterInterceptors(): ReadonlyArray<AfterInterceptor> {
    return this._afterInterceptors;
  }

  public addBeforeInterceptor<T>(interceptor: BeforeInterceptor): void {
    this._beforeInterceptors.push(interceptor);
  }

  public getBeforeInterceptors(): ReadonlyArray<BeforeInterceptor> {
    return this._beforeInterceptors;
  }

}
