## dumb-selector component

Usage:

```ts

import { observable, observableArray } from "knockout";

class Demo {

  public defaultValue = observable('Vanilla');
  public options = observableArray([
    {
      value: 'Retail',
      label: 'RETAIL'
    },
    {
      value: this.defaultValue(),
      label: 'VANILLA'
    }
  ]);

  constructor() {
    this.options.subscribe(() => {});
  }

}

```

```html

<orthanc-selector
  params="{
    label: 'mode',
    name: 'game-mode',
    value: defaultValue,
    options: options
  }"
></orthanc-selector>
<span data-bind="text: defaultValue"></span>

```