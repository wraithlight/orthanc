import { observable, Subscribable, subscribable } from "knockout";
import { LoginAsGuestEvent, LoginAsMemberEvent } from "../../model/login-events";

interface CharacterCreationContainerParams {
  onBack: Subscribable;
}

export class CharacterCreationContainer implements CharacterCreationContainerParams {
  public onBack: Subscribable;

  constructor(params: CharacterCreationContainerParams) {
    this.onBack = params.onBack;
   }

}
