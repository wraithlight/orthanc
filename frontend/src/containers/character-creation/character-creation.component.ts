import { observable, Subscribable, subscribable } from "knockout";
import { CharacterCreationClient } from "./character-creation.client";
import { getConfig } from "../../state";

interface CharacterCreationContainerParams {
  onBack: Subscribable;
  onNext: Subscribable;
}

export class CharacterCreationContainer implements CharacterCreationContainerParams {
  public onBack: Subscribable;
  public onNext: Subscribable;
  public onGenerate: Subscribable;
  public readonly stats = observable<{ int: number; dex: number; str: number; con: number; maxHits: number; }>({
    int: 0,
    dex: 0,
    str: 0,
    con: 0,
    maxHits: 0
  });

  private readonly _characterCreationClient = new CharacterCreationClient(getConfig().apiUrl);

  constructor(params: CharacterCreationContainerParams) {
    this.onBack = params.onBack;
    this.onNext = params.onNext;
    this.onGenerate = new subscribable();
    this.onGenerate.subscribe(() => this.onGenerateHandler());
    this.stats
    this._characterCreationClient.generateStats().then(m => {
      this.stats({
        int: m.stats.int,
        dex: m.stats.dex,
        str: m.stats.str,
        con: m.stats.con,
        maxHits: m.maxHits
      });
    });
  }

  public async onGenerateHandler(): Promise<void> {
    await this._characterCreationClient.generateStats().then(m => {
      this.stats({
        int: m.stats.int,
        dex: m.stats.dex,
        str: m.stats.str,
        con: m.stats.con,
        maxHits: m.maxHits
      });
    });
  }

}
