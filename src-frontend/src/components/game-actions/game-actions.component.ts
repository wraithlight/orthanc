import { Observable, ObservableArray, Subscribable } from "knockout";

interface GameActionsComponentProps {
  actions: ObservableArray;
  maxHits: Observable<number>;
  curHits: Observable<number>;
  onActionItemClick: Subscribable<{ key: string, payload: string }>;
}

export class GameActionsComponent implements GameActionsComponentProps {

  public readonly actions: ObservableArray<any>;
  public readonly maxHits: Observable<number>;
  public readonly curHits: Observable<number>;
  public readonly onActionItemClick: Subscribable<{ key: string; payload: string; }>;

  constructor(params: GameActionsComponentProps) {
    this.actions = params.actions;
    this.maxHits = params.maxHits;
    this.curHits = params.curHits;
    this.onActionItemClick = params.onActionItemClick;
  }

  public onActionItemClickEvent(
    key: string,
    payload: string
  ): void {
    this.onActionItemClick.notifySubscribers({ key, payload });
  }

}
