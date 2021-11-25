<?php

use Phinx\Migration\AbstractMigration;

class Search extends AbstractMigration {
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change() {
        $this->execute("
        DROP TABLE IF EXISTS `sphinx_delta`;
        CREATE TABLE `sphinx_delta` (
            `ID` int(10) NOT NULL,
            `GroupID` int(11) NOT NULL DEFAULT 0,
            `GroupName` varchar(255) DEFAULT NULL,
            `ArtistName` varchar(2048) DEFAULT NULL,
            `TagList` varchar(728) DEFAULT NULL,
            `Year` int(4) DEFAULT NULL,
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
            `FreeTorrent` tinyint(1) DEFAULT NULL,
            `FileList` mediumtext DEFAULT NULL,
            `Description` text DEFAULT NULL,
            `VoteScore` float NOT NULL DEFAULT 0,
            `LastChanged` timestamp NOT NULL DEFAULT current_timestamp(),
            `IMDBRating` float DEFAULT NULL,
            `DoubanRating` float DEFAULT NULL,
            `Region` varchar(100) DEFAULT NULL,
            `Language` varchar(100) DEFAULT NULL,
            `IMDBID` varchar(15) DEFAULT NULL,
            `Resolution` varchar(15) DEFAULT NULL,
            `Container` varchar(15) DEFAULT NULL,
            `Source` varchar(15) DEFAULT NULL,
            `codec` varchar(15) NOT NULL,
            `Subtitles` set('chinese_simplified','chinese_traditional','english','japanese','korean','no_subtitles','arabic','brazilian_port','bulgarian','croatian','czech','danish','dutch','estonian','finnish','french','german','greek','hebrew','hindi','hungarian','icelandic','indonesian','italian','latvian','lithuanian','norwegian','persian','polish','portuguese','romanian','russian','serbian','slovak','slovenian','spanish','swedish','thai','turkish','ukrainian','vietnamese') DEFAULT NULL,
            PRIMARY KEY (`ID`) USING BTREE,
            KEY `GroupID` (`GroupID`) USING BTREE,
            KEY `Size` (`Size`) USING BTREE
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
           DROP TABLE IF EXISTS `sphinx_tg`;
           CREATE TABLE `sphinx_tg` (
            `id` int(11) NOT NULL,
            `name` varchar(300) DEFAULT NULL,
            `tags` varchar(500) DEFAULT NULL,
            `year` smallint(6) DEFAULT NULL,
            `catid` smallint(6) DEFAULT NULL,
            `reltype` smallint(6) DEFAULT NULL,
            `subname` varchar(300) DEFAULT NULL,
            `imdbid` varchar(15) DEFAULT NULL,
            `imdbrating` float DEFAULT NULL,
            `doubanrating` float DEFAULT NULL,
            `region` varchar(100) DEFAULT NULL,
            `language` varchar(100) DEFAULT NULL,
            PRIMARY KEY (`id`) USING BTREE
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
           DROP TABLE IF EXISTS `sphinx_t`;
           CREATE TABLE `sphinx_t` (
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
            `media` varchar(15) NOT NULL,
            `resolution` varchar(15) DEFAULT NULL,
            `maker` varchar(15) DEFAULT NULL,
            `container` varchar(15) DEFAULT NULL,
            `subtitle` varchar(80) NOT NULL DEFAULT '',
            `codec` varchar(15) NOT NULL,
            `format` varchar(15) NOT NULL,
            `source` varchar(15) DEFAULT NULL,
            `encoding` varchar(30) NOT NULL,
            `remyear` smallint(6) NOT NULL,
            `remtitle` varchar(80) NOT NULL,
            `remrlabel` varchar(80) NOT NULL,
            `remcnumber` varchar(80) NOT NULL,
            `filelist` mediumtext DEFAULT NULL,
            `remident` int(10) unsigned NOT NULL,
            `description` text DEFAULT NULL,
            `subtitles` set('chinese_simplified','chinese_traditional','english','japanese','korean','no_subtitles','arabic','brazilian_port','bulgarian','croatian','czech','danish','dutch','estonian','finnish','french','german','greek','hebrew','hindi','hungarian','icelandic','indonesian','italian','latvian','lithuanian','norwegian','persian','polish','portuguese','romanian','russian','serbian','slovak','slovenian','spanish','swedish','thai','turkish','ukrainian','vietnamese') DEFAULT NULL,
            PRIMARY KEY (`id`) USING BTREE,
            KEY `gid_remident` (`gid`,`remident`) USING BTREE,
            KEY `format` (`format`) USING BTREE
           ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT
        ");
    }
}
