import { Observable } from "knockout";

import { CharacterGameStats } from "../../domain";

interface GameStatisticsComponentProps {
  stats: Observable<CharacterGameStats>;
}

export class GameStatisticsComponent implements GameStatisticsComponentProps {

  public readonly stats: Observable<CharacterGameStats>;

  constructor(props: GameStatisticsComponentProps) {
    this.stats = props.stats;
  }
}
