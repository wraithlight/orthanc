import { Observable } from "knockout";

import { GameCharacter, GameEquipment } from "../../domain";

interface GameCharacterInfoComponentProps {
  character: Observable<GameCharacter>;
  equipment: Observable<GameEquipment>;
}

export class GameCharacterInfoComponent implements GameCharacterInfoComponentProps {
  public readonly character: Observable<GameCharacter>;
  public readonly equipment: Observable<GameEquipment>;

  constructor(params: GameCharacterInfoComponentProps) {
    this.character = params.character;
    this.equipment = params.equipment;
  }

}
