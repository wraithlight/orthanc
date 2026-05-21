import { ObservableArray } from "knockout";

interface GameSpellsComponentParams {
  activeSpells: ObservableArray<ReadonlyArray<unknown>>;
}

export class GameSpellsComponent implements GameSpellsComponentParams {

  public readonly activeSpells: ObservableArray<ReadonlyArray<unknown>>;

  constructor(params: GameSpellsComponentParams) {
    this.activeSpells = params.activeSpells;
  }

}
