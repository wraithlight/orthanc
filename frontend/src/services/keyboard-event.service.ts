import { KeyboardEventType } from "./keyboard-event.type";

export class KeyboardEventService {

  private _isEmitEnabled = true;
  private _listeners: Record<string, () => void> = {};
  private static _instance: KeyboardEventService;

  private constructor() {
    document.addEventListener("keydown", (event: KeyboardEvent) => {
      if (!this._isEmitEnabled) return;
      const handler = this._listeners[event.code.toLowerCase()];
      handler && handler();
    });
  }

  public static getInstance(): KeyboardEventService {
    if (!this._instance) {
      this._instance = new KeyboardEventService();
    }
    return this._instance;
  }

  public disableEmit(): void {
    this._isEmitEnabled = false;
  }

  public enableEmit(): void {
    this._isEmitEnabled = true;
  }

  public subscribe(
    key: KeyboardEventType,
    callback: () => void
  ): void {
    this._listeners[key.toLowerCase()] = callback;
  }

}
