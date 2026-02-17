import { State } from "../../state";

interface CoreFooterComponentProps { }

export class CoreFooterComponent implements CoreFooterComponentProps {
  public version: string;
  public environment: string;

  constructor() {
    this.version = State.config()!.version;
    this.environment = State.config()!.environment;
  }

}
