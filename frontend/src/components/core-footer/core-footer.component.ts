import { getConfig } from "../../state";

interface CoreFooterComponentProps {}

export class CoreFooterComponent implements CoreFooterComponentProps {
  public version: string;

  constructor() {
    this.version = getConfig().version;
  }

}
