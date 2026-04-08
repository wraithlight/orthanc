import { AfterInterceptor } from "./after-interceptor.type";
import { InterceptorCache } from "./interceptor.cache";

export function createAfterInterceptor(
  interceptor: AfterInterceptor
) {
  InterceptorCache.getInstance().addAfterInterceptor(interceptor);
}
