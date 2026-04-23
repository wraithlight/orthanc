import { observable, Observable, Subscribable } from "knockout";

import { NonEmptyArray } from "../../framework";

interface OrthancDialogAction {
  id: string;
  label: string;
  onClick: () => void;
}

interface OrthancDialogComponentParams {
  id: Observable<string>;
  title: Observable<string>;
  messageHtml: Observable<string>;
  actions: NonEmptyArray<OrthancDialogAction>;
  closeSubscription: Subscribable<void>;
  onOpen?: () => boolean;
  onClose?: () => void;
  type: Observable<"HTML" | "COMPONENT">;
  componentSelector: Observable<string>;
  componentPayload: Observable<any>;
}

export class OrthancDialogComponent implements OrthancDialogComponentParams {
  public isDialogVisible = observable(false);

  public readonly id: Observable<string>;
  public readonly title: Observable<string>;
  public readonly messageHtml: Observable<string>;
  public readonly actions: NonEmptyArray<OrthancDialogAction>;
  public readonly onOpen?: (() => boolean) | undefined;
  public readonly onClose?: (() => void) | undefined;
  public readonly closeSubscription: Subscribable<void>;
  public readonly type: Observable<"HTML" | "COMPONENT">;
  public readonly componentSelector: Observable<string>;
  public readonly componentPayload: Observable<any>;

  constructor(params: OrthancDialogComponentParams) {
    this.id = params.id;
    this.title = params.title;
    this.messageHtml = params.messageHtml;
    this.actions = params.actions;
    this.onOpen = params.onOpen;
    this.onClose = params.onClose;
    this.closeSubscription = params.closeSubscription;
    this.type = params.type;
    this.componentSelector = params.componentSelector;
    this.componentPayload = params.componentPayload;

    this.openDialog();
    this.closeSubscription.subscribe(() => {
      this.isDialogVisible(false);
      this.onClose ? this.onClose() : undefined;
    });
  }

  private openDialog(): void {
    if (this.onOpen) {
      this.isDialogVisible(this.onOpen());
    } else {
      this.isDialogVisible(true);
    }
  }

}
