import { BeforeInterceptor } from "./before-interceptor.type";
import { InterceptorCache } from "./interceptor.cache";

export function createBeforeInterceptor(
  interceptor: BeforeInterceptor
) {
  InterceptorCache.getInstance().addBeforeInterceptor(interceptor);
}
