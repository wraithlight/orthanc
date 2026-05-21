import { Observable, observable, subscribable, Subscribable, unwrap } from "knockout";
import { isNil } from "../../framework";

interface OrthancOptionsDialogComponentProps {
  closeSubscription: Subscribable;
}

const createKey = (label: string) => {
  return label.toLowerCase().split(" ").join("-");
};

const createSelector = (label: string) => {
  const key = createKey(label);
  return `orthanc-dialog-options-${key}`;
}

const createPath = (label: string) => ({
  label: label,
  key: createKey(label),
  selector: createSelector(label)
});

export class OrthancOptionsDialogComponent implements OrthancOptionsDialogComponentProps {
  public readonly items = [
    createPath("Language"),
    createPath("Game Mode")
  ];
  public readonly activeItem = observable(this.items[0]);
  public readonly saveSubscription = new subscribable();

  public readonly closeSubscription: Subscribable;

  constructor(params: Observable<OrthancOptionsDialogComponentProps>) {
    const uParams = unwrap(params);
    this.closeSubscription = uParams.closeSubscription;
  }

  public onMenuItemClick(key: string): void {
    const itemLike = this.items.find(m => m.key === key);
    if (isNil(itemLike)) return;

    this.activeItem(itemLike);
  }

  public onCancelClick(): void {
    this.closeDialog();
  }

  public onSaveAndCloseClick(): void {
    // TODO: This is memleak at all, because the components wont unsubscribe on destroy event.
    this.saveSubscription.notifySubscribers();
    this.closeDialog();
  }

  private closeDialog(): void {
    this.closeSubscription.notifySubscribers();
  }

}
