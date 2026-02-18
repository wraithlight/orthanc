<?php

class FeedbackEventsState extends BaseState {

  private const FEEDBACK_EVENTS_KEY = "FEEDBACK_EVENTS__FEEDBACK_EVENTS";

  public function initialize(): void {
    $this->writeState(self::FEEDBACK_EVENTS_KEY, []);
  }

  /**
   * @param string $key
   * @param string $label
   * @return State_FeedbackEvent[]
   */
  public function addEvent(string $key, string $label): array {
    $events = $this->readState(self::FEEDBACK_EVENTS_KEY);
    array_push($events, new State_FeedbackEvent($key, $label));
    $this->writeState(self::FEEDBACK_EVENTS_KEY, $events);
    return $this->getEventsCore();
  }

  /**
   * @return State_FeedbackEvent[]
   */
  public function getEvents(): array {
    return $this->getEventsCore();
  }

  /**
   * @return State_FeedbackEvent[]
   */
  private function getEventsCore(): array {
    return $this->readState(self::FEEDBACK_EVENTS_KEY);
  }

}
