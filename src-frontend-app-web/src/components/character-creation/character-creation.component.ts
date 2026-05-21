import { Observable, Subscribable } from "knockout";

interface CharacterCreationComponentParams {
  onBack: Subscribable;
  onNext: Subscribable;
  onGenerate: Subscribable;
  stats: Observable<{
    int: number;
    dex: number;
    str: number;
    con: number;
    maxHits: number;
  }>;
}

export class CharacterCreationComponent implements CharacterCreationComponentParams {
  public readonly onBack: Subscribable;
  public readonly onNext: Subscribable;
  public readonly onGenerate: Subscribable;
  public readonly stats: Observable<{ int: number; dex: number; str: number; con: number; maxHits: number; }>;

  constructor(params: CharacterCreationComponentParams) {
    this.onBack = params.onBack;
    this.onNext = params.onNext;
    this.onGenerate = params.onGenerate;
    this.stats = params.stats;
  }

  public onBackClick(): void {
    this.onBack.notifySubscribers();
  }

  public onNextClick(): void {
    this.onNext.notifySubscribers();
  }

  public onGenerateClick(): void {
    this.onGenerate.notifySubscribers();
  }

}
