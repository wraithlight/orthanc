import { HeaderNames } from "../dal-generated";
import { createAfterInterceptor } from "../http";
import { doVersionCheck } from "../version-check";

export const createVersionCheckerInterceptor = () => createAfterInterceptor((res: Response) => {
  const platformVersion = res.headers.get(HeaderNames.X_ORTHANC_PLATFORM_VERSION.toLowerCase());
  doVersionCheck(platformVersion);
});
