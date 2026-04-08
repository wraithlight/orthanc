import { Observable } from "knockout";

import { GameCharacter } from "../../domain";

interface GameCharasteristicsComponentProps {
  character: Observable<GameCharacter>;
}

export class GameCharasteristics implements GameCharasteristicsComponentProps {

  public readonly character: Observable<GameCharacter>;

  constructor(params: GameCharasteristicsComponentProps) {
    this.character = params.character;
  }

}
