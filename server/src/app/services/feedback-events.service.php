<?php

class FeedbackEventsService {

  private $_feedbackEventsState;

  public function __construct() {
    $this->_feedbackEventsState = new FeedbackEventsState();
  }

  /**
   * @return void
   */
  public function startRound(): void {
    $this->_feedbackEventsState->initialize();
  }

  /**
   * @param string $key
   * @param string $label
   * @return State_FeedbackEvent[]
   */
  public function addEvent(string $key, string $label): array {
    return $this->_feedbackEventsState->addEvent($key, $label);
  }

  /**
   * @return State_FeedbackEvent[]
   */
  public function getEvents(): array {
    return $this->_feedbackEventsState->getEvents();
  }

}
