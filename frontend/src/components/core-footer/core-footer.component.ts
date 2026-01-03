import { getConfig } from "../../state";

interface CoreFooterComponentProps { }

export class CoreFooterComponent implements CoreFooterComponentProps {
  public version: string;
  public environment: string;

  constructor() {
    this.version = getConfig().version;
    this.environment = getConfig().environment;
  }

}
