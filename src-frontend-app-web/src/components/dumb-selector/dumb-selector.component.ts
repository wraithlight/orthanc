import { Observable, ObservableArray } from "knockout";

interface OrthancSelectorOption {
  value: string;
  label: string;
}

interface OrthancSelectorComponentParams {
  label: string;
  name: string;
  value: Observable<string>;
  options: ObservableArray<OrthancSelectorOption>;
}

export class OrthancSelectorComponent implements OrthancSelectorComponentParams {

  public readonly label: string;
  public readonly name: string;
  public readonly value: Observable<string>;
  public readonly options: ObservableArray<OrthancSelectorOption>;

  constructor(params: OrthancSelectorComponentParams) {
    this.label = params.label;
    this.name = params.name;
    this.value = params.value;
    this.options = params.options;
  }

  public onChange(value: string): void {
    this.value(value);
  }

}
