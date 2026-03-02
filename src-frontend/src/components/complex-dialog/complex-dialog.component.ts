import { observable, Subscribable } from "knockout";

import { DialogQueueService } from "../../services";

interface OrthancDialogsComponentParams { }

export class OrthancDialogsComponent implements OrthancDialogsComponentParams {

  public readonly currentDialog = observable();
  private readonly _dialogQueueService = DialogQueueService.getInstance();

  constructor() {
    this._dialogQueueService.getDialogQueue().subscribe(m => {
      if (!m[0]) {
        this.currentDialog(undefined);
      }

      if (this.currentDialog() !== m[0]) {
        this.currentDialog(m[0]);
      }
    });
  }

}
