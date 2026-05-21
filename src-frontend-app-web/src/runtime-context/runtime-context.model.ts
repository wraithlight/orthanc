import { Device, GameMode } from "../domain";

export interface IRuntimeContext {
  device: Device;
  gameMode: GameMode;
}
