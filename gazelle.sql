SET NAMES utf8;
SET FOREIGN_KEY_CHECKS = 0;

USE gazelle;

-- ----------------------------
-- Table structure for activity
-- ----------------------------
DROP TABLE IF EXISTS `activity`;
CREATE TABLE `activity`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Text` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Display` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for api_applications
-- ----------------------------
DROP TABLE IF EXISTS `api_applications`;
CREATE TABLE `api_applications`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `UserID` int(10) NOT NULL,
  `Token` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for api_users
-- ----------------------------
DROP TABLE IF EXISTS `api_users`;
CREATE TABLE `api_users`  (
  `UserID` int(10) NOT NULL,
  `AppID` int(10) NOT NULL,
  `Token` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `State` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `Access` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`UserID`, `AppID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for applicant
-- ----------------------------
DROP TABLE IF EXISTS `applicant`;
CREATE TABLE `applicant`  (
  `ID` int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `RoleID` int(4) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  `ThreadID` int(6) UNSIGNED NOT NULL,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Created` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Modified` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Resolved` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `RoleID`(`RoleID`) USING BTREE,
  INDEX `ThreadID`(`ThreadID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  CONSTRAINT `applicant_ibfk_1` FOREIGN KEY (`RoleID`) REFERENCES `applicant_role` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `applicant_ibfk_2` FOREIGN KEY (`ThreadID`) REFERENCES `thread` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `applicant_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for applicant_role
-- ----------------------------
DROP TABLE IF EXISTS `applicant_role`;
CREATE TABLE `applicant_role`  (
  `ID` int(4) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Title` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Published` tinyint(4) NOT NULL DEFAULT 0,
  `Description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Created` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Modified` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  CONSTRAINT `applicant_role_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for apply_question
-- ----------------------------
DROP TABLE IF EXISTS `apply_question`;
CREATE TABLE `apply_question`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `iterm_id` bigint(20) NOT NULL,
  `sort` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apply_question_answer
-- ----------------------------
DROP TABLE IF EXISTS `apply_question_answer`;
CREATE TABLE `apply_question_answer`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `iterm_id` bigint(20) DEFAULT NULL,
  `answer` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `remark` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apply_question_iterm
-- ----------------------------
DROP TABLE IF EXISTS `apply_question_iterm`;
CREATE TABLE `apply_question_iterm`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `description` blob,
  `remark` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `type` tinyint(4) DEFAULT 0 COMMENT '0-?? 1-?? 2-??',
  `allow_empty` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apply_user
-- ----------------------------
DROP TABLE IF EXISTS `apply_user`;
CREATE TABLE `apply_user`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `name` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `email` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `question_code` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `time` datetime(0) DEFAULT NULL,
  `IP` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `status` int(11) DEFAULT 0 COMMENT '0-??? 1-?? 2-??',
  `check_id` bigint(20) DEFAULT NULL,
  `check_description` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `check_time` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for apply_user_answer
-- ----------------------------
DROP TABLE IF EXISTS `apply_user_answer`;
CREATE TABLE `apply_user_answer`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `apply_id` bigint(20) DEFAULT NULL,
  `question_code` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `iterm_id` bigint(20) DEFAULT NULL,
  `answer` blob,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for artists_alias
-- ----------------------------
DROP TABLE IF EXISTS `artists_alias`;
CREATE TABLE `artists_alias`  (
  `AliasID` int(10) NOT NULL AUTO_INCREMENT,
  `ArtistID` int(10) NOT NULL,
  `Name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Redirect` int(10) NOT NULL DEFAULT 0,
  `UserID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`AliasID`) USING BTREE,
  INDEX `ArtistID`(`ArtistID`, `Name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 247 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for artists_group
-- ----------------------------
DROP TABLE IF EXISTS `artists_group`;
CREATE TABLE `artists_group`  (
  `ArtistID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RevisionID` int(12) DEFAULT NULL,
  `VanityHouse` tinyint(1) NOT NULL,
  `LastCommentID` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ArtistID`) USING BTREE,
  INDEX `Name`(`Name`, `RevisionID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 247 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;
-- ----------------------------
-- Table structure for artists_similar
-- ----------------------------
DROP TABLE IF EXISTS `artists_similar`;
CREATE TABLE `artists_similar`  (
  `ArtistID` int(10) NOT NULL DEFAULT 0,
  `SimilarID` int(12) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ArtistID`, `SimilarID`) USING BTREE,
  INDEX `ArtistID`(`ArtistID`, `SimilarID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for artists_similar_scores
-- ----------------------------
DROP TABLE IF EXISTS `artists_similar_scores`;
CREATE TABLE `artists_similar_scores`  (
  `SimilarID` int(12) NOT NULL AUTO_INCREMENT,
  `Score` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`SimilarID`) USING BTREE,
  INDEX `Score`(`Score`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for artists_similar_votes
-- ----------------------------
DROP TABLE IF EXISTS `artists_similar_votes`;
CREATE TABLE `artists_similar_votes`  (
  `SimilarID` int(12) NOT NULL,
  `UserID` int(10) NOT NULL,
  `Way` enum('up','down') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'up',
  PRIMARY KEY (`SimilarID`, `UserID`, `Way`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for artists_tags
-- ----------------------------
DROP TABLE IF EXISTS `artists_tags`;
CREATE TABLE `artists_tags`  (
  `TagID` int(10) NOT NULL DEFAULT 0,
  `ArtistID` int(10) NOT NULL DEFAULT 0,
  `PositiveVotes` int(6) NOT NULL DEFAULT 1,
  `NegativeVotes` int(6) NOT NULL DEFAULT 1,
  `UserID` int(10) NOT NULL,
  PRIMARY KEY (`TagID`, `ArtistID`) USING BTREE,
  INDEX `TagID`(`TagID`, `ArtistID`, `PositiveVotes`, `NegativeVotes`, `UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bad_passwords
-- ----------------------------
DROP TABLE IF EXISTS `bad_passwords`;
CREATE TABLE `bad_passwords`  (
  `Password` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`Password`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for badges
-- ----------------------------
DROP TABLE IF EXISTS `badges`;
CREATE TABLE `badges`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `BadgeID` int(11) NOT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Username` int(11) NOT NULL DEFAULT 0,
  `Profile` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `UserID`(`UserID`, `BadgeID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25639 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for badges_item
-- ----------------------------
DROP TABLE IF EXISTS `badges_item`;
CREATE TABLE `badges_item`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Label` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `BigImage` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `SmallImage` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Level` int(11) NOT NULL,
  `Count` int(11) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Label`(`Label`, `Level`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 136 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for badges_label
-- ----------------------------
DROP TABLE IF EXISTS `badges_label`;
CREATE TABLE `badges_label`  (
  `Label` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `DisImage` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Remark` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Type` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Auto` tinyint(1) NOT NULL DEFAULT 0,
  `Rank` int(11) NOT NULL,
  `Father` tinyint(1) NOT NULL DEFAULT 1,
  `Progress` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`Label`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for blog
-- ----------------------------
DROP TABLE IF EXISTS `blog`;
CREATE TABLE `blog`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL,
  `ThreadID` int(10) UNSIGNED DEFAULT NULL,
  `Important` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bonus_history
-- ----------------------------
DROP TABLE IF EXISTS `bonus_history`;
CREATE TABLE `bonus_history`  (
  `ID` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ItemID` int(6) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Price` int(10) UNSIGNED NOT NULL,
  `OtherUserID` int(10) UNSIGNED DEFAULT NULL,
  `PurchaseDate` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `bonus_history_fk_user`(`UserID`) USING BTREE,
  INDEX `bonus_history_fk_item`(`ItemID`) USING BTREE,
  CONSTRAINT `bonus_history_fk_item` FOREIGN KEY (`ItemID`) REFERENCES `bonus_item` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `bonus_history_fk_user` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bonus_item
-- ----------------------------
DROP TABLE IF EXISTS `bonus_item`;
CREATE TABLE `bonus_item`  (
  `ID` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Price` int(10) UNSIGNED NOT NULL,
  `Amount` int(2) UNSIGNED DEFAULT NULL,
  `MinClass` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `FreeClass` int(6) UNSIGNED NOT NULL DEFAULT 999999,
  `OffPrice` int(10) NOT NULL,
  `OffClass` int(6) NOT NULL DEFAULT 999999,
  `Label` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Title` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Rank` int(6) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Label`(`Label`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bookmarks_artists
-- ----------------------------
DROP TABLE IF EXISTS `bookmarks_artists`;
CREATE TABLE `bookmarks_artists`  (
  `UserID` int(10) NOT NULL,
  `ArtistID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `ArtistID`(`ArtistID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bookmarks_collages
-- ----------------------------
DROP TABLE IF EXISTS `bookmarks_collages`;
CREATE TABLE `bookmarks_collages`  (
  `UserID` int(10) NOT NULL,
  `CollageID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `CollageID`(`CollageID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bookmarks_requests
-- ----------------------------
DROP TABLE IF EXISTS `bookmarks_requests`;
CREATE TABLE `bookmarks_requests`  (
  `UserID` int(10) NOT NULL,
  `RequestID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `RequestID`(`RequestID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for bookmarks_torrents
-- ----------------------------
DROP TABLE IF EXISTS `bookmarks_torrents`;
CREATE TABLE `bookmarks_torrents`  (
  `UserID` int(10) NOT NULL,
  `GroupID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  `Sort` int(11) NOT NULL DEFAULT 0,
  UNIQUE INDEX `groups_users`(`GroupID`, `UserID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for calendar
-- ----------------------------
DROP TABLE IF EXISTS `calendar`;
CREATE TABLE `calendar`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Category` tinyint(1) DEFAULT NULL,
  `StartDate` datetime(0) DEFAULT NULL,
  `EndDate` datetime(0) DEFAULT NULL,
  `AddedBy` int(10) DEFAULT NULL,
  `Importance` tinyint(1) DEFAULT NULL,
  `Team` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for changelog
-- ----------------------------
DROP TABLE IF EXISTS `changelog`;
CREATE TABLE `changelog`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Time` datetime(0) NOT NULL,
  `Message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Author` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for collages
-- ----------------------------
DROP TABLE IF EXISTS `collages`;
CREATE TABLE `collages`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `NumTorrents` int(4) NOT NULL DEFAULT 0,
  `Deleted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0',
  `Locked` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `CategoryID` int(2) NOT NULL DEFAULT 1,
  `TagList` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `MaxGroups` int(10) NOT NULL DEFAULT 0,
  `MaxGroupsPerUser` int(10) NOT NULL DEFAULT 0,
  `Featured` tinyint(4) NOT NULL DEFAULT 0,
  `Subscribers` int(10) DEFAULT 0,
  `updated` datetime(0) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Name`(`Name`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `CategoryID`(`CategoryID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for collages_artists
-- ----------------------------
DROP TABLE IF EXISTS `collages_artists`;
CREATE TABLE `collages_artists`  (
  `CollageID` int(10) NOT NULL,
  `ArtistID` int(10) NOT NULL,
  `UserID` int(10) NOT NULL,
  `Sort` int(10) NOT NULL DEFAULT 0,
  `AddedOn` datetime(0) NOT NULL,
  PRIMARY KEY (`CollageID`, `ArtistID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Sort`(`Sort`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for collages_torrents
-- ----------------------------
DROP TABLE IF EXISTS `collages_torrents`;
CREATE TABLE `collages_torrents`  (
  `CollageID` int(10) NOT NULL,
  `GroupID` int(10) NOT NULL,
  `UserID` int(10) NOT NULL,
  `Sort` int(10) NOT NULL DEFAULT 0,
  `AddedOn` datetime(0) NOT NULL,
  PRIMARY KEY (`CollageID`, `GroupID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Sort`(`Sort`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for comments
-- ----------------------------
DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Page` enum('artist','collages','requests','torrents') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `PageID` int(10) NOT NULL,
  `AuthorID` int(10) NOT NULL,
  `AddedTime` datetime(0) NOT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `EditedUserID` int(10) DEFAULT NULL,
  `EditedTime` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Page`(`Page`, `PageID`) USING BTREE,
  INDEX `AuthorID`(`AuthorID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 707 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for comments_edits
-- ----------------------------
DROP TABLE IF EXISTS `comments_edits`;
CREATE TABLE `comments_edits`  (
  `Page` enum('forums','artist','collages','requests','torrents') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `PostID` int(10) DEFAULT NULL,
  `EditUser` int(10) DEFAULT NULL,
  `EditTime` datetime(0) DEFAULT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  INDEX `EditUser`(`EditUser`) USING BTREE,
  INDEX `PostHistory`(`Page`, `PostID`, `EditTime`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for comments_edits_tmp
-- ----------------------------
DROP TABLE IF EXISTS `comments_edits_tmp`;
CREATE TABLE `comments_edits_tmp`  (
  `Page` enum('forums','artist','collages','requests','torrents') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `PostID` int(10) DEFAULT NULL,
  `EditUser` int(10) DEFAULT NULL,
  `EditTime` datetime(0) DEFAULT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  INDEX `EditUser`(`EditUser`) USING BTREE,
  INDEX `PostHistory`(`Page`, `PostID`, `EditTime`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for concerts
-- ----------------------------
DROP TABLE IF EXISTS `concerts`;
CREATE TABLE `concerts`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ConcertID` int(10) NOT NULL,
  `TopicID` int(10) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ConcertID`(`ConcertID`) USING BTREE,
  INDEX `TopicID`(`TopicID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for contest
-- ----------------------------
DROP TABLE IF EXISTS `contest`;
CREATE TABLE `contest`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `ContestTypeID` int(11) NOT NULL,
  `Name` varchar(80) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `Banner` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `DateBegin` datetime(0) NOT NULL,
  `DateEnd` datetime(0) NOT NULL,
  `Display` int(11) NOT NULL DEFAULT 50,
  `MaxTracked` int(11) NOT NULL DEFAULT 500,
  `WikiText` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Name`(`Name`) USING BTREE,
  INDEX `contest_type_fk`(`ContestTypeID`) USING BTREE,
  CONSTRAINT `contest_type_fk` FOREIGN KEY (`ContestTypeID`) REFERENCES `contest_type` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for contest_leaderboard
-- ----------------------------
DROP TABLE IF EXISTS `contest_leaderboard`;
CREATE TABLE `contest_leaderboard`  (
  `ContestID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `FlacCount` int(11) NOT NULL,
  `LastTorrentID` int(11) NOT NULL,
  `LastTorrentName` varchar(80) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `ArtistList` varchar(80) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `ArtistNames` varchar(200) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  `LastUpload` datetime(0) NOT NULL,
  INDEX `contest_fk`(`ContestID`) USING BTREE,
  INDEX `flac_upload_idx`(`FlacCount`, `LastUpload`, `UserID`) USING BTREE,
  CONSTRAINT `contest_fk` FOREIGN KEY (`ContestID`) REFERENCES `contest` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for contest_type
-- ----------------------------
DROP TABLE IF EXISTS `contest_type`;
CREATE TABLE `contest_type`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) CHARACTER SET utf8 COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Name`(`Name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_swedish_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cover_art
-- ----------------------------
DROP TABLE IF EXISTS `cover_art`;
CREATE TABLE `cover_art`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `GroupID` int(10) NOT NULL,
  `Image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Summary` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Time` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `GroupID`(`GroupID`, `Image`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for currency_conversion_rates
-- ----------------------------
DROP TABLE IF EXISTS `currency_conversion_rates`;
CREATE TABLE `currency_conversion_rates`  (
  `Currency` char(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Rate` decimal(9, 4) DEFAULT NULL,
  `Time` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`Currency`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for do_not_upload
-- ----------------------------
DROP TABLE IF EXISTS `do_not_upload`;
CREATE TABLE `do_not_upload`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Comment` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  `Sequence` mediumint(8) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for donations
-- ----------------------------
DROP TABLE IF EXISTS `donations`;
CREATE TABLE `donations`  (
  `UserID` int(10) NOT NULL,
  `Amount` decimal(6, 2) NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL,
  `Currency` varchar(5) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'USD',
  `Source` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Reason` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Rank` int(10) DEFAULT 0,
  `AddedBy` int(10) DEFAULT 0,
  `TotalRank` int(10) DEFAULT 0,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE,
  INDEX `Amount`(`Amount`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for donations_bitcoin
-- ----------------------------
DROP TABLE IF EXISTS `donations_bitcoin`;
CREATE TABLE `donations_bitcoin`  (
  `BitcoinAddress` varchar(34) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Amount` decimal(24, 8) NOT NULL,
  INDEX `BitcoinAddress`(`BitcoinAddress`, `Amount`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for donor_forum_usernames
-- ----------------------------
DROP TABLE IF EXISTS `donor_forum_usernames`;
CREATE TABLE `donor_forum_usernames`  (
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Prefix` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Suffix` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `UseComma` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for donor_rewards
-- ----------------------------
DROP TABLE IF EXISTS `donor_rewards`;
CREATE TABLE `donor_rewards`  (
  `UserID` int(10) NOT NULL DEFAULT 0,
  `IconMouseOverText` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `AvatarMouseOverText` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `CustomIcon` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `SecondAvatar` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `CustomIconLink` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `ProfileInfo1` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ProfileInfo2` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ProfileInfo3` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ProfileInfo4` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ProfileInfoTitle1` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ProfileInfoTitle2` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ProfileInfoTitle3` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ProfileInfoTitle4` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ColorUsername` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `GradientsColor` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for drives
-- ----------------------------
DROP TABLE IF EXISTS `drives`;
CREATE TABLE `drives`  (
  `DriveID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Offset` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`DriveID`) USING BTREE,
  INDEX `Name`(`Name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for dupe_groups
-- ----------------------------
DROP TABLE IF EXISTS `dupe_groups`;
CREATE TABLE `dupe_groups`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Comments` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for email_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `email_blacklist`;
CREATE TABLE `email_blacklist`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `UserID` int(10) NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL,
  `Comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for events_reward_log
-- ----------------------------
DROP TABLE IF EXISTS `events_reward_log`;
CREATE TABLE `events_reward_log`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserIDs` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ByUserID` int(11) DEFAULT NULL,
  `Invites` int(11) DEFAULT NULL,
  `InvitesTime` datetime(0) DEFAULT NULL,
  `Tokens` int(11) DEFAULT NULL,
  `TokensTime` datetime(0) DEFAULT NULL,
  `Bonus` int(11) DEFAULT NULL,
  `Badge` int(11) DEFAULT NULL,
  `Remark` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Time` datetime(0) DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for featured_albums
-- ----------------------------
DROP TABLE IF EXISTS `featured_albums`;
CREATE TABLE `featured_albums`  (
  `GroupID` int(10) NOT NULL DEFAULT 0,
  `ThreadID` int(10) NOT NULL DEFAULT 0,
  `Title` varchar(35) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Started` datetime(0) NOT NULL,
  `Ended` datetime(0) NOT NULL,
  `Type` tinyint(4) NOT NULL DEFAULT 0
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for featured_merch
-- ----------------------------
DROP TABLE IF EXISTS `featured_merch`;
CREATE TABLE `featured_merch`  (
  `ProductID` int(10) NOT NULL DEFAULT 0,
  `Title` varchar(35) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Started` datetime(0) NOT NULL,
  `Ended` datetime(0) NOT NULL,
  `ArtistID` int(10) UNSIGNED DEFAULT 0
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums
-- ----------------------------
DROP TABLE IF EXISTS `forums`;
CREATE TABLE `forums`  (
  `ID` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `CategoryID` tinyint(2) NOT NULL DEFAULT 0,
  `Sort` int(6) UNSIGNED NOT NULL,
  `Name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `MinClassRead` int(4) NOT NULL DEFAULT 0,
  `MinClassWrite` int(4) NOT NULL DEFAULT 0,
  `MinClassCreate` int(4) NOT NULL DEFAULT 0,
  `NumTopics` int(10) NOT NULL DEFAULT 0,
  `NumPosts` int(10) NOT NULL DEFAULT 0,
  `LastPostID` int(10) NOT NULL DEFAULT 0,
  `LastPostAuthorID` int(10) NOT NULL DEFAULT 0,
  `LastPostTopicID` int(10) NOT NULL DEFAULT 0,
  `LastPostTime` datetime(0) NOT NULL,
  `AutoLock` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '1',
  `AutoLockWeeks` int(3) UNSIGNED NOT NULL DEFAULT 4,
  `Second` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Sort`(`Sort`) USING BTREE,
  INDEX `MinClassRead`(`MinClassRead`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_categories
-- ----------------------------
DROP TABLE IF EXISTS `forums_categories`;
CREATE TABLE `forums_categories`  (
  `ID` tinyint(2) NOT NULL AUTO_INCREMENT,
  `Name` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Sort` int(6) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Sort`(`Sort`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_last_read_topics
-- ----------------------------
DROP TABLE IF EXISTS `forums_last_read_topics`;
CREATE TABLE `forums_last_read_topics`  (
  `UserID` int(10) NOT NULL,
  `TopicID` int(10) NOT NULL,
  `PostID` int(10) NOT NULL,
  PRIMARY KEY (`UserID`, `TopicID`) USING BTREE,
  INDEX `TopicID`(`TopicID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_polls
-- ----------------------------
DROP TABLE IF EXISTS `forums_polls`;
CREATE TABLE `forums_polls`  (
  `TopicID` int(10) UNSIGNED NOT NULL,
  `Question` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Answers` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Featured` datetime(0) NOT NULL,
  `Closed` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `MaxCount` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`TopicID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_polls_votes
-- ----------------------------
DROP TABLE IF EXISTS `forums_polls_votes`;
CREATE TABLE `forums_polls_votes`  (
  `TopicID` int(10) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Vote` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`TopicID`, `UserID`, `Vote`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_posts
-- ----------------------------
DROP TABLE IF EXISTS `forums_posts`;
CREATE TABLE `forums_posts`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `TopicID` int(10) NOT NULL,
  `AuthorID` int(10) NOT NULL,
  `AddedTime` datetime(0) NOT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `EditedUserID` int(10) DEFAULT NULL,
  `EditedTime` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `TopicID`(`TopicID`) USING BTREE,
  INDEX `AuthorID`(`AuthorID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_posts_jf_log
-- ----------------------------
DROP TABLE IF EXISTS `forums_posts_jf_log`;
CREATE TABLE `forums_posts_jf_log`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `TopicID` int(10) NOT NULL,
  `AuthorID` int(10) NOT NULL,
  `PostID` int(10) NOT NULL,
  `AddedTime` datetime(0) NOT NULL,
  `LogTime` datetime(0) NOT NULL,
  `Sentuid` int(10) NOT NULL,
  `Sentjf` int(10) NOT NULL,
  `Comment` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Sys` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for forums_specific_rules
-- ----------------------------
DROP TABLE IF EXISTS `forums_specific_rules`;
CREATE TABLE `forums_specific_rules`  (
  `ForumID` int(6) UNSIGNED DEFAULT NULL,
  `ThreadID` int(10) DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_topic_notes
-- ----------------------------
DROP TABLE IF EXISTS `forums_topic_notes`;
CREATE TABLE `forums_topic_notes`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `TopicID` int(10) NOT NULL,
  `AuthorID` int(10) NOT NULL,
  `AddedTime` datetime(0) NOT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `TopicID`(`TopicID`) USING BTREE,
  INDEX `AuthorID`(`AuthorID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for forums_topics
-- ----------------------------
DROP TABLE IF EXISTS `forums_topics`;
CREATE TABLE `forums_topics`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Title` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `AuthorID` int(10) NOT NULL,
  `IsLocked` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `IsNotice` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `IsSticky` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `ForumID` int(3) NOT NULL,
  `NumPosts` int(10) NOT NULL DEFAULT 0,
  `LastPostID` int(10) NOT NULL,
  `LastPostTime` datetime(0) NOT NULL,
  `LastPostAuthorID` int(10) NOT NULL,
  `StickyPostID` int(10) NOT NULL DEFAULT 0,
  `Ranking` tinyint(2) DEFAULT 0,
  `CreatedTime` datetime(0) NOT NULL,
  `AutoLocked` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `hiddenreplies` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `AuthorID`(`AuthorID`) USING BTREE,
  INDEX `ForumID`(`ForumID`) USING BTREE,
  INDEX `IsSticky`(`IsSticky`) USING BTREE,
  INDEX `LastPostID`(`LastPostID`) USING BTREE,
  INDEX `Title`(`Title`) USING BTREE,
  INDEX `CreatedTime`(`CreatedTime`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for freetorrents_timed
-- ----------------------------
DROP TABLE IF EXISTS `freetorrents_timed`;
CREATE TABLE `freetorrents_timed`  (
  `TorrentID` int(11) NOT NULL,
  `EndTime` datetime(0) NOT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for friends
-- ----------------------------
DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `FriendID` int(10) UNSIGNED NOT NULL,
  `Comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`UserID`, `FriendID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `FriendID`(`FriendID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for geoip_country
-- ----------------------------
DROP TABLE IF EXISTS `geoip_country`;
CREATE TABLE `geoip_country`  (
  `StartIP` int(11) UNSIGNED NOT NULL,
  `EndIP` int(11) UNSIGNED NOT NULL,
  `Code` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`StartIP`, `EndIP`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for group_log
-- ----------------------------
DROP TABLE IF EXISTS `group_log`;
CREATE TABLE `group_log`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `GroupID` int(10) NOT NULL,
  `TorrentID` int(10) NOT NULL,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Info` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Time` datetime(0) NOT NULL,
  `Hidden` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `TorrentID`(`TorrentID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 39574 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for invite_tree
-- ----------------------------
DROP TABLE IF EXISTS `invite_tree`;
CREATE TABLE `invite_tree`  (
  `UserID` int(10) NOT NULL DEFAULT 0,
  `InviterID` int(10) NOT NULL DEFAULT 0,
  `TreePosition` int(8) NOT NULL DEFAULT 1,
  `TreeID` int(10) NOT NULL DEFAULT 1,
  `TreeLevel` int(3) NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`) USING BTREE,
  INDEX `InviterID`(`InviterID`) USING BTREE,
  INDEX `TreePosition`(`TreePosition`) USING BTREE,
  INDEX `TreeID`(`TreeID`) USING BTREE,
  INDEX `TreeLevel`(`TreeLevel`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for invites
-- ----------------------------
DROP TABLE IF EXISTS `invites`;
CREATE TABLE `invites`  (
  `InviterID` int(10) NOT NULL DEFAULT 0,
  `InviteKey` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Expires` datetime(0) NOT NULL,
  `Reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `InviteID` int(12) DEFAULT 0,
  PRIMARY KEY (`InviteKey`) USING BTREE,
  INDEX `Expires`(`Expires`) USING BTREE,
  INDEX `InviterID`(`InviterID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for invites_history
-- ----------------------------
DROP TABLE IF EXISTS `invites_history`;
CREATE TABLE `invites_history`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `InviteKey` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 70 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for invites_typed
-- ----------------------------
DROP TABLE IF EXISTS `invites_typed`;
CREATE TABLE `invites_typed`  (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `UserID` int(10) NOT NULL,
  `EndTime` datetime(0) DEFAULT NULL,
  `Type` enum('time','count') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Used` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for ip_bans
-- ----------------------------
DROP TABLE IF EXISTS `ip_bans`;
CREATE TABLE `ip_bans`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `FromIP` int(11) UNSIGNED NOT NULL,
  `ToIP` int(11) UNSIGNED NOT NULL,
  `Reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `FromIP_2`(`FromIP`, `ToIP`) USING BTREE,
  INDEX `ToIP`(`ToIP`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for ip_lock
-- ----------------------------
DROP TABLE IF EXISTS `ip_lock`;
CREATE TABLE `ip_lock`  (
  `UserID` int(11) NOT NULL,
  `IPs` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for label_aliases
-- ----------------------------
DROP TABLE IF EXISTS `label_aliases`;
CREATE TABLE `label_aliases`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `BadLabel` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `AliasLabel` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `BadLabel`(`BadLabel`) USING BTREE,
  INDEX `AliasLabel`(`AliasLabel`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for last_sent_email
-- ----------------------------
DROP TABLE IF EXISTS `last_sent_email`;
CREATE TABLE `last_sent_email`  (
  `UserID` int(10) NOT NULL,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for lastfm_users
-- ----------------------------
DROP TABLE IF EXISTS `lastfm_users`;
CREATE TABLE `lastfm_users`  (
  `ID` int(10) UNSIGNED NOT NULL,
  `Username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for locked_accounts
-- ----------------------------
DROP TABLE IF EXISTS `locked_accounts`;
CREATE TABLE `locked_accounts`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `Type` tinyint(1) NOT NULL,
  PRIMARY KEY (`UserID`) USING BTREE,
  CONSTRAINT `fk_user_id` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for log
-- ----------------------------
DROP TABLE IF EXISTS `log`;
CREATE TABLE `log`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Message` varchar(400) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66769 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for login_attempts
-- ----------------------------
DROP TABLE IF EXISTS `login_attempts`;
CREATE TABLE `login_attempts`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL,
  `IP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `LastAttempt` datetime(0) NOT NULL,
  `Attempts` int(10) UNSIGNED NOT NULL,
  `BannedUntil` datetime(0) NOT NULL,
  `Bans` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `IP`(`IP`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3074 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for login_link
-- ----------------------------
DROP TABLE IF EXISTS `login_link`;
CREATE TABLE `login_link`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `LoginKey` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserID` int(11) NOT NULL,
  `Username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Used` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for movie_info_cache
-- ----------------------------
DROP TABLE IF EXISTS `movie_info_cache`;
CREATE TABLE `movie_info_cache`  (
  `IMDBID` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `OMDBData` json,
  `Time` datetime(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`IMDBID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for new_info_hashes
-- ----------------------------
DROP TABLE IF EXISTS `new_info_hashes`;
CREATE TABLE `new_info_hashes`  (
  `TorrentID` int(11) NOT NULL,
  `InfoHash` binary(20) DEFAULT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE,
  INDEX `InfoHash`(`InfoHash`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for news
-- ----------------------------
DROP TABLE IF EXISTS `news`;
CREATE TABLE `news`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for ocelot_query_times
-- ----------------------------
DROP TABLE IF EXISTS `ocelot_query_times`;
CREATE TABLE `ocelot_query_times`  (
  `buffer` enum('users','torrents','snatches','peers') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `starttime` datetime(0) NOT NULL,
  `ocelotinstance` datetime(0) NOT NULL,
  `querylength` int(11) NOT NULL,
  `timespent` int(11) NOT NULL,
  UNIQUE INDEX `starttime`(`starttime`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for permissions
-- ----------------------------
DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Level` int(10) UNSIGNED NOT NULL,
  `Name` varchar(25) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Values` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `DisplayStaff` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `PermittedForums` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Secondary` tinyint(4) NOT NULL DEFAULT 0,
  `StaffGroup` int(3) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Level`(`Level`) USING BTREE,
  INDEX `DisplayStaff`(`DisplayStaff`) USING BTREE,
  INDEX `StaffGroup`(`StaffGroup`) USING BTREE,
  CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`StaffGroup`) REFERENCES `staff_groups` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 61 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of permissions
-- ----------------------------
INSERT INTO `permissions` VALUES (2, 100, 'User', 'a:10:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:16:\"site_album_votes\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"0\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (3, 150, 'Member', 'a:17:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:14:\"zip_downloader\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"0\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (4, 200, 'Power User', 'a:19:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:14:\"zip_downloader\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"1\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (5, 250, 'Elite', 'a:25:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:15:\"site_can_invite\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:13:\"torrents_edit\";i:1;s:19:\"self_torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"2\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (11, 900, 'Moderator', 'a:75:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:20:\"site_collages_delete\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:15:\"site_can_invite\";i:1;s:27:\"site_send_unlimited_invites\";i:1;s:22:\"site_moderate_requests\";i:1;s:18:\"site_delete_artist\";i:1;s:19:\"forums_polls_create\";i:1;s:20:\"site_moderate_forums\";i:1;s:17:\"site_admin_forums\";i:1;s:28:\"site_view_torrent_snatchlist\";i:1;s:15:\"site_delete_tag\";i:1;s:23:\"site_disable_ip_history\";i:1;s:14:\"zip_downloader\";i:1;s:16:\"site_search_many\";i:1;s:12:\"project_team\";i:1;s:21:\"site_tag_aliases_read\";i:1;s:17:\"forums_see_hidden\";i:1;s:17:\"users_edit_titles\";i:1;s:18:\"users_edit_avatars\";i:1;s:18:\"users_edit_invites\";i:1;s:21:\"users_edit_reset_keys\";i:1;s:18:\"users_view_friends\";i:1;s:10:\"users_warn\";i:1;s:19:\"users_disable_users\";i:1;s:19:\"users_disable_posts\";i:1;s:17:\"users_disable_any\";i:1;s:18:\"users_view_invites\";i:1;s:20:\"users_view_seedleech\";i:1;s:19:\"users_view_uploaded\";i:1;s:15:\"users_view_keys\";i:1;s:14:\"users_view_ips\";i:1;s:16:\"users_view_email\";i:1;s:18:\"users_invite_notes\";i:1;s:23:\"users_override_paranoia\";i:1;s:12:\"users_logout\";i:1;s:9:\"users_mod\";i:1;s:11:\"staff_award\";i:1;s:19:\"users_view_disabled\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:18:\"torrents_check_log\";i:1;s:15:\"torrents_delete\";i:1;s:20:\"torrents_delete_fast\";i:1;s:18:\"torrents_freeleech\";i:1;s:20:\"torrents_search_fast\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:19:\"torrents_fix_ghosts\";i:1;s:17:\"admin_manage_blog\";i:1;s:19:\"admin_manage_forums\";i:1;s:16:\"admin_manage_fls\";i:1;s:19:\"admin_manage_badges\";i:1;s:16:\"admin_send_bonus\";i:1;s:13:\"admin_reports\";i:1;s:26:\"admin_advanced_user_search\";i:1;s:17:\"admin_clear_cache\";i:1;s:15:\"admin_whitelist\";i:1;s:17:\"admin_manage_wiki\";i:1;s:17:\"admin_interviewer\";i:1;s:11:\"MaxCollages\";s:1:\"6\";}', '1', '', 0, 1);
INSERT INTO `permissions` VALUES (15, 1000, 'Sysop', 'a:120:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:20:\"site_collages_delete\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:22:\"site_can_invite_always\";i:1;s:15:\"site_can_invite\";i:1;s:27:\"site_send_unlimited_invites\";i:1;s:22:\"site_moderate_requests\";i:1;s:18:\"site_delete_artist\";i:1;s:19:\"forums_polls_create\";i:1;s:21:\"forums_polls_moderate\";i:1;s:20:\"site_moderate_forums\";i:1;s:17:\"site_admin_forums\";i:1;s:14:\"site_view_flow\";i:1;s:18:\"site_view_full_log\";i:1;s:28:\"site_view_torrent_snatchlist\";i:1;s:18:\"site_recommend_own\";i:1;s:27:\"site_manage_recommendations\";i:1;s:15:\"site_delete_tag\";i:1;s:23:\"site_disable_ip_history\";i:1;s:14:\"zip_downloader\";i:1;s:10:\"site_debug\";i:1;s:16:\"site_search_many\";i:1;s:21:\"site_collages_recover\";i:1;s:12:\"project_team\";i:1;s:21:\"site_tag_aliases_read\";i:1;s:17:\"forums_see_hidden\";i:1;s:15:\"show_admin_team\";i:1;s:19:\"show_staff_username\";i:1;s:20:\"users_edit_usernames\";i:1;s:16:\"users_edit_ratio\";i:1;s:20:\"users_edit_own_ratio\";i:1;s:17:\"users_edit_titles\";i:1;s:18:\"users_edit_avatars\";i:1;s:18:\"users_edit_invites\";i:1;s:22:\"users_edit_watch_hours\";i:1;s:21:\"users_edit_reset_keys\";i:1;s:19:\"users_edit_profiles\";i:1;s:18:\"users_view_friends\";i:1;s:20:\"users_reset_own_keys\";i:1;s:19:\"users_edit_password\";i:1;s:19:\"users_promote_below\";i:1;s:16:\"users_promote_to\";i:1;s:16:\"users_give_donor\";i:1;s:10:\"users_warn\";i:1;s:19:\"users_disable_users\";i:1;s:19:\"users_disable_posts\";i:1;s:17:\"users_disable_any\";i:1;s:18:\"users_delete_users\";i:1;s:18:\"users_view_invites\";i:1;s:20:\"users_view_seedleech\";i:1;s:19:\"users_view_uploaded\";i:1;s:15:\"users_view_keys\";i:1;s:14:\"users_view_ips\";i:1;s:16:\"users_view_email\";i:1;s:18:\"users_invite_notes\";i:1;s:23:\"users_override_paranoia\";i:1;s:20:\"users_make_invisible\";i:1;s:12:\"users_logout\";i:1;s:9:\"users_mod\";i:1;s:11:\"staff_award\";i:1;s:19:\"users_view_disabled\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"self_torrents_check\";i:1;s:18:\"torrents_check_log\";i:1;s:15:\"torrents_delete\";i:1;s:20:\"torrents_delete_fast\";i:1;s:18:\"torrents_freeleech\";i:1;s:20:\"torrents_search_fast\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:25:\"torrents_edit_vanityhouse\";i:1;s:23:\"artist_edit_vanityhouse\";i:1;s:17:\"torrents_hide_dnu\";i:1;s:19:\"torrents_fix_ghosts\";i:1;s:17:\"admin_manage_news\";i:1;s:17:\"admin_manage_blog\";i:1;s:18:\"admin_manage_polls\";i:1;s:19:\"admin_manage_forums\";i:1;s:16:\"admin_manage_fls\";i:1;s:21:\"admin_manage_user_fls\";i:1;s:19:\"admin_manage_badges\";i:1;s:23:\"admin_manage_applicants\";i:1;s:16:\"admin_send_bonus\";i:1;s:13:\"admin_reports\";i:1;s:16:\"admin_bp_history\";i:1;s:26:\"admin_advanced_user_search\";i:1;s:18:\"admin_create_users\";i:1;s:15:\"admin_donor_log\";i:1;s:24:\"admin_manage_stylesheets\";i:1;s:19:\"admin_manage_ipbans\";i:1;s:9:\"admin_dnu\";i:1;s:17:\"admin_clear_cache\";i:1;s:15:\"admin_whitelist\";i:1;s:24:\"admin_manage_permissions\";i:1;s:14:\"admin_schedule\";i:1;s:17:\"admin_login_watch\";i:1;s:17:\"admin_manage_wiki\";i:1;s:18:\"admin_update_geoip\";i:1;s:17:\"admin_interviewer\";i:1;s:20:\"events_reward_tokens\";i:1;s:19:\"events_reward_bonus\";i:1;s:21:\"events_reward_invites\";i:1;s:20:\"events_reward_badges\";i:1;s:21:\"events_reward_history\";i:1;s:11:\"MaxCollages\";s:0:\"\";}', '1', '', 0, 4);
INSERT INTO `permissions` VALUES (20, 201, 'Donor', 'a:18:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:15:\"site_can_invite\";i:1;s:19:\"forums_polls_create\";i:1;s:14:\"zip_downloader\";i:1;s:11:\"MaxCollages\";s:1:\"1\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (21, 800, 'Forum Moderator', 'a:45:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:20:\"site_moderate_forums\";i:1;s:17:\"site_admin_forums\";i:1;s:28:\"site_view_torrent_snatchlist\";i:1;s:15:\"site_delete_tag\";i:1;s:23:\"site_disable_ip_history\";i:1;s:14:\"zip_downloader\";i:1;s:16:\"site_search_many\";i:1;s:12:\"project_team\";i:1;s:21:\"site_tag_aliases_read\";i:1;s:18:\"users_view_friends\";i:1;s:10:\"users_warn\";i:1;s:19:\"users_disable_posts\";i:1;s:20:\"users_view_seedleech\";i:1;s:19:\"users_view_uploaded\";i:1;s:18:\"users_invite_notes\";i:1;s:23:\"users_override_paranoia\";i:1;s:9:\"users_mod\";i:1;s:11:\"staff_award\";i:1;s:19:\"users_view_disabled\";i:1;s:13:\"torrents_edit\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:19:\"torrents_fix_ghosts\";i:1;s:16:\"admin_send_bonus\";i:1;s:13:\"admin_reports\";i:1;s:26:\"admin_advanced_user_search\";i:1;s:17:\"admin_manage_wiki\";i:1;s:11:\"MaxCollages\";s:1:\"5\";}', '1', '', 0, 1);
INSERT INTO `permissions` VALUES (22, 850, 'Torrent Moderator', 'a:55:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:18:\"site_delete_artist\";i:1;s:19:\"forums_polls_create\";i:1;s:20:\"site_moderate_forums\";i:1;s:17:\"site_admin_forums\";i:1;s:28:\"site_view_torrent_snatchlist\";i:1;s:15:\"site_delete_tag\";i:1;s:23:\"site_disable_ip_history\";i:1;s:14:\"zip_downloader\";i:1;s:16:\"site_search_many\";i:1;s:12:\"project_team\";i:1;s:21:\"site_tag_aliases_read\";i:1;s:21:\"users_edit_reset_keys\";i:1;s:18:\"users_view_friends\";i:1;s:10:\"users_warn\";i:1;s:17:\"users_disable_any\";i:1;s:20:\"users_view_seedleech\";i:1;s:19:\"users_view_uploaded\";i:1;s:15:\"users_view_keys\";i:1;s:14:\"users_view_ips\";i:1;s:16:\"users_view_email\";i:1;s:18:\"users_invite_notes\";i:1;s:23:\"users_override_paranoia\";i:1;s:9:\"users_mod\";i:1;s:11:\"staff_award\";i:1;s:19:\"users_view_disabled\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:18:\"torrents_check_log\";i:1;s:15:\"torrents_delete\";i:1;s:20:\"torrents_delete_fast\";i:1;s:20:\"torrents_search_fast\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:19:\"torrents_fix_ghosts\";i:1;s:16:\"admin_send_bonus\";i:1;s:13:\"admin_reports\";i:1;s:26:\"admin_advanced_user_search\";i:1;s:17:\"admin_clear_cache\";i:1;s:11:\"MaxCollages\";s:1:\"6\";}', '1', '', 0, 1);
INSERT INTO `permissions` VALUES (23, 255, 'First Line Support', 'a:4:{s:22:\"site_collages_personal\";i:1;s:19:\"site_advanced_top10\";i:1;s:11:\"staff_award\";i:1;s:11:\"MaxCollages\";s:1:\"1\";}', '1', '28', 1, 6);
INSERT INTO `permissions` VALUES (24, 950, 'Developer', 'a:62:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:15:\"site_can_invite\";i:1;s:19:\"forums_polls_create\";i:1;s:20:\"site_moderate_forums\";i:1;s:17:\"site_admin_forums\";i:1;s:14:\"site_view_flow\";i:1;s:18:\"site_view_full_log\";i:1;s:28:\"site_view_torrent_snatchlist\";i:1;s:18:\"site_recommend_own\";i:1;s:27:\"site_manage_recommendations\";i:1;s:14:\"zip_downloader\";i:1;s:10:\"site_debug\";i:1;s:16:\"site_search_many\";i:1;s:21:\"site_tag_aliases_read\";i:1;s:16:\"users_give_donor\";i:1;s:19:\"users_view_uploaded\";i:1;s:14:\"users_view_ips\";i:1;s:9:\"users_mod\";i:1;s:11:\"staff_award\";i:1;s:19:\"users_view_disabled\";i:1;s:13:\"torrents_edit\";i:1;s:18:\"torrents_check_log\";i:1;s:20:\"torrents_search_fast\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:17:\"admin_manage_blog\";i:1;s:18:\"admin_manage_polls\";i:1;s:19:\"admin_manage_forums\";i:1;s:13:\"admin_reports\";i:1;s:16:\"admin_bp_history\";i:1;s:26:\"admin_advanced_user_search\";i:1;s:15:\"admin_donor_log\";i:1;s:24:\"admin_manage_stylesheets\";i:1;s:19:\"admin_manage_ipbans\";i:1;s:17:\"admin_clear_cache\";i:1;s:15:\"admin_whitelist\";i:1;s:24:\"admin_manage_permissions\";i:1;s:14:\"admin_schedule\";i:1;s:17:\"admin_login_watch\";i:1;s:18:\"admin_update_geoip\";i:1;s:17:\"admin_interviewer\";i:1;s:20:\"events_reward_tokens\";i:1;s:19:\"events_reward_bonus\";i:1;s:21:\"events_reward_invites\";i:1;s:20:\"events_reward_badges\";i:1;s:21:\"events_reward_history\";i:1;s:11:\"MaxCollages\";s:1:\"1\";}', '1', '35', 0, 3);
INSERT INTO `permissions` VALUES (25, 400, 'Torrent Master', 'a:24:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:13:\"torrents_edit\";i:1;s:19:\"self_torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"3\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (26, 601, 'VIP', 'a:23:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:13:\"torrents_edit\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"4\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (27, 701, 'Legend', 'a:31:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:21:\"site_tag_aliases_read\";i:1;s:18:\"users_view_friends\";i:1;s:18:\"users_view_invites\";i:1;s:20:\"users_view_seedleech\";i:1;s:19:\"users_view_uploaded\";i:1;s:23:\"users_override_paranoia\";i:1;s:11:\"staff_award\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"6\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (28, 501, 'Elite TM +', 'a:26:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"self_torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:16:\"admin_manage_fls\";i:1;s:11:\"MaxCollages\";s:1:\"5\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (29, 450, 'Power TM', 'a:25:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"self_torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"4\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (30, 300, 'Interviewer', 'a:3:{s:11:\"staff_award\";i:1;s:17:\"admin_interviewer\";i:1;s:11:\"MaxCollages\";s:1:\"0\";}', '0', '30', 1, 6);
INSERT INTO `permissions` VALUES (31, 310, 'Torrent Celebrity', 'a:3:{s:16:\"users_view_email\";i:1;s:19:\"users_view_disabled\";i:1;s:11:\"MaxCollages\";s:1:\"0\";}', '0', '38', 1, 6);
INSERT INTO `permissions` VALUES (32, 320, 'Designer', 'a:14:{s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:11:\"MaxCollages\";s:1:\"5\";}', '1', '33', 1, 3);
INSERT INTO `permissions` VALUES (40, 980, 'Administrator', 'a:120:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:20:\"site_collages_delete\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:22:\"site_can_invite_always\";i:1;s:15:\"site_can_invite\";i:1;s:27:\"site_send_unlimited_invites\";i:1;s:22:\"site_moderate_requests\";i:1;s:18:\"site_delete_artist\";i:1;s:19:\"forums_polls_create\";i:1;s:21:\"forums_polls_moderate\";i:1;s:20:\"site_moderate_forums\";i:1;s:17:\"site_admin_forums\";i:1;s:14:\"site_view_flow\";i:1;s:18:\"site_view_full_log\";i:1;s:28:\"site_view_torrent_snatchlist\";i:1;s:18:\"site_recommend_own\";i:1;s:27:\"site_manage_recommendations\";i:1;s:15:\"site_delete_tag\";i:1;s:23:\"site_disable_ip_history\";i:1;s:14:\"zip_downloader\";i:1;s:10:\"site_debug\";i:1;s:16:\"site_search_many\";i:1;s:21:\"site_collages_recover\";i:1;s:12:\"project_team\";i:1;s:21:\"site_tag_aliases_read\";i:1;s:17:\"forums_see_hidden\";i:1;s:15:\"show_admin_team\";i:1;s:19:\"show_staff_username\";i:1;s:20:\"users_edit_usernames\";i:1;s:16:\"users_edit_ratio\";i:1;s:20:\"users_edit_own_ratio\";i:1;s:17:\"users_edit_titles\";i:1;s:18:\"users_edit_avatars\";i:1;s:18:\"users_edit_invites\";i:1;s:22:\"users_edit_watch_hours\";i:1;s:21:\"users_edit_reset_keys\";i:1;s:19:\"users_edit_profiles\";i:1;s:18:\"users_view_friends\";i:1;s:20:\"users_reset_own_keys\";i:1;s:19:\"users_edit_password\";i:1;s:19:\"users_promote_below\";i:1;s:16:\"users_promote_to\";i:1;s:16:\"users_give_donor\";i:1;s:10:\"users_warn\";i:1;s:19:\"users_disable_users\";i:1;s:19:\"users_disable_posts\";i:1;s:17:\"users_disable_any\";i:1;s:18:\"users_delete_users\";i:1;s:18:\"users_view_invites\";i:1;s:20:\"users_view_seedleech\";i:1;s:19:\"users_view_uploaded\";i:1;s:15:\"users_view_keys\";i:1;s:14:\"users_view_ips\";i:1;s:16:\"users_view_email\";i:1;s:18:\"users_invite_notes\";i:1;s:23:\"users_override_paranoia\";i:1;s:20:\"users_make_invisible\";i:1;s:12:\"users_logout\";i:1;s:9:\"users_mod\";i:1;s:11:\"staff_award\";i:1;s:19:\"users_view_disabled\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"self_torrents_check\";i:1;s:18:\"torrents_check_log\";i:1;s:15:\"torrents_delete\";i:1;s:20:\"torrents_delete_fast\";i:1;s:18:\"torrents_freeleech\";i:1;s:20:\"torrents_search_fast\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:25:\"torrents_edit_vanityhouse\";i:1;s:23:\"artist_edit_vanityhouse\";i:1;s:17:\"torrents_hide_dnu\";i:1;s:19:\"torrents_fix_ghosts\";i:1;s:17:\"admin_manage_news\";i:1;s:17:\"admin_manage_blog\";i:1;s:18:\"admin_manage_polls\";i:1;s:19:\"admin_manage_forums\";i:1;s:16:\"admin_manage_fls\";i:1;s:21:\"admin_manage_user_fls\";i:1;s:19:\"admin_manage_badges\";i:1;s:23:\"admin_manage_applicants\";i:1;s:16:\"admin_send_bonus\";i:1;s:13:\"admin_reports\";i:1;s:16:\"admin_bp_history\";i:1;s:26:\"admin_advanced_user_search\";i:1;s:18:\"admin_create_users\";i:1;s:15:\"admin_donor_log\";i:1;s:24:\"admin_manage_stylesheets\";i:1;s:19:\"admin_manage_ipbans\";i:1;s:9:\"admin_dnu\";i:1;s:17:\"admin_clear_cache\";i:1;s:15:\"admin_whitelist\";i:1;s:24:\"admin_manage_permissions\";i:1;s:14:\"admin_schedule\";i:1;s:17:\"admin_login_watch\";i:1;s:17:\"admin_manage_wiki\";i:1;s:18:\"admin_update_geoip\";i:1;s:17:\"admin_interviewer\";i:1;s:20:\"events_reward_tokens\";i:1;s:19:\"events_reward_bonus\";i:1;s:21:\"events_reward_invites\";i:1;s:20:\"events_reward_badges\";i:1;s:21:\"events_reward_history\";i:1;s:11:\"MaxCollages\";s:1:\"6\";}', '1', '', 0, 4);
INSERT INTO `permissions` VALUES (42, 205, 'Donor', 'a:14:{s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:19:\"forums_polls_create\";i:1;s:14:\"zip_downloader\";i:1;s:11:\"MaxCollages\";i:1;}', '0', '10', 1, NULL);
INSERT INTO `permissions` VALUES (44, 500, 'Elite TM', 'a:25:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"self_torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"5\";}', '0', '', 0, 6);
INSERT INTO `permissions` VALUES (45, 410, 'Pick Team', 'a:7:{s:15:\"site_delete_tag\";i:1;s:13:\"torrents_edit\";i:1;s:19:\"self_torrents_check\";i:1;s:18:\"torrents_freeleech\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"4\";}', '0', '', 1, 1);
INSERT INTO `permissions` VALUES (51, 265, 'Translators', 'a:3:{s:14:\"site_edit_wiki\";i:1;s:11:\"staff_award\";i:1;s:11:\"MaxCollages\";s:1:\"1\";}', '1', '58', 1, 6);
INSERT INTO `permissions` VALUES (52, 350, 'Sailing Team', 'a:1:{s:11:\"MaxCollages\";s:1:\"6\";}', '0', '', 1, 6);
INSERT INTO `permissions` VALUES (53, 340, 'Human Resource', 'a:1:{s:11:\"MaxCollages\";s:0:\"\";}', '1', '', 1, 5);
INSERT INTO `permissions` VALUES (55, 321, 'Developers', 'a:28:{s:19:\"forums_polls_create\";i:1;s:21:\"forums_polls_moderate\";i:1;s:18:\"site_view_full_log\";i:1;s:18:\"users_edit_invites\";i:1;s:19:\"users_edit_profiles\";i:1;s:10:\"users_warn\";i:1;s:19:\"users_disable_users\";i:1;s:19:\"users_disable_posts\";i:1;s:17:\"users_disable_any\";i:1;s:19:\"users_view_uploaded\";i:1;s:16:\"users_view_email\";i:1;s:23:\"users_override_paranoia\";i:1;s:9:\"users_mod\";i:1;s:11:\"staff_award\";i:1;s:13:\"torrents_edit\";i:1;s:18:\"torrents_check_log\";i:1;s:15:\"torrents_delete\";i:1;s:20:\"torrents_delete_fast\";i:1;s:18:\"torrents_freeleech\";i:1;s:18:\"admin_manage_polls\";i:1;s:19:\"admin_manage_badges\";i:1;s:16:\"admin_send_bonus\";i:1;s:20:\"events_reward_tokens\";i:1;s:19:\"events_reward_bonus\";i:1;s:21:\"events_reward_invites\";i:1;s:20:\"events_reward_badges\";i:1;s:21:\"events_reward_history\";i:1;s:11:\"MaxCollages\";s:0:\"\";}', '1', '', 1, 6);
INSERT INTO `permissions` VALUES (56, 280, 'Torrent Inspector', 'a:9:{s:15:\"site_delete_tag\";i:1;s:16:\"site_search_many\";i:1;s:19:\"users_view_uploaded\";i:1;s:11:\"staff_award\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"1\";}', '1', '58', 1, 6);
INSERT INTO `permissions` VALUES (57, 311, 'Official Inviter', 'a:6:{s:22:\"site_can_invite_always\";i:1;s:15:\"site_can_invite\";i:1;s:27:\"site_send_unlimited_invites\";i:1;s:18:\"users_view_invites\";i:1;s:18:\"users_invite_notes\";i:1;s:11:\"MaxCollages\";s:1:\"0\";}', '0', '', 1, 6);
INSERT INTO `permissions` VALUES (58, 710, 'Legends', 'a:28:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:15:\"site_can_invite\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:18:\"users_view_friends\";i:1;s:23:\"users_override_paranoia\";i:1;s:11:\"staff_award\";i:1;s:13:\"torrents_edit\";i:1;s:19:\"self_torrents_check\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:11:\"MaxCollages\";s:1:\"6\";}', '0', '', 1, 6);
INSERT INTO `permissions` VALUES (60, 502, 'Super Elite TM', 'a:27:{s:10:\"site_leech\";i:1;s:11:\"site_upload\";i:1;s:9:\"site_vote\";i:1;s:20:\"site_submit_requests\";i:1;s:20:\"site_advanced_search\";i:1;s:10:\"site_top10\";i:1;s:20:\"site_torrents_notify\";i:1;s:20:\"site_collages_create\";i:1;s:20:\"site_collages_manage\";i:1;s:23:\"site_collages_subscribe\";i:1;s:22:\"site_collages_personal\";i:1;s:28:\"site_collages_renamepersonal\";i:1;s:19:\"site_advanced_top10\";i:1;s:16:\"site_album_votes\";i:1;s:19:\"site_make_bookmarks\";i:1;s:14:\"site_edit_wiki\";i:1;s:19:\"forums_polls_create\";i:1;s:15:\"site_delete_tag\";i:1;s:14:\"zip_downloader\";i:1;s:13:\"torrents_edit\";i:1;s:14:\"torrents_check\";i:1;s:19:\"self_torrents_check\";i:1;s:18:\"torrents_check_log\";i:1;s:19:\"torrents_add_artist\";i:1;s:13:\"edit_unknowns\";i:1;s:16:\"admin_manage_fls\";i:1;s:11:\"MaxCollages\";s:0:\"\";}', '0', '', 0, 6);

-- ----------------------------
-- Table structure for phinxlog
-- ----------------------------
DROP TABLE IF EXISTS `phinxlog`;
CREATE TABLE `phinxlog`  (
  `version` bigint(20) NOT NULL,
  `migration_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `start_time` timestamp(0) DEFAULT CURRENT_TIMESTAMP,
  `end_time` timestamp(0) DEFAULT CURRENT_TIMESTAMP,
  `breakpoint` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`version`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_conversations
-- ----------------------------
DROP TABLE IF EXISTS `pm_conversations`;
CREATE TABLE `pm_conversations`  (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `Subject` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 87285 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_conversations_users
-- ----------------------------
DROP TABLE IF EXISTS `pm_conversations_users`;
CREATE TABLE `pm_conversations_users`  (
  `UserID` int(10) NOT NULL DEFAULT 0,
  `ConvID` int(12) NOT NULL DEFAULT 0,
  `InInbox` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `InSentbox` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `SentDate` datetime(0) NOT NULL,
  `ReceivedDate` datetime(0) NOT NULL,
  `UnRead` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `Sticky` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `ForwardedTo` int(12) NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`, `ConvID`) USING BTREE,
  INDEX `InInbox`(`InInbox`) USING BTREE,
  INDEX `InSentbox`(`InSentbox`) USING BTREE,
  INDEX `ConvID`(`ConvID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `SentDate`(`SentDate`) USING BTREE,
  INDEX `ReceivedDate`(`ReceivedDate`) USING BTREE,
  INDEX `Sticky`(`Sticky`) USING BTREE,
  INDEX `ForwardedTo`(`ForwardedTo`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for pm_messages
-- ----------------------------
DROP TABLE IF EXISTS `pm_messages`;
CREATE TABLE `pm_messages`  (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `ConvID` int(12) NOT NULL DEFAULT 0,
  `SentDate` datetime(0) NOT NULL,
  `SenderID` int(10) NOT NULL DEFAULT 0,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ConvID`(`ConvID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 88725 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for push_notifications_usage
-- ----------------------------
DROP TABLE IF EXISTS `push_notifications_usage`;
CREATE TABLE `push_notifications_usage`  (
  `PushService` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `TimesUsed` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`PushService`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for register_apply
-- ----------------------------
DROP TABLE IF EXISTS `register_apply`;
CREATE TABLE `register_apply`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `email` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `site` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ipv4` char(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ipv6` char(63) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `site_ss` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `client_ss` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `introduction` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `apply_status` int(1) UNSIGNED ZEROFILL NOT NULL DEFAULT 0,
  `apply_pw` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `note` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `waring` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ts` timestamp(0) NOT NULL ON UPDATE CURRENT_TIMESTAMP(0),
  `ts_mod` timestamp(0) DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `id_mod` int(10) DEFAULT NULL,
  `addnote` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `c_red` int(1) UNSIGNED ZEROFILL NOT NULL,
  `c_ops` int(1) UNSIGNED ZEROFILL NOT NULL,
  `c_nwcd` int(1) UNSIGNED ZEROFILL NOT NULL,
  `c_opencd` int(1) UNSIGNED ZEROFILL NOT NULL,
  `c_others` int(1) UNSIGNED ZEROFILL NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for register_apply_link
-- ----------------------------
DROP TABLE IF EXISTS `register_apply_link`;
CREATE TABLE `register_apply_link`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `IP` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ApplyKey` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Used` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `ApplyKey`(`ApplyKey`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for register_apply_log
-- ----------------------------
DROP TABLE IF EXISTS `register_apply_log`;
CREATE TABLE `register_apply_log`  (
  `UserID` int(10) NOT NULL,
  `ApplyID` int(10) NOT NULL,
  `ApplyStatus` int(1) NOT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for reports
-- ----------------------------
DROP TABLE IF EXISTS `reports`;
CREATE TABLE `reports`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ThingID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Type` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Comment` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ResolverID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Status` enum('New','InProgress','Resolved') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'New',
  `ResolvedTime` datetime(0) NOT NULL,
  `ReportedTime` datetime(0) NOT NULL,
  `Reason` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ClaimerID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Notes` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Status`(`Status`) USING BTREE,
  INDEX `Type`(`Type`) USING BTREE,
  INDEX `ResolvedTime`(`ResolvedTime`) USING BTREE,
  INDEX `ResolverID`(`ResolverID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for reports_email_blacklist
-- ----------------------------
DROP TABLE IF EXISTS `reports_email_blacklist`;
CREATE TABLE `reports_email_blacklist`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Type` tinyint(4) NOT NULL DEFAULT 0,
  `UserID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  `Checked` tinyint(4) NOT NULL DEFAULT 0,
  `ResolverID` int(10) DEFAULT 0,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for reportsv2
-- ----------------------------
DROP TABLE IF EXISTS `reportsv2`;
CREATE TABLE `reportsv2`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ReporterID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TorrentID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Type` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  `UserComment` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ResolverID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Status` enum('New','InProgress','Resolved') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'New',
  `ReportedTime` datetime(0) NOT NULL,
  `LastChangeTime` datetime(0) NOT NULL,
  `ModComment` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Track` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Image` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ExtraID` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Link` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `LogMessage` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `UploaderReply` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ReplyTime` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Status`(`Status`) USING BTREE,
  INDEX `Type`(`Type`(1)) USING BTREE,
  INDEX `LastChangeTime`(`LastChangeTime`) USING BTREE,
  INDEX `TorrentID`(`TorrentID`) USING BTREE,
  INDEX `ResolverID`(`ResolverID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for requests
-- ----------------------------
DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL,
  `LastVote` datetime(0) DEFAULT NULL,
  `CategoryID` int(3) NOT NULL,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Year` int(4) DEFAULT NULL,
  `Image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ReleaseType` tinyint(2) DEFAULT NULL,
  `CatalogueNumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `BitrateList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FormatList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `MediaList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `LogCue` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FillerID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TorrentID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TimeFilled` datetime(0) NOT NULL,
  `Visible` binary(1) NOT NULL DEFAULT 1,
  `RecordLabel` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `GroupID` int(10) DEFAULT NULL,
  `OCLC` varchar(55) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Userid`(`UserID`) USING BTREE,
  INDEX `Name`(`Title`) USING BTREE,
  INDEX `Filled`(`TorrentID`) USING BTREE,
  INDEX `FillerID`(`FillerID`) USING BTREE,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE,
  INDEX `Year`(`Year`) USING BTREE,
  INDEX `TimeFilled`(`TimeFilled`) USING BTREE,
  INDEX `LastVote`(`LastVote`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for requests_artists
-- ----------------------------
DROP TABLE IF EXISTS `requests_artists`;
CREATE TABLE `requests_artists`  (
  `RequestID` int(10) UNSIGNED NOT NULL,
  `ArtistID` int(10) NOT NULL,
  `AliasID` int(10) NOT NULL,
  `Importance` enum('1','2','3','4','5','6','7') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`RequestID`, `AliasID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for requests_tags
-- ----------------------------
DROP TABLE IF EXISTS `requests_tags`;
CREATE TABLE `requests_tags`  (
  `TagID` int(10) NOT NULL DEFAULT 0,
  `RequestID` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`TagID`, `RequestID`) USING BTREE,
  INDEX `TagID`(`TagID`) USING BTREE,
  INDEX `RequestID`(`RequestID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for requests_votes
-- ----------------------------
DROP TABLE IF EXISTS `requests_votes`;
CREATE TABLE `requests_votes`  (
  `RequestID` int(10) NOT NULL DEFAULT 0,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Bounty` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`RequestID`, `UserID`) USING BTREE,
  INDEX `RequestID`(`RequestID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Bounty`(`Bounty`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for schedule
-- ----------------------------
DROP TABLE IF EXISTS `schedule`;
CREATE TABLE `schedule`  (
  `NextHour` int(2) NOT NULL DEFAULT 0,
  `NextDay` int(2) NOT NULL DEFAULT 0,
  `NextBiWeekly` int(2) NOT NULL DEFAULT 0,
  `NextMonth` int(2) NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for site_history
-- ----------------------------
DROP TABLE IF EXISTS `site_history`;
CREATE TABLE `site_history`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Url` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Category` tinyint(2) DEFAULT NULL,
  `SubCategory` tinyint(2) DEFAULT NULL,
  `Tags` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `AddedBy` int(10) DEFAULT NULL,
  `Date` datetime(0) DEFAULT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for site_options
-- ----------------------------
DROP TABLE IF EXISTS `site_options`;
CREATE TABLE `site_options`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Value` tinytext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Name`(`Name`) USING BTREE,
  INDEX `name_index`(`Name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sphinx_a
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_a`;
CREATE TABLE `sphinx_a`  (
  `gid` int(11) DEFAULT NULL,
  `aname` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  INDEX `gid`(`gid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sphinx_delta
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_delta`;
CREATE TABLE `sphinx_delta`  (
  `ID` int(10) NOT NULL,
  `GroupID` int(11) NOT NULL DEFAULT 0,
  `GroupName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ArtistName` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `TagList` varchar(728) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Year` int(4) DEFAULT NULL,
  `CatalogueNumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RecordLabel` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CategoryID` tinyint(2) DEFAULT NULL,
  `Time` int(12) DEFAULT NULL,
  `ReleaseType` tinyint(2) DEFAULT NULL,
  `Size` bigint(20) DEFAULT NULL,
  `Snatched` int(10) DEFAULT NULL,
  `Seeders` int(10) DEFAULT NULL,
  `Leechers` int(10) DEFAULT NULL,
  `LogScore` int(3) DEFAULT NULL,
  `Scene` tinyint(1) NOT NULL DEFAULT 0,
  `Jinzhuan` tinyint(1) NOT NULL DEFAULT 0,
  `Diy` tinyint(1) NOT NULL DEFAULT 0,
  `Buy` tinyint(1) NOT NULL DEFAULT 0,
  `Allow` tinyint(1) NOT NULL DEFAULT 0,
  `VanityHouse` tinyint(1) NOT NULL DEFAULT 0,
  `HasLog` tinyint(1) DEFAULT NULL,
  `HasCue` tinyint(1) DEFAULT NULL,
  `LogChecksum` tinyint(1) NOT NULL DEFAULT 0,
  `FreeTorrent` tinyint(1) DEFAULT NULL,
  `Media` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Format` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Encoding` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RemasterYear` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `RemasterTitle` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RemasterRecordLabel` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RemasterCatalogueNumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FileList` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Description` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `VoteScore` float NOT NULL DEFAULT 0,
  `LastChanged` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `Size`(`Size`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sphinx_hash
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_hash`;
CREATE TABLE `sphinx_hash`  (
  `ID` int(10) NOT NULL,
  `GroupName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ArtistName` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `TagList` varchar(728) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Year` int(4) DEFAULT NULL,
  `CatalogueNumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RecordLabel` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CategoryID` tinyint(2) DEFAULT NULL,
  `Time` int(12) DEFAULT NULL,
  `ReleaseType` tinyint(2) DEFAULT NULL,
  `Size` bigint(20) DEFAULT NULL,
  `Snatched` int(10) DEFAULT NULL,
  `Seeders` int(10) DEFAULT NULL,
  `Leechers` int(10) DEFAULT NULL,
  `LogScore` int(3) DEFAULT NULL,
  `Scene` tinyint(1) NOT NULL DEFAULT 0,
  `VanityHouse` tinyint(1) NOT NULL DEFAULT 0,
  `HasLog` tinyint(1) DEFAULT NULL,
  `HasCue` tinyint(1) DEFAULT NULL,
  `FreeTorrent` tinyint(1) DEFAULT NULL,
  `Media` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Format` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Encoding` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RemasterYear` int(4) DEFAULT NULL,
  `RemasterTitle` varchar(512) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RemasterRecordLabel` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `RemasterCatalogueNumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FileList` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for sphinx_index_last_pos
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_index_last_pos`;
CREATE TABLE `sphinx_index_last_pos`  (
  `Type` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `ID` int(11) DEFAULT NULL,
  PRIMARY KEY (`Type`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sphinx_requests
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_requests`;
CREATE TABLE `sphinx_requests`  (
  `ID` int(10) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TimeAdded` int(12) UNSIGNED NOT NULL,
  `LastVote` int(12) UNSIGNED NOT NULL,
  `CategoryID` int(3) NOT NULL,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Year` int(4) DEFAULT NULL,
  `ArtistList` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ReleaseType` tinyint(2) DEFAULT NULL,
  `CatalogueNumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `BitrateList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FormatList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `MediaList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `LogCue` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FillerID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TorrentID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TimeFilled` int(12) UNSIGNED NOT NULL,
  `Visible` binary(1) NOT NULL DEFAULT 1,
  `Bounty` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `Votes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `RecordLabel` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Userid`(`UserID`) USING BTREE,
  INDEX `Name`(`Title`) USING BTREE,
  INDEX `Filled`(`TorrentID`) USING BTREE,
  INDEX `FillerID`(`FillerID`) USING BTREE,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE,
  INDEX `Year`(`Year`) USING BTREE,
  INDEX `TimeFilled`(`TimeFilled`) USING BTREE,
  INDEX `LastVote`(`LastVote`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sphinx_requests_delta
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_requests_delta`;
CREATE TABLE `sphinx_requests_delta`  (
  `ID` int(10) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TimeAdded` int(12) UNSIGNED DEFAULT NULL,
  `LastVote` int(12) UNSIGNED DEFAULT NULL,
  `CategoryID` tinyint(4) DEFAULT NULL,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `TagList` varchar(728) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Year` int(4) DEFAULT NULL,
  `ArtistList` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ReleaseType` tinyint(2) DEFAULT NULL,
  `CatalogueNumber` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `BitrateList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FormatList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `MediaList` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `LogCue` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `FillerID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TorrentID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `TimeFilled` int(12) UNSIGNED DEFAULT NULL,
  `Visible` binary(1) NOT NULL DEFAULT 1,
  `Bounty` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `Votes` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `RecordLabel` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Userid`(`UserID`) USING BTREE,
  INDEX `Name`(`Title`) USING BTREE,
  INDEX `Filled`(`TorrentID`) USING BTREE,
  INDEX `FillerID`(`FillerID`) USING BTREE,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE,
  INDEX `Year`(`Year`) USING BTREE,
  INDEX `TimeFilled`(`TimeFilled`) USING BTREE,
  INDEX `LastVote`(`LastVote`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sphinx_t
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_t`;
CREATE TABLE `sphinx_t`  (
  `id` int(11) NOT NULL,
  `gid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `size` bigint(20) NOT NULL,
  `snatched` int(11) NOT NULL,
  `seeders` int(11) NOT NULL,
  `leechers` int(11) NOT NULL,
  `time` int(11) NOT NULL,
  `logscore` smallint(6) NOT NULL,
  `scene` tinyint(4) NOT NULL,
  `jinzhuan` tinyint(4) NOT NULL,
  `diy` tinyint(4) NOT NULL,
  `buy` tinyint(4) NOT NULL,
  `allow` tinyint(4) NOT NULL,
  `haslog` tinyint(4) NOT NULL,
  `hascue` tinyint(4) NOT NULL,
  `logchecksum` tinyint(4) NOT NULL,
  `freetorrent` tinyint(4) NOT NULL,
  `media` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `resolution` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `maker` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `container` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `subtitle` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `codec` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `format` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `source` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `encoding` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `remyear` smallint(6) NOT NULL,
  `remtitle` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `remrlabel` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `remcnumber` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `filelist` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `remident` int(10) UNSIGNED NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `gid_remident`(`gid`, `remident`) USING BTREE,
  INDEX `format`(`format`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for sphinx_tg
-- ----------------------------
DROP TABLE IF EXISTS `sphinx_tg`;
CREATE TABLE `sphinx_tg`  (
  `id` int(11) NOT NULL,
  `name` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tags` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `year` smallint(6) DEFAULT NULL,
  `rlabel` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `cnumber` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `catid` smallint(6) DEFAULT NULL,
  `reltype` smallint(6) DEFAULT NULL,
  `vanityhouse` tinyint(4) DEFAULT NULL,
  `subname` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for staff_answers
-- ----------------------------
DROP TABLE IF EXISTS `staff_answers`;
CREATE TABLE `staff_answers`  (
  `QuestionID` int(10) NOT NULL,
  `UserID` int(10) NOT NULL,
  `Answer` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Date` datetime(0) NOT NULL,
  PRIMARY KEY (`QuestionID`, `UserID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for staff_blog
-- ----------------------------
DROP TABLE IF EXISTS `staff_blog`;
CREATE TABLE `staff_blog`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Title` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL,
  `ThreadID` int(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for staff_blog_visits
-- ----------------------------
DROP TABLE IF EXISTS `staff_blog_visits`;
CREATE TABLE `staff_blog_visits`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `Time` datetime(0) NOT NULL,
  UNIQUE INDEX `UserID`(`UserID`) USING BTREE,
  CONSTRAINT `staff_blog_visits_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for staff_groups
-- ----------------------------
DROP TABLE IF EXISTS `staff_groups`;
CREATE TABLE `staff_groups`  (
  `ID` int(3) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Sort` int(4) UNSIGNED NOT NULL,
  `Name` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Name`(`Name`(50)) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of staff_groups
-- ----------------------------
INSERT INTO `staff_groups` VALUES (1, 7, 'Moderators');
INSERT INTO `staff_groups` VALUES (2, 8, 'Human Resource');
INSERT INTO `staff_groups` VALUES (3, 9, 'Developers');
INSERT INTO `staff_groups` VALUES (4, 10, 'Administrators');
INSERT INTO `staff_groups` VALUES (5, 2, 'Official Staffs');
INSERT INTO `staff_groups` VALUES (6, 1, 'Secondary Class');

-- ----------------------------
-- Table structure for staff_ignored_questions
-- ----------------------------
DROP TABLE IF EXISTS `staff_ignored_questions`;
CREATE TABLE `staff_ignored_questions`  (
  `QuestionID` int(10) NOT NULL,
  `UserID` int(10) NOT NULL,
  PRIMARY KEY (`QuestionID`, `UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for staff_pm_conversations
-- ----------------------------
DROP TABLE IF EXISTS `staff_pm_conversations`;
CREATE TABLE `staff_pm_conversations`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Subject` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `UserID` int(11) DEFAULT NULL,
  `Status` enum('Open','Unanswered','Resolved') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Level` int(11) DEFAULT NULL,
  `AssignedToUser` int(11) DEFAULT NULL,
  `Date` datetime(0) DEFAULT NULL,
  `Unread` tinyint(1) DEFAULT NULL,
  `ResolverID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `StatusAssigned`(`Status`, `AssignedToUser`) USING BTREE,
  INDEX `StatusLevel`(`Status`, `Level`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for staff_pm_messages
-- ----------------------------
DROP TABLE IF EXISTS `staff_pm_messages`;
CREATE TABLE `staff_pm_messages`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `SentDate` datetime(0) DEFAULT NULL,
  `Message` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `ConvID` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for staff_pm_responses
-- ----------------------------
DROP TABLE IF EXISTS `staff_pm_responses`;
CREATE TABLE `staff_pm_responses`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Message` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Name` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for styles_backup
-- ----------------------------
DROP TABLE IF EXISTS `styles_backup`;
CREATE TABLE `styles_backup`  (
  `UserID` int(10) NOT NULL DEFAULT 0,
  `StyleID` int(10) DEFAULT NULL,
  `StyleURL` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`UserID`) USING BTREE,
  INDEX `StyleURL`(`StyleURL`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for stylesheets
-- ----------------------------
DROP TABLE IF EXISTS `stylesheets`;
CREATE TABLE `stylesheets`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Default` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 34 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Records of stylesheets
-- ----------------------------
INSERT INTO `stylesheets` VALUES (35, 'GPW Dark Mono', 'Dark Mono', '1');

-- ----------------------------
-- Table structure for tag_aliases
-- ----------------------------
DROP TABLE IF EXISTS `tag_aliases`;
CREATE TABLE `tag_aliases`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `BadTag` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `AliasTag` varchar(30) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `BadTag`(`BadTag`) USING BTREE,
  INDEX `AliasTag`(`AliasTag`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for tags
-- ----------------------------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `TagType` enum('genre','other') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'other',
  `Uses` int(12) NOT NULL DEFAULT 1,
  `UserID` int(10) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Name_2`(`Name`) USING BTREE,
  INDEX `TagType`(`TagType`) USING BTREE,
  INDEX `Uses`(`Uses`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 43884 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for thread
-- ----------------------------
DROP TABLE IF EXISTS `thread`;
CREATE TABLE `thread`  (
  `ID` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ThreadTypeID` int(6) UNSIGNED NOT NULL,
  `Created` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ThreadTypeID`(`ThreadTypeID`) USING BTREE,
  CONSTRAINT `thread_ibfk_1` FOREIGN KEY (`ThreadTypeID`) REFERENCES `thread_type` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for thread_note
-- ----------------------------
DROP TABLE IF EXISTS `thread_note`;
CREATE TABLE `thread_note`  (
  `ID` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ThreadID` int(6) UNSIGNED NOT NULL,
  `Created` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0),
  `UserID` int(10) UNSIGNED NOT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Visibility` enum('staff','public') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ThreadID`(`ThreadID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  CONSTRAINT `thread_note_ibfk_1` FOREIGN KEY (`ThreadID`) REFERENCES `thread` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `thread_note_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for thread_type
-- ----------------------------
DROP TABLE IF EXISTS `thread_type`;
CREATE TABLE `thread_type`  (
  `ID` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Name` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Name`(`Name`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for thumb
-- ----------------------------
DROP TABLE IF EXISTS `thumb`;
CREATE TABLE `thumb`  (
  `ItemID` int(10) NOT NULL,
  `Type` enum('post','profile','wiki','torrent') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `FromUserID` int(10) NOT NULL,
  `ToUserID` int(10) NOT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ItemID`, `Type`, `FromUserID`, `ToUserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tokens_typed
-- ----------------------------
DROP TABLE IF EXISTS `tokens_typed`;
CREATE TABLE `tokens_typed`  (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `EndTime` date DEFAULT NULL,
  `Type` enum('count','time') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserID` int(10) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for top10_history
-- ----------------------------
DROP TABLE IF EXISTS `top10_history`;
CREATE TABLE `top10_history`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Date` datetime(0) NOT NULL,
  `Type` enum('Daily','Weekly') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9029 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for top10_history_torrents
-- ----------------------------
DROP TABLE IF EXISTS `top10_history_torrents`;
CREATE TABLE `top10_history_torrents`  (
  `HistoryID` int(10) NOT NULL DEFAULT 0,
  `Rank` tinyint(2) NOT NULL DEFAULT 0,
  `TorrentID` int(10) NOT NULL DEFAULT 0,
  `TitleString` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `TagString` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT ''
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents
-- ----------------------------
DROP TABLE IF EXISTS `torrents`;
CREATE TABLE `torrents`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `GroupID` int(10) NOT NULL,
  `UserID` int(10) DEFAULT NULL,
  `Media` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Format` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Encoding` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Remastered` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `RemasterYear` int(4) DEFAULT NULL,
  `RemasterTitle` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `RemasterCatalogueNumber` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `RemasterRecordLabel` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Scene` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Jinzhuan` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Diy` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Buy` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Allow` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `HasLog` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `HasCue` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `HasLogDB` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `LogScore` int(6) NOT NULL DEFAULT 0,
  `LogChecksum` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `info_hash` blob NOT NULL,
  `FileCount` int(6) NOT NULL,
  `FileList` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `FilePath` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Size` bigint(12) NOT NULL,
  `Leechers` int(6) NOT NULL DEFAULT 0,
  `Seeders` int(6) NOT NULL DEFAULT 0,
  `last_action` datetime(0) NOT NULL,
  `FreeTorrent` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `FreeLeechType` enum('0','1','2','3','4','5','6','7') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Time` datetime(0) NOT NULL,
  `Description` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Snatched` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `balance` bigint(20) NOT NULL DEFAULT 0,
  `LastReseedRequest` datetime(0) NOT NULL,
  `TranscodedFrom` int(10) NOT NULL DEFAULT 0,
  `Checked` int(10) NOT NULL,
  `NotMainMovie` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0',
  `Source` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Codec` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Container` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Resolution` varchar(10) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Subtitles` set('chinese_simplified','chinese_traditional','english','japanese','korean','no_subtitles','arabic','brazilian_port','bulgarian','croatian','czech','danish','dutch','estonian','finnish','french','german','greek','hebrew','hindi','hungarian','icelandic','indonesian','italian','latvian','lithuanian','norwegian','persian','polish','portuguese','romanian','russian','serbian','slovak','slovenian','spanish','swedish','thai','turkish','ukrainian','vietnamese') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Makers` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `InfoHash`(`info_hash`(40)) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Media`(`Media`) USING BTREE,
  INDEX `Format`(`Format`) USING BTREE,
  INDEX `Encoding`(`Encoding`) USING BTREE,
  INDEX `Year`(`RemasterYear`) USING BTREE,
  INDEX `FileCount`(`FileCount`) USING BTREE,
  INDEX `Size`(`Size`) USING BTREE,
  INDEX `Seeders`(`Seeders`) USING BTREE,
  INDEX `Leechers`(`Leechers`) USING BTREE,
  INDEX `Snatched`(`Snatched`) USING BTREE,
  INDEX `last_action`(`last_action`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE,
  INDEX `FreeTorrent`(`FreeTorrent`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18185 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_artists
-- ----------------------------
DROP TABLE IF EXISTS `torrents_artists`;
CREATE TABLE `torrents_artists`  (
  `GroupID` int(10) NOT NULL,
  `ArtistID` int(10) NOT NULL,
  `AliasID` int(10) NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Importance` enum('1','2','3','4','5','6','7') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  PRIMARY KEY (`GroupID`, `ArtistID`, `Importance`) USING BTREE,
  INDEX `ArtistID`(`ArtistID`) USING BTREE,
  INDEX `AliasID`(`AliasID`) USING BTREE,
  INDEX `Importance`(`Importance`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_bad_compress
-- ----------------------------
DROP TABLE IF EXISTS `torrents_bad_compress`;
CREATE TABLE `torrents_bad_compress`  (
  `TorrentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TimeAdded` datetime(0) NOT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for torrents_bad_files
-- ----------------------------
DROP TABLE IF EXISTS `torrents_bad_files`;
CREATE TABLE `torrents_bad_files`  (
  `TorrentID` int(11) NOT NULL DEFAULT 0,
  `UserID` int(11) NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_bad_folders
-- ----------------------------
DROP TABLE IF EXISTS `torrents_bad_folders`;
CREATE TABLE `torrents_bad_folders`  (
  `TorrentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TimeAdded` datetime(0) NOT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_bad_img
-- ----------------------------
DROP TABLE IF EXISTS `torrents_bad_img`;
CREATE TABLE `torrents_bad_img`  (
  `TorrentID` int(11) NOT NULL DEFAULT 0,
  `UserID` int(11) NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for torrents_bad_tags
-- ----------------------------
DROP TABLE IF EXISTS `torrents_bad_tags`;
CREATE TABLE `torrents_bad_tags`  (
  `TorrentID` int(10) NOT NULL DEFAULT 0,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_balance_history
-- ----------------------------
DROP TABLE IF EXISTS `torrents_balance_history`;
CREATE TABLE `torrents_balance_history`  (
  `TorrentID` int(10) NOT NULL,
  `GroupID` int(10) NOT NULL,
  `balance` bigint(20) NOT NULL,
  `Time` datetime(0) NOT NULL,
  `Last` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0',
  UNIQUE INDEX `TorrentID_2`(`TorrentID`, `Time`) USING BTREE,
  UNIQUE INDEX `TorrentID_3`(`TorrentID`, `balance`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_cassette_approved
-- ----------------------------
DROP TABLE IF EXISTS `torrents_cassette_approved`;
CREATE TABLE `torrents_cassette_approved`  (
  `TorrentID` int(10) NOT NULL DEFAULT 0,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_check
-- ----------------------------
DROP TABLE IF EXISTS `torrents_check`;
CREATE TABLE `torrents_check`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `UserID` int(10) NOT NULL,
  `TorrentID` int(10) NOT NULL,
  `Type` int(1) NOT NULL,
  `Message` varchar(250) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for torrents_custom_trumpable
-- ----------------------------
DROP TABLE IF EXISTS `torrents_custom_trumpable`;
CREATE TABLE `torrents_custom_trumpable`  (
  `TorrentID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TimeAdded` datetime(0) NOT NULL,
  `CustomTrumpable` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for torrents_files
-- ----------------------------
DROP TABLE IF EXISTS `torrents_files`;
CREATE TABLE `torrents_files`  (
  `TorrentID` int(10) NOT NULL,
  `File` mediumblob NOT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for torrents_group
-- ----------------------------
DROP TABLE IF EXISTS `torrents_group`;
CREATE TABLE `torrents_group`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `ArtistID` int(10) DEFAULT NULL,
  `CategoryID` int(3) DEFAULT NULL,
  `Name` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `SubName` varchar(300) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Year` int(4) DEFAULT NULL,
  `CatalogueNumber` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `RecordLabel` varchar(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `ReleaseType` tinyint(2) DEFAULT 21,
  `TagList` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Time` datetime(0) NOT NULL,
  `RevisionID` int(12) DEFAULT NULL,
  `WikiBody` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `WikiImage` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `VanityHouse` tinyint(1) DEFAULT 0,
  `IMDBID` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `TrailerLink` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `ArtistID`(`ArtistID`) USING BTREE,
  INDEX `CategoryID`(`CategoryID`) USING BTREE,
  INDEX `Name`(`Name`(255)) USING BTREE,
  INDEX `Year`(`Year`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE,
  INDEX `RevisionID`(`RevisionID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 15790 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_logs
-- ----------------------------
DROP TABLE IF EXISTS `torrents_logs`;
CREATE TABLE `torrents_logs`  (
  `LogID` int(10) NOT NULL AUTO_INCREMENT,
  `TorrentID` int(10) NOT NULL DEFAULT 0,
  `Log` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `FileName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Details` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Score` int(3) NOT NULL,
  `Checksum` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `Adjusted` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `AdjustedScore` int(3) NOT NULL,
  `AdjustedChecksum` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `AdjustedBy` int(10) NOT NULL DEFAULT 0,
  `AdjustmentReason` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `AdjustmentDetails` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  PRIMARY KEY (`LogID`) USING BTREE,
  INDEX `TorrentID`(`TorrentID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_lossymaster_approved
-- ----------------------------
DROP TABLE IF EXISTS `torrents_lossymaster_approved`;
CREATE TABLE `torrents_lossymaster_approved`  (
  `TorrentID` int(10) NOT NULL DEFAULT 0,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_lossyweb_approved
-- ----------------------------
DROP TABLE IF EXISTS `torrents_lossyweb_approved`;
CREATE TABLE `torrents_lossyweb_approved`  (
  `TorrentID` int(10) NOT NULL DEFAULT 0,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_missing_lineage
-- ----------------------------
DROP TABLE IF EXISTS `torrents_missing_lineage`;
CREATE TABLE `torrents_missing_lineage`  (
  `TorrentID` int(10) NOT NULL DEFAULT 0,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `TimeAdded` datetime(0) NOT NULL,
  INDEX `TimeAdded`(`TimeAdded`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_peerlists
-- ----------------------------
DROP TABLE IF EXISTS `torrents_peerlists`;
CREATE TABLE `torrents_peerlists`  (
  `TorrentID` int(11) NOT NULL,
  `GroupID` int(11) DEFAULT NULL,
  `Seeders` int(11) DEFAULT NULL,
  `Leechers` int(11) DEFAULT NULL,
  `Snatches` int(11) DEFAULT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `Stats`(`TorrentID`, `Seeders`, `Leechers`, `Snatches`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_peerlists_compare
-- ----------------------------
DROP TABLE IF EXISTS `torrents_peerlists_compare`;
CREATE TABLE `torrents_peerlists_compare`  (
  `TorrentID` int(11) NOT NULL,
  `GroupID` int(11) DEFAULT NULL,
  `Seeders` int(11) DEFAULT NULL,
  `Leechers` int(11) DEFAULT NULL,
  `Snatches` int(11) DEFAULT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `Stats`(`TorrentID`, `Seeders`, `Leechers`, `Snatches`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_recommended
-- ----------------------------
DROP TABLE IF EXISTS `torrents_recommended`;
CREATE TABLE `torrents_recommended`  (
  `GroupID` int(10) NOT NULL,
  `UserID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  PRIMARY KEY (`GroupID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_send_bonus
-- ----------------------------
DROP TABLE IF EXISTS `torrents_send_bonus`;
CREATE TABLE `torrents_send_bonus`  (
  `TorrentID` int(11) NOT NULL,
  `FromUserID` int(11) NOT NULL,
  `Bonus` int(11) NOT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`TorrentID`, `FromUserID`, `Bonus`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for torrents_tags
-- ----------------------------
DROP TABLE IF EXISTS `torrents_tags`;
CREATE TABLE `torrents_tags`  (
  `TagID` int(10) NOT NULL DEFAULT 0,
  `GroupID` int(10) NOT NULL DEFAULT 0,
  `PositiveVotes` int(6) NOT NULL DEFAULT 1,
  `NegativeVotes` int(6) NOT NULL DEFAULT 1,
  `UserID` int(10) DEFAULT NULL,
  PRIMARY KEY (`TagID`, `GroupID`) USING BTREE,
  INDEX `TagID`(`TagID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `PositiveVotes`(`PositiveVotes`) USING BTREE,
  INDEX `NegativeVotes`(`NegativeVotes`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_tags_votes
-- ----------------------------
DROP TABLE IF EXISTS `torrents_tags_votes`;
CREATE TABLE `torrents_tags_votes`  (
  `GroupID` int(10) NOT NULL,
  `TagID` int(10) NOT NULL,
  `UserID` int(10) NOT NULL,
  `Way` enum('up','down') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'up',
  PRIMARY KEY (`GroupID`, `TagID`, `UserID`, `Way`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for torrents_votes
-- ----------------------------
DROP TABLE IF EXISTS `torrents_votes`;
CREATE TABLE `torrents_votes`  (
  `GroupID` int(10) NOT NULL,
  `Ups` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Total` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `Score` float NOT NULL DEFAULT 0,
  PRIMARY KEY (`GroupID`) USING BTREE,
  INDEX `Score`(`Score`) USING BTREE,
  CONSTRAINT `torrents_votes_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `torrents_group` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for upload_contest
-- ----------------------------
DROP TABLE IF EXISTS `upload_contest`;
CREATE TABLE `upload_contest`  (
  `TorrentID` int(10) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`TorrentID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  CONSTRAINT `upload_contest_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for user_questions
-- ----------------------------
DROP TABLE IF EXISTS `user_questions`;
CREATE TABLE `user_questions`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Question` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserID` int(10) NOT NULL,
  `Date` datetime(0) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Date`(`Date`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_collage_subs
-- ----------------------------
DROP TABLE IF EXISTS `users_collage_subs`;
CREATE TABLE `users_collage_subs`  (
  `UserID` int(10) NOT NULL,
  `CollageID` int(10) NOT NULL,
  `LastVisit` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`UserID`, `CollageID`) USING BTREE,
  INDEX `CollageID`(`CollageID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_comments_last_read
-- ----------------------------
DROP TABLE IF EXISTS `users_comments_last_read`;
CREATE TABLE `users_comments_last_read`  (
  `UserID` int(10) NOT NULL,
  `Page` enum('artist','collages','requests','torrents') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `PageID` int(10) NOT NULL,
  `PostID` int(10) NOT NULL,
  PRIMARY KEY (`UserID`, `Page`, `PageID`) USING BTREE,
  INDEX `Page`(`Page`, `PageID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_donor_ranks
-- ----------------------------
DROP TABLE IF EXISTS `users_donor_ranks`;
CREATE TABLE `users_donor_ranks`  (
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Rank` tinyint(2) NOT NULL DEFAULT 0,
  `Foundrank` tinyint(2) NOT NULL DEFAULT 0,
  `DonationTime` datetime(0) DEFAULT NULL,
  `FoundTime` datetime(0) DEFAULT NULL,
  `Hidden` tinyint(2) NOT NULL DEFAULT 0,
  `TotalRank` int(10) NOT NULL DEFAULT 0,
  `SpecialRank` tinyint(2) DEFAULT 0,
  `InvitesRecievedRank` tinyint(4) DEFAULT 0,
  `RankExpirationTime` datetime(0) DEFAULT NULL,
  `FoundExpirationTime` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`UserID`) USING BTREE,
  INDEX `DonationTime`(`DonationTime`) USING BTREE,
  INDEX `SpecialRank`(`SpecialRank`) USING BTREE,
  INDEX `Rank`(`Rank`) USING BTREE,
  INDEX `TotalRank`(`TotalRank`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_downloads
-- ----------------------------
DROP TABLE IF EXISTS `users_downloads`;
CREATE TABLE `users_downloads`  (
  `UserID` int(10) NOT NULL,
  `TorrentID` int(1) NOT NULL,
  `Time` datetime(0) NOT NULL,
  PRIMARY KEY (`UserID`, `TorrentID`, `Time`) USING BTREE,
  INDEX `TorrentID`(`TorrentID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_dupes
-- ----------------------------
DROP TABLE IF EXISTS `users_dupes`;
CREATE TABLE `users_dupes`  (
  `GroupID` int(10) UNSIGNED NOT NULL,
  `UserID` int(10) UNSIGNED NOT NULL,
  UNIQUE INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  CONSTRAINT `users_dupes_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `users_dupes_ibfk_2` FOREIGN KEY (`GroupID`) REFERENCES `dupe_groups` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_enable_recommendations
-- ----------------------------
DROP TABLE IF EXISTS `users_enable_recommendations`;
CREATE TABLE `users_enable_recommendations`  (
  `ID` int(10) NOT NULL,
  `Enable` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `Enable`(`Enable`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_enable_requests
-- ----------------------------
DROP TABLE IF EXISTS `users_enable_requests`;
CREATE TABLE `users_enable_requests`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(10) UNSIGNED NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `IP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.0.0.0',
  `UserAgent` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Timestamp` datetime(0) NOT NULL,
  `HandledTimestamp` datetime(0) DEFAULT NULL,
  `Token` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `CheckedBy` int(10) UNSIGNED DEFAULT NULL,
  `Outcome` tinyint(1) DEFAULT NULL COMMENT '1 for approved, 2 for denied, 3 for discarded',
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserId`(`UserID`) USING BTREE,
  INDEX `CheckedBy`(`CheckedBy`) USING BTREE,
  CONSTRAINT `users_enable_requests_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `users_enable_requests_ibfk_2` FOREIGN KEY (`CheckedBy`) REFERENCES `users_main` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_freeleeches
-- ----------------------------
DROP TABLE IF EXISTS `users_freeleeches`;
CREATE TABLE `users_freeleeches`  (
  `UserID` int(10) NOT NULL,
  `TorrentID` int(10) NOT NULL,
  `Time` datetime(0) NOT NULL,
  `Expired` tinyint(1) NOT NULL DEFAULT 0,
  `Downloaded` bigint(20) NOT NULL DEFAULT 0,
  `Uses` int(10) NOT NULL DEFAULT 1,
  PRIMARY KEY (`UserID`, `TorrentID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE,
  INDEX `Expired_Time`(`Expired`, `Time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_freeleeches_time
-- ----------------------------
DROP TABLE IF EXISTS `users_freeleeches_time`;
CREATE TABLE `users_freeleeches_time`  (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `TorrentID` int(11) NOT NULL,
  `Time` datetime(0) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users_freetorrents
-- ----------------------------
DROP TABLE IF EXISTS `users_freetorrents`;
CREATE TABLE `users_freetorrents`  (
  `UserID` int(10) NOT NULL,
  `TorrentID` int(10) NOT NULL,
  `FreeTorrent` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Time` datetime(0) NOT NULL,
  `Uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `Downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`, `TorrentID`, `FreeTorrent`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_geodistribution
-- ----------------------------
DROP TABLE IF EXISTS `users_geodistribution`;
CREATE TABLE `users_geodistribution`  (
  `Code` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Users` int(10) NOT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_history_emails
-- ----------------------------
DROP TABLE IF EXISTS `users_history_emails`;
CREATE TABLE `users_history_emails`  (
  `UserID` int(10) NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Time` datetime(0) DEFAULT NULL,
  `IP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  INDEX `UserID`(`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_history_ips
-- ----------------------------
DROP TABLE IF EXISTS `users_history_ips`;
CREATE TABLE `users_history_ips`  (
  `UserID` int(10) NOT NULL,
  `IP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.0.0.0',
  `StartTime` datetime(0) NOT NULL,
  `EndTime` datetime(0) DEFAULT NULL,
  PRIMARY KEY (`UserID`, `IP`, `StartTime`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `IP`(`IP`) USING BTREE,
  INDEX `StartTime`(`StartTime`) USING BTREE,
  INDEX `EndTime`(`EndTime`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_history_passkeys
-- ----------------------------
DROP TABLE IF EXISTS `users_history_passkeys`;
CREATE TABLE `users_history_passkeys`  (
  `UserID` int(10) NOT NULL,
  `OldPassKey` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `NewPassKey` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `ChangeTime` datetime(0) DEFAULT NULL,
  `ChangerIP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_history_passwords
-- ----------------------------
DROP TABLE IF EXISTS `users_history_passwords`;
CREATE TABLE `users_history_passwords`  (
  `UserID` int(10) NOT NULL,
  `ChangeTime` datetime(0) DEFAULT NULL,
  `ChangerIP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  INDEX `User_Time`(`UserID`, `ChangeTime`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_info
-- ----------------------------
DROP TABLE IF EXISTS `users_info`;
CREATE TABLE `users_info`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `StyleID` int(10) UNSIGNED NOT NULL,
  `StyleURL` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Info` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `AdminComment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `SiteOptions` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ViewAvatars` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `Donor` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Found` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Artist` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DownloadAlt` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Warned` datetime(0) NOT NULL,
  `SupportFor` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `TorrentGrouping` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '0=Open,1=Closed,2=Off',
  `ShowTags` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `NotifyOnQuote` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `AuthKey` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ResetKey` varchar(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ResetExpires` datetime(0) NOT NULL,
  `JoinDate` datetime(0) NOT NULL,
  `Inviter` int(10) DEFAULT NULL,
  `BitcoinAddress` varchar(34) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `WarnedTimes` int(2) NOT NULL DEFAULT 0,
  `DisableAvatar` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisableInvites` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisablePosting` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisableForums` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisablePoints` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisableIRC` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0',
  `DisableTagging` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisableUpload` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisableWiki` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisablePM` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `RatioWatchEnds` datetime(0) NOT NULL,
  `RatioWatchDownload` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `RatioWatchTimes` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `BanDate` datetime(0) NOT NULL,
  `BanReason` enum('0','1','2','3','4') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `CatchupTime` datetime(0) DEFAULT NULL,
  `LastReadNews` int(10) NOT NULL DEFAULT 0,
  `HideCountryChanges` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `RestrictedForums` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `DisableRequests` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `PermittedForums` varchar(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `UnseededAlerts` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `ReportedAlerts` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `RequestsAlerts` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `LastReadBlog` int(10) NOT NULL DEFAULT 0,
  `InfoTitle` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `NotifyOnDeleteSeeding` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `NotifyOnDeleteSnatched` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `NotifyOnDeleteDownloaded` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `Lang` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'chs',
  `DisableCheckAll` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `DisableCheckSelf` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `TGID` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  UNIQUE INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `SupportFor`(`SupportFor`) USING BTREE,
  INDEX `DisableInvites`(`DisableInvites`) USING BTREE,
  INDEX `Donor`(`Donor`) USING BTREE,
  INDEX `Warned`(`Warned`) USING BTREE,
  INDEX `JoinDate`(`JoinDate`) USING BTREE,
  INDEX `Inviter`(`Inviter`) USING BTREE,
  INDEX `RatioWatchEnds`(`RatioWatchEnds`) USING BTREE,
  INDEX `RatioWatchDownload`(`RatioWatchDownload`) USING BTREE,
  INDEX `BitcoinAddress`(`BitcoinAddress`(4)) USING BTREE,
  INDEX `AuthKey`(`AuthKey`) USING BTREE,
  INDEX `ResetKey`(`ResetKey`) USING BTREE,
  INDEX `Found`(`Found`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_last_month
-- ----------------------------
DROP TABLE IF EXISTS `users_last_month`;
CREATE TABLE `users_last_month`  (
  `ID` int(10) UNSIGNED NOT NULL,
  `Downloaded` bigint(20) UNSIGNED NOT NULL,
  `TorrentCnt` int(11) NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users_levels
-- ----------------------------
DROP TABLE IF EXISTS `users_levels`;
CREATE TABLE `users_levels`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `PermissionID` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`UserID`, `PermissionID`) USING BTREE,
  INDEX `PermissionID`(`PermissionID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_main
-- ----------------------------
DROP TABLE IF EXISTS `users_main`;
CREATE TABLE `users_main`  (
  `ID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Username` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `PassHash` varchar(60) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Secret` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `IRCKey` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `LastLogin` datetime(0) NOT NULL,
  `LastAccess` datetime(0) NOT NULL,
  `IP` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0.0.0.0',
  `Class` tinyint(2) NOT NULL DEFAULT 5,
  `Uploaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `Downloaded` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `BonusPoints` float(20, 5) NOT NULL DEFAULT 0.00000,
  `Title` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Enabled` enum('0','1','2') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Paranoia` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Visible` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `Invites` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `PermissionID` int(10) UNSIGNED NOT NULL,
  `AwardLevel` int(11) NOT NULL DEFAULT 0,
  `CustomPermissions` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `can_leech` tinyint(4) NOT NULL DEFAULT 1,
  `torrent_pass` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `RequiredRatio` double(10, 8) NOT NULL DEFAULT 0.00000000,
  `RequiredRatioWork` double(10, 8) NOT NULL DEFAULT 0.00000000,
  `ipcc` varchar(2) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `FLTokens` int(10) NOT NULL DEFAULT 0,
  `FLT_Given` int(10) NOT NULL DEFAULT 0,
  `Invites_Given` int(10) NOT NULL DEFAULT 0,
  `2FA_Key` varchar(16) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Recovery` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `FirstTorrent` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`) USING BTREE,
  UNIQUE INDEX `Username`(`Username`) USING BTREE,
  INDEX `Email`(`Email`) USING BTREE,
  INDEX `PassHash`(`PassHash`) USING BTREE,
  INDEX `LastAccess`(`LastAccess`) USING BTREE,
  INDEX `IP`(`IP`) USING BTREE,
  INDEX `Class`(`Class`) USING BTREE,
  INDEX `Uploaded`(`Uploaded`) USING BTREE,
  INDEX `Downloaded`(`Downloaded`) USING BTREE,
  INDEX `Enabled`(`Enabled`) USING BTREE,
  INDEX `Invites`(`Invites`) USING BTREE,
  INDEX `torrent_pass`(`torrent_pass`) USING BTREE,
  INDEX `RequiredRatio`(`RequiredRatio`) USING BTREE,
  INDEX `cc_index`(`ipcc`) USING BTREE,
  INDEX `PermissionID`(`PermissionID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 46 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_notifications_settings
-- ----------------------------
DROP TABLE IF EXISTS `users_notifications_settings`;
CREATE TABLE `users_notifications_settings`  (
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Inbox` tinyint(1) DEFAULT 1,
  `StaffPM` tinyint(1) DEFAULT 1,
  `News` tinyint(1) DEFAULT 1,
  `Blog` tinyint(1) DEFAULT 1,
  `Torrents` tinyint(1) DEFAULT 1,
  `Collages` tinyint(1) DEFAULT 1,
  `Quotes` tinyint(1) DEFAULT 1,
  `Subscriptions` tinyint(1) DEFAULT 1,
  `SiteAlerts` tinyint(1) DEFAULT 1,
  `RequestAlerts` tinyint(1) DEFAULT 1,
  `CollageAlerts` tinyint(1) DEFAULT 1,
  `TorrentAlerts` tinyint(1) DEFAULT 1,
  `ForumAlerts` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_notify_filters
-- ----------------------------
DROP TABLE IF EXISTS `users_notify_filters`;
CREATE TABLE `users_notify_filters`  (
  `ID` int(12) NOT NULL AUTO_INCREMENT,
  `UserID` int(10) NOT NULL,
  `Label` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Artists` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `RecordLabels` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Users` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `Tags` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `NotTags` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Categories` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Formats` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Encodings` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `Media` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `FromYear` int(4) NOT NULL DEFAULT 0,
  `ToYear` int(4) NOT NULL DEFAULT 0,
  `ExcludeVA` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `NewGroupsOnly` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `ReleaseTypes` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `FromLogScore` int(6) NOT NULL DEFAULT 0,
  `ToLogScore` int(6) NOT NULL DEFAULT 0,
  `FromSize` bigint(12) NOT NULL DEFAULT 0,
  `ToSize` bigint(12) NOT NULL DEFAULT 0,
  `NotUsers` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`ID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `FromYear`(`FromYear`) USING BTREE,
  INDEX `ToYear`(`ToYear`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_notify_quoted
-- ----------------------------
DROP TABLE IF EXISTS `users_notify_quoted`;
CREATE TABLE `users_notify_quoted`  (
  `UserID` int(10) NOT NULL,
  `QuoterID` int(10) NOT NULL,
  `Page` enum('forums','artist','collages','requests','torrents') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `PageID` int(10) NOT NULL,
  `PostID` int(10) NOT NULL,
  `UnRead` tinyint(1) NOT NULL DEFAULT 1,
  `Date` datetime(0) NOT NULL,
  PRIMARY KEY (`UserID`, `Page`, `PostID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_notify_torrents
-- ----------------------------
DROP TABLE IF EXISTS `users_notify_torrents`;
CREATE TABLE `users_notify_torrents`  (
  `UserID` int(10) NOT NULL,
  `FilterID` int(10) NOT NULL,
  `GroupID` int(10) NOT NULL,
  `TorrentID` int(10) NOT NULL,
  `UnRead` tinyint(4) NOT NULL DEFAULT 1,
  PRIMARY KEY (`UserID`, `TorrentID`) USING BTREE,
  INDEX `TorrentID`(`TorrentID`) USING BTREE,
  INDEX `UserID_Unread`(`UserID`, `UnRead`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_points
-- ----------------------------
DROP TABLE IF EXISTS `users_points`;
CREATE TABLE `users_points`  (
  `UserID` int(10) NOT NULL,
  `GroupID` int(10) NOT NULL,
  `Points` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`UserID`, `GroupID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_points_requests
-- ----------------------------
DROP TABLE IF EXISTS `users_points_requests`;
CREATE TABLE `users_points_requests`  (
  `UserID` int(10) NOT NULL,
  `RequestID` int(10) NOT NULL,
  `Points` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`RequestID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `RequestID`(`RequestID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_push_notifications
-- ----------------------------
DROP TABLE IF EXISTS `users_push_notifications`;
CREATE TABLE `users_push_notifications`  (
  `UserID` int(10) NOT NULL,
  `PushService` tinyint(1) NOT NULL DEFAULT 0,
  `PushOptions` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_sessions
-- ----------------------------
DROP TABLE IF EXISTS `users_sessions`;
CREATE TABLE `users_sessions`  (
  `UserID` int(10) NOT NULL,
  `SessionID` char(32) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `KeepLogged` enum('0','1') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `Browser` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `OperatingSystem` varchar(13) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `IP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `LastUpdate` datetime(0) NOT NULL,
  `Active` tinyint(4) NOT NULL DEFAULT 1,
  `FullUA` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `BrowserVersion` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `OperatingSystemVersion` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`UserID`, `SessionID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `LastUpdate`(`LastUpdate`) USING BTREE,
  INDEX `Active`(`Active`) USING BTREE,
  INDEX `ActiveAgeKeep`(`Active`, `LastUpdate`, `KeepLogged`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_subscriptions
-- ----------------------------
DROP TABLE IF EXISTS `users_subscriptions`;
CREATE TABLE `users_subscriptions`  (
  `UserID` int(10) NOT NULL,
  `TopicID` int(10) NOT NULL,
  PRIMARY KEY (`UserID`, `TopicID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_subscriptions_comments
-- ----------------------------
DROP TABLE IF EXISTS `users_subscriptions_comments`;
CREATE TABLE `users_subscriptions_comments`  (
  `UserID` int(10) NOT NULL,
  `Page` enum('artist','collages','requests','torrents') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `PageID` int(10) NOT NULL,
  PRIMARY KEY (`UserID`, `Page`, `PageID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_summary
-- ----------------------------
DROP TABLE IF EXISTS `users_summary`;
CREATE TABLE `users_summary`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `Groups` int(10) NOT NULL DEFAULT 0,
  `PerfectFlacs` int(10) NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`) USING BTREE,
  CONSTRAINT `users_summary_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for users_torrent_history
-- ----------------------------
DROP TABLE IF EXISTS `users_torrent_history`;
CREATE TABLE `users_torrent_history`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `NumTorrents` int(6) UNSIGNED NOT NULL,
  `Date` int(8) UNSIGNED NOT NULL,
  `Time` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `LastTime` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `Finished` enum('1','0') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '1',
  `Weight` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`, `NumTorrents`, `Date`) USING BTREE,
  INDEX `Finished`(`Finished`) USING BTREE,
  INDEX `Date`(`Date`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_torrent_history_snatch
-- ----------------------------
DROP TABLE IF EXISTS `users_torrent_history_snatch`;
CREATE TABLE `users_torrent_history_snatch`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `NumSnatches` int(10) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`) USING BTREE,
  INDEX `NumSnatches`(`NumSnatches`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_torrent_history_temp
-- ----------------------------
DROP TABLE IF EXISTS `users_torrent_history_temp`;
CREATE TABLE `users_torrent_history_temp`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `NumTorrents` int(6) UNSIGNED NOT NULL DEFAULT 0,
  `SumTime` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `SeedingAvg` int(6) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_votes
-- ----------------------------
DROP TABLE IF EXISTS `users_votes`;
CREATE TABLE `users_votes`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `GroupID` int(10) NOT NULL,
  `Type` enum('Up','Down') CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Time` timestamp(0) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`UserID`, `GroupID`) USING BTREE,
  INDEX `GroupID`(`GroupID`) USING BTREE,
  INDEX `Type`(`Type`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE,
  INDEX `Vote`(`Type`, `GroupID`, `UserID`) USING BTREE,
  CONSTRAINT `users_votes_ibfk_1` FOREIGN KEY (`GroupID`) REFERENCES `torrents_group` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `users_votes_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users_main` (`ID`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for users_warnings_forums
-- ----------------------------
DROP TABLE IF EXISTS `users_warnings_forums`;
CREATE TABLE `users_warnings_forums`  (
  `UserID` int(10) UNSIGNED NOT NULL,
  `Comment` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`UserID`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wiki_aliases
-- ----------------------------
DROP TABLE IF EXISTS `wiki_aliases`;
CREATE TABLE `wiki_aliases`  (
  `Alias` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `UserID` int(10) NOT NULL,
  `ArticleID` int(10) DEFAULT NULL,
  PRIMARY KEY (`Alias`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wiki_articles
-- ----------------------------
DROP TABLE IF EXISTS `wiki_articles`;
CREATE TABLE `wiki_articles`  (
  `ID` int(10) NOT NULL AUTO_INCREMENT,
  `Lan_id` int(11) NOT NULL,
  `Revision` int(10) NOT NULL DEFAULT 1,
  `Title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `MinClassRead` int(4) DEFAULT NULL,
  `MinClassEdit` int(4) DEFAULT NULL,
  `Date` datetime(0) DEFAULT NULL,
  `Author` int(10) DEFAULT NULL,
  `Father` int(11) NOT NULL,
  `Lang` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'chs',
  PRIMARY KEY (`ID`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wiki_artists
-- ----------------------------
DROP TABLE IF EXISTS `wiki_artists`;
CREATE TABLE `wiki_artists`  (
  `RevisionID` int(12) NOT NULL AUTO_INCREMENT,
  `PageID` int(10) NOT NULL DEFAULT 0,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Summary` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Time` datetime(0) NOT NULL,
  `Image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`RevisionID`) USING BTREE,
  INDEX `PageID`(`PageID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wiki_revisions
-- ----------------------------
DROP TABLE IF EXISTS `wiki_revisions`;
CREATE TABLE `wiki_revisions`  (
  `ID` int(10) NOT NULL,
  `Revision` int(10) NOT NULL,
  `Title` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Body` mediumtext CHARACTER SET utf8 COLLATE utf8_general_ci,
  `Date` datetime(0) DEFAULT NULL,
  `Author` int(10) DEFAULT NULL,
  INDEX `ID_Revision`(`ID`, `Revision`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wiki_torrents
-- ----------------------------
DROP TABLE IF EXISTS `wiki_torrents`;
CREATE TABLE `wiki_torrents`  (
  `RevisionID` int(12) NOT NULL AUTO_INCREMENT,
  `PageID` int(10) NOT NULL DEFAULT 0,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Summary` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Time` datetime(0) NOT NULL,
  `Image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`RevisionID`) USING BTREE,
  INDEX `PageID`(`PageID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 21090 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for wiki_torrents_0331
-- ----------------------------
DROP TABLE IF EXISTS `wiki_torrents_0331`;
CREATE TABLE `wiki_torrents_0331`  (
  `RevisionID` int(12) NOT NULL AUTO_INCREMENT,
  `PageID` int(10) NOT NULL DEFAULT 0,
  `Body` text CHARACTER SET utf8 COLLATE utf8_general_ci,
  `UserID` int(10) NOT NULL DEFAULT 0,
  `Summary` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `Time` datetime(0) NOT NULL,
  `Image` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  PRIMARY KEY (`RevisionID`) USING BTREE,
  INDEX `PageID`(`PageID`) USING BTREE,
  INDEX `UserID`(`UserID`) USING BTREE,
  INDEX `Time`(`Time`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xbt_client_whitelist
-- ----------------------------
DROP TABLE IF EXISTS `xbt_client_whitelist`;
CREATE TABLE `xbt_client_whitelist`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `peer_id` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `vstring` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `peer_id`(`peer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xbt_files_history
-- ----------------------------
DROP TABLE IF EXISTS `xbt_files_history`;
CREATE TABLE `xbt_files_history`  (
  `uid` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `seedtime` int(11) NOT NULL DEFAULT 0,
  `downloaded` bigint(20) NOT NULL DEFAULT 0,
  `uploaded` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`fid`, `uid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for xbt_files_users
-- ----------------------------
DROP TABLE IF EXISTS `xbt_files_users`;
CREATE TABLE `xbt_files_users`  (
  `uid` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `announced` int(11) NOT NULL DEFAULT 0,
  `completed` tinyint(1) NOT NULL DEFAULT 0,
  `downloaded` bigint(20) NOT NULL DEFAULT 0,
  `remaining` bigint(20) NOT NULL DEFAULT 0,
  `uploaded` bigint(20) NOT NULL DEFAULT 0,
  `upspeed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `downspeed` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `corrupt` bigint(20) NOT NULL DEFAULT 0,
  `timespent` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `useragent` varchar(51) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `connectable` tinyint(4) NOT NULL DEFAULT 1,
  `peer_id` binary(20) NOT NULL DEFAULT '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0',
  `fid` int(11) NOT NULL,
  `mtime` int(11) NOT NULL DEFAULT 0,
  `ip` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '',
  `ipv6` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`peer_id`, `fid`, `uid`) USING BTREE,
  INDEX `remaining_idx`(`remaining`) USING BTREE,
  INDEX `fid_idx`(`fid`) USING BTREE,
  INDEX `mtime_idx`(`mtime`) USING BTREE,
  INDEX `uid_active`(`uid`, `active`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for xbt_snatched
-- ----------------------------
DROP TABLE IF EXISTS `xbt_snatched`;
CREATE TABLE `xbt_snatched`  (
  `uid` int(11) NOT NULL DEFAULT 0,
  `tstamp` int(11) NOT NULL,
  `fid` int(11) NOT NULL,
  `IP` varchar(15) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `seedtime` int(11) NOT NULL DEFAULT 0,
  `ipv6` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  INDEX `fid`(`fid`) USING BTREE,
  INDEX `tstamp`(`tstamp`) USING BTREE,
  INDEX `uid_tstamp`(`uid`, `tstamp`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Compact;
-- ----------------------------
-- Function structure for binomial_ci
-- ----------------------------
DROP FUNCTION IF EXISTS `binomial_ci`;
CREATE DEFINER=`root`@`%` FUNCTION `binomial_ci`(p int, n int) RETURNS float
 DETERMINISTIC
 SQL SECURITY INVOKER
RETURN IF(n = 0,0.0,((p + 1.35336) / n - 1.6452 * SQRT((p * (n-p)) / n + 0.67668) / n) / (1 + 2.7067 / n));
-- ----------------------------
-- Function structure for size_correct
-- ----------------------------
DROP FUNCTION IF EXISTS `size_correct`;
DELIMITER //
CREATE DEFINER=`root`@`%` FUNCTION `size_correct`(`size` DOUBLE) RETURNS double
 NO SQL
BEGIN
declare res double default 0;
if size <= 1.6 then 
    set res = size;
else
    set res = 2.232158 - 3.947062 * pow (0.379185, size) + 0.127678 * size;
end if;
RETURN res;
END
//
DELIMITER ;

SET FOREIGN_KEY_CHECKS = 1;

alter table torrents add column dead_time datetime(0) not null;
ALTER TABLE `users_freetorrents` CHANGE `FreeTorrent` `FreeTorrent` ENUM('0','1','2','11','12','13') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0'; 
ALTER TABLE `torrents` CHANGE `FreeTorrent` `FreeTorrent` ENUM('0','1','2','11','12','13') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0'; 

ALTER TABLE wiki_torrents ADD COLUMN IMDBRating float DEFAULT NULL;
ALTER TABLE wiki_torrents ADD COLUMN DoubanRating float DEFAULT NULL;
ALTER TABLE wiki_torrents ADD COLUMN Duration smallint(2) DEFAULT NULL;
ALTER TABLE wiki_torrents ADD COLUMN ReleaseDate varchar(15) DEFAULT NULL;
ALTER TABLE wiki_torrents ADD COLUMN Region varchar(100) DEFAULT NULL;
ALTER TABLE wiki_torrents ADD COLUMN Language varchar(100) DEFAULT NULL;

ALTER TABLE torrents_group ADD COLUMN IMDBRating float DEFAULT NULL;
ALTER TABLE torrents_group ADD COLUMN DoubanRating float DEFAULT NULL;
ALTER TABLE torrents_group ADD COLUMN Duration smallint(2) DEFAULT NULL;
ALTER TABLE torrents_group ADD COLUMN ReleaseDate varchar(15) DEFAULT NULL;
ALTER TABLE torrents_group ADD COLUMN Region varchar(100) DEFAULT NULL;
ALTER TABLE torrents_group ADD COLUMN Language varchar(100) DEFAULT NULL;

ALTER TABLE wiki_artists ADD COLUMN IMDBID varchar(15) DEFAULT NULL;


INSERT INTO `thread_type` (`Name`) VALUES ('staff-role');
