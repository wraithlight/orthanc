import { Observable } from "knockout";

import { GameEquipment } from "../../domain";

interface GameEquipmentComponentProps {
  equipment: Observable<GameEquipment>;
}

export class GameEquipmentComponent implements GameEquipmentComponentProps {

  public readonly equipment: Observable<GameEquipment>;

  constructor(params: GameEquipmentComponentProps) {
    this.equipment = params.equipment;
  }

}
