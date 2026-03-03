<?php

class HallOfFameManager
{
  private $_sessionService;
  private $_hallOfFameService;
  private $_levelService;
  private $_userInteractionsService;
  private $_versionService;

  public function __construct()
  {
    $this->_sessionService = new SessionService();
    $this->_hallOfFameService = new HallOfFameService();
    $this->_levelService = new LevelService();
    $this->_userInteractionsService = new UserInteractionsService();
    $this->_versionService = new VersionService();
  }

  public function addUser(
    string $sessionId,
    int $startTime
  ): void {
    $version = $this->_versionService->getVersion();
    $this->_hallOfFameService->addUser(
        $this->_sessionService->getPlayerName(),
        $sessionId,
        $startTime,
        $this->_levelService->getCurrentXp(),
        $this->_levelService->getXpPercentageFromKills(),
        $this->_userInteractionsService->getMoves(),
        $this->_userInteractionsService->getActions(),
        $this->_levelService->getLevel(),
        $version->version . '@' . $version->gitHash,
        $this->_sessionService->getGameMode(),
    );
  }

  public function listHallOfFame(): array {
    return $this->_hallOfFameService->list(10);
  }

}
