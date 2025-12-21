import { observable, Subscribable, subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";

interface CharacterCreationContainerParams {
  onBack: Subscribable;
  onNext: Subscribable;
  characterImageUrl: string;
}

export class CharacterCreationContainer implements CharacterCreationContainerParams {
  public onBack: Subscribable;
  public onNext: Subscribable;
  public characterImageUrl: string;

  constructor(params: CharacterCreationContainerParams) {
    this.onBack = params.onBack;
    this.onNext = params.onNext;
    this.characterImageUrl = params.characterImageUrl;
  }

}
