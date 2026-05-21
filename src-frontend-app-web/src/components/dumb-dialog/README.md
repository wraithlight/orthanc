## dumb-dialog component

Usage:

```ts

class Demo {

  public readonly dialogId = observable('dialog-id-here');
  public readonly dialogTitle = observable('Dialog title!');
  public readonly dialogMessage = observable('<h3>Test Dialog</h3>You can put <b>any content</b> here.');
  public readonly dialogCloseSubscription = new subscribable();

  public readonly actions = observableArray([
    {
      id: 'dialog-cta-main',
      label: 'Main CTA',
      onClick: () => this.onMainClick()
    }
  ]);

  public onMainClick(): void {
    alert('Main CTA click!');
    setTimeout(() => this.dialogCloseSubscription.notifySubscribers(), 5_000);
  }

  public onDialogOpen(): boolean {
    console.log("Dialog open!");
    return true;
  }

  public onDialogClose(): void {
    console.log("Dialog close!");
  }

}

```

```html

<orthanc-dialog
  params="{
    id: dialogId,
    title: dialogTitle,
    messageHtml: dialogMessage,
    actions: actions,
    closeSubscription: dialogCloseSubscription,
    onOpen: onDialogOpen,
    onClose=() => onDialogClose()
  }"
>
</orthanc-dialog>


```