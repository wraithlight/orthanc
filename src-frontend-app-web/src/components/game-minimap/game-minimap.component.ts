import { ObservableArray } from "knockout";

interface GameMinimapComponentParams {
  minimapState: ObservableArray<ReadonlyArray<unknown>>;
}

export class GameMinimapComponent implements GameMinimapComponentParams {

  public readonly minimapState: ObservableArray<ReadonlyArray<unknown>>;

  constructor(params: GameMinimapComponentParams) {
    this.minimapState = params.minimapState;

    this.minimapState.subscribe(() => this.renderMinimap());
  }


  private renderMinimap() {
    const canvas = document.getElementById('minimapCanvas') as HTMLCanvasElement;
    const ctx = canvas.getContext('2d')!;
    canvas.width = canvas.clientWidth;
    canvas.height = canvas.clientHeight;
    ctx!.imageSmoothingEnabled = false;
    ctx!.fillStyle = 'black';
    ctx!.fillRect(0, 0, canvas.width, canvas.height);

    const rows = this.minimapState().length;
    const cols = this.minimapState()[0].length;

    const cellWidth = Math.floor(canvas.width / cols);
    const cellHeight = Math.floor(canvas.height / rows);

    const primaryColor = window.getComputedStyle(document.body).getPropertyValue('--color-primary');
    for (let y = 0; y < rows; y++) {
      for (let x = 0; x < cols; x++) {
        if (this.minimapState()[y][x] === 'WALL') ctx.fillStyle = primaryColor;
        else if (this.minimapState()[y][x] === 'PLAYER') ctx.fillStyle = '#FF0000';
        else ctx.fillStyle = '#000000';

        ctx.fillRect(x * cellWidth, y * cellHeight, cellWidth, cellHeight);

        if (this.minimapState()[y][x] === 'EMPTY') {
          ctx.fillStyle = primaryColor;
          ctx.fillRect(x * cellWidth + cellWidth / 3, y * cellHeight + cellHeight / 3, cellWidth / 3, cellHeight / 3);
        }
      }
    }
  }

}
