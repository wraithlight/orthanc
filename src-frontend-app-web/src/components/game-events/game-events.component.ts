import { ObservableArray } from "knockout";

interface GameEventsComponentParams {
  events: ObservableArray<ReadonlyArray<unknown>>;
}

export class GameEventsComponent implements GameEventsComponentParams {

  public readonly events: ObservableArray<ReadonlyArray<unknown>>;

  constructor(params: GameEventsComponentParams) {
    this.events = params.events;
  }


}
