<?php
enum GameState: string {
  case EndFail = "GAME_END_FAIL";
  case EndSuccess = "GAME_END_SUCCESS";
  case Running = "GAME_RUNNING";
}
