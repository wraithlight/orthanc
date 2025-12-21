import { Observable, observable, Subscribable } from "knockout";

interface CharacterCreationComponentParams {
  onBack: Subscribable;
  onNext: Subscribable;
  characterImageUrl: string;
}

export class CharacterCreationComponent implements CharacterCreationComponentParams {
  public readonly onBack: Subscribable;
  public readonly onNext: Subscribable;
  public readonly characterImageUrl: string;

  constructor(params: CharacterCreationComponentParams) {
    this.onBack = params.onBack;
    this.onNext = params.onNext;
    this.characterImageUrl = params.characterImageUrl;
  }

  public onBackClick(): void {
    this.onBack.notifySubscribers();
  }

  public onNextClick(): void {
    this.onNext.notifySubscribers();
  }

}
