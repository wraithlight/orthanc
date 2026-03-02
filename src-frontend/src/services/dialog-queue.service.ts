import { observable, observableArray, Subscribable } from "knockout";

import { NonEmptyArray } from "../framework";

export class DialogQueueService {

  private static _instance = new DialogQueueService();

  public static getInstance(): DialogQueueService {
    return this._instance;
  }

  private _dialogQueue = observableArray<any>([]);

  private constructor() {}

  public openDialog(
    id: string,
    title: string,
    messageHtml: string,
    actions: NonEmptyArray<{
      id: string,
      label: string,
      onClick: () => void
    }>,
    closeSubscription: Subscribable<void>,
    onOpen?: () => boolean,
    onClose?: () => void,
  ): void {
    this._dialogQueue.push({
      id: observable(id),
      title: observable(title),
      messageHtml: observable(messageHtml),
      actions: observableArray(actions),
      closeSubscription: closeSubscription,
      onOpen: onOpen,
      onClose: () => this.onDialogClose(onClose)
    });
  }

  public getDialogQueue() {
    return this._dialogQueue;
  }

  private onDialogClose(closeFn?: () => void): void {
    this._dialogQueue.shift();
    closeFn ? closeFn() : undefined;
  }

}
