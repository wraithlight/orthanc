import { subscribable } from "knockout";

import { HeaderNames } from "../domain";
import { readFromConfigState } from "../state";
import { DialogQueueService } from "../services";
import { isNil, Nullable } from "../framework";

let isVersionDialogVisible = false;
export const doVersionCheck = (requiredVersion: Nullable<string>) => {
  if (isVersionDialogVisible) {
    return;
  }
  const platformVersion = requiredVersion;

  if (isNil(platformVersion)) {
    // eslint-disable-next-line no-console
    console.warn(`The header '${HeaderNames.PlatformVersion}' was not present on the response!`);
    return;
  }

  const configVersion = readFromConfigState(m => m.version, platformVersion);

  if (platformVersion !== configVersion) {
    isVersionDialogVisible = true;
    const closeVersionMismatchDialog = new subscribable();
    closeVersionMismatchDialog.subscribe(() => {
      location.reload();
      isVersionDialogVisible = false;
    });
    DialogQueueService.getInstance().openDialog(
      "version-mismatch-dialog",
      "Warning!",
      "You have an outdated version of the game! Some features may not work, please refresh the window to avoid version mismatch issues.",
      [
        {
          id: "cta-refresh",
          label: "Refresh",
          onClick: () => closeVersionMismatchDialog.notifySubscribers()
        }
      ],
      closeVersionMismatchDialog
    )
  }
};
