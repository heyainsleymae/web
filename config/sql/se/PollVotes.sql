CREATE TABLE `PollVotes` (
  `UserId` int(10) unsigned NOT NULL,
  `PollItemId` int(10) unsigned NOT NULL,
  `Created` datetime NOT NULL,
  UNIQUE KEY `idxUnique` (`PollItemId`,`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
