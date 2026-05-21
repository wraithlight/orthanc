import { HeaderNames } from "../domain";
import { createAfterInterceptor } from "../http";
import { doVersionCheck } from "../version-check";

export const createVersionCheckerInterceptor = () => createAfterInterceptor((res: Response) => {
  const platformVersion = res.headers.get(HeaderNames.PlatformVersion.toLowerCase());
  doVersionCheck(platformVersion);
});
