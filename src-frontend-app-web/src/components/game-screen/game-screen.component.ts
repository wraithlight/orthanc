import { ObservableArray, observableArray } from "knockout";

interface GameScreenComponentProps {
  tiles: ObservableArray<unknown>;
  endgameItemIcons: ObservableArray<unknown>;
}

export class GameScreenComponent implements GameScreenComponentProps {
  public readonly tiles: ObservableArray<unknown>;
  public readonly endgameItemIcons: ObservableArray<unknown>;

  constructor(props: GameScreenComponentProps) {
    this.tiles = props.tiles;
    this.endgameItemIcons = props.endgameItemIcons;
  }
}
