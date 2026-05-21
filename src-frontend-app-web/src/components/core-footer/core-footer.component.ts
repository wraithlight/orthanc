import { Environment } from "../../environment";
import { readFromConfigState } from "../../state";

interface CoreFooterComponentProps { }

export class CoreFooterComponent implements CoreFooterComponentProps {
  public version: string;
  public environment: string;

  constructor() {
    this.version = readFromConfigState(m => m.version, "CANNOT_DETERMINE");
    this.environment = Environment.environmentName;
  }

}
