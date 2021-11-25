<?
require(SERVER_ROOT . '/sections/torrents/functions.php');

$GroupAllowed = array('WikiBody', 'WikiImage', 'ID', 'Name', 'Year', 'RecordLabel', 'CatalogueNumber', 'ReleaseType', 'CategoryID', 'Time', 'VanityHouse');
$TorrentAllowed = array('ID', 'Media', 'Format', 'Encoding', 'Remastered', 'RemasterYear', 'RemasterTitle', 'RemasterRecordLabel', 'RemasterCatalogueNumber', 'Scene', 'HasLog', 'HasCue', 'LogScore', 'FileCount', 'Size', 'Seeders', 'Leechers', 'Snatched', 'FreeTorrent', 'Time', 'Description', 'FileList', 'FilePath', 'UserID', 'Username');

$TorrentID = (int)$_GET['id'];
$TorrentHash = (string)$_GET['hash'];

if ($TorrentID && $TorrentHash) {
    ajax_json_error("bad parameters");
}

if ($TorrentHash) {
    if (!is_valid_torrenthash($TorrentHash)) {
        ajax_json_error("bad hash parameter");
    } else {
        $TorrentID = (int)torrenthash_to_torrentid($TorrentHash);
        if (!$TorrentID) {
            ajax_json_error("bad hash parameter");
        }
    }
}

if ($TorrentID <= 0) {
    ajax_json_error("bad id parameter");
}

$TorrentCache = get_torrent_info($TorrentID, true, 0, true, true);

if (!$TorrentCache) {
    ajax_json_error("bad id parameter");
}

list($TorrentDetails, $TorrentList) = $TorrentCache;

if (!isset($TorrentList[$TorrentID])) {
    ajax_json_error("bad id parameter");
}

$GroupID = $TorrentDetails['ID'];

$ArtistForm = Artists::get_artist($GroupID);
if ($TorrentDetails['CategoryID'] == 0) {
    $CategoryName = "Unknown";
} else {
    $CategoryName = $Categories[$TorrentDetails['CategoryID'] - 1];
}
$JsonMusicInfo = array();
if ($CategoryName == "Music") {
    $JsonMusicInfo = array(
        'composers' => $ArtistForm[4] == null ? array() : pullmediainfo($ArtistForm[4]),
        'dj' => $ArtistForm[6] == null ? array() : pullmediainfo($ArtistForm[6]),
        'artists' => $ArtistForm[1] == null ? array() : pullmediainfo($ArtistForm[1]),
        'with' => $ArtistForm[2] == null ? array() : pullmediainfo($ArtistForm[2]),
        'conductor' => $ArtistForm[5] == null ? array() : pullmediainfo($ArtistForm[5]),
        'remixedBy' => $ArtistForm[3] == null ? array() : pullmediainfo($ArtistForm[3]),
        'producer' => $ArtistForm[7] == null ? array() : pullmediainfo($ArtistForm[7])
    );
} else {
    $JsonMusicInfo = null;
}

$TagList = explode('|', $TorrentDetails['GROUP_CONCAT(DISTINCT tags.Name ORDER BY `TagID` SEPARATOR \'|\')']);

$JsonTorrentDetails = array(
    'description' => html_entity_decode(Text::full_format($TorrentDetails['WikiBody'])),
    'conver' => $TorrentDetails['WikiImage'],
    'id' => (int)$TorrentDetails['ID'],
    'name' => $TorrentDetails['Name'],
    'subName' => html_entity_decode($TorrentDetails['SubName']),
    'year' => (int)$TorrentDetails['Year'],
    'releaseType' => (int)$TorrentDetails['ReleaseType'],
    'categoryId' => (int)$TorrentDetails['CategoryID'],
    'categoryName' => $CategoryName,
    'time' => $TorrentDetails['Time'],
    'isBookmarked' => Bookmarks::has_bookmarked('torrent', $GroupID),
    'tags' => $TagList,
    'imdbId' => $TorrentDetails['IMDBID'],
    'imdbRating' => $TorrentDetails['IMDBRating'],
    'imdbVote' => $TorrentDetails['IMDBVote'],
    'doubanId' => $TorrentDetails['DoubanID'],
    'doubanRating' => $TorrentDetails['DoubanRating'],
    'doubanVote' => $TorrentDetails['DoubanVote'],
    'rtRating' => $TorrentDetails['RTRating'],
    'duration' => $TorrentDetails['Duration'],
    'releaseDate' => $TorrentDetails['ReleaseDate'],
    'region' => $TorrentDetails['Region'],
    'language' => $TorrentDetails['Language'],
);

$Torrent = $TorrentList[$TorrentID];

$Reports = Torrents::get_reports($TorrentID);
if (count($Reports) > 0) {
    $Torrent['Reported'] = true;
} else {
    $Torrent['Reported'] = false;
}
// Convert file list back to the old format
$FileList = explode("\n", $Torrent['FileList']);
foreach ($FileList as &$File) {
    $File = Torrents::filelist_old_format($File);
}
unset($File);
$FileList = implode('|||', $FileList);
$Userinfo = Users::user_info($Torrent['UserID']);
$JsonTorrentList[] = array(
    'id' => (int)$Torrent['ID'],
    'infoHash' => $Torrent['InfoHash'],
    'media' => $Torrent['Media'],
    'format' => $Torrent['Format'],
    'encoding' => $Torrent['Encoding'],
    'remastered' => $Torrent['Remastered'] == 1,
    'remasterYear' => (int)$Torrent['RemasterYear'],
    'remasterTitle' => $Torrent['RemasterTitle'],
    'remasterCustomTitle' => $Torrent['RemasterCustomTitle'],
    'scene' => $Torrent['Scene'] == 1,
    'hasLog' => $Torrent['HasLog'] == 1,
    'hasCue' => $Torrent['HasCue'] == 1,
    'logScore' => (int)$Torrent['LogScore'],
    'logChecksum' => (int)$Torrent['LogChecksum'],
    'logCount' => (int)$Torrent['LogCount'],
    'fileCount' => (int)$Torrent['FileCount'],
    'size' => (int)$Torrent['Size'],
    'seeders' => (int)$Torrent['Seeders'],
    'leechers' => (int)$Torrent['Leechers'],
    'snatched' => (int)$Torrent['Snatched'],
    'freeTorrent' => $Torrent['FreeTorrent'] == 1,
    'reported' => $Torrent['Reported'],
    'time' => $Torrent['Time'],
    'description' => $Torrent['Description'],
    'mediainfos' => isset($Torrent['MediaInfo']) ? json_decode($Torrent['MediaInfo']) : [],
    'fileList' => $FileList,
    'filePath' => $Torrent['FilePath'],
    'userId' => (int)$Torrent['UserID'],
    'username' => $Userinfo['Username'],
    'resolution' => $Torrent['Resolution'],
    'source' => $Torrent['Source'],
    'codec' => $Torrent['Codec'],
    'container' => $Torrent['Container'],
    'processing' => $Torrent['Processing'],
    'chineseDubbed' => $Torrent['ChineseDubbed'],
    'specialSub' => $Torrent['SpecialSub'],
    'subtitleType' => $Torrent['SubtitleType'],
    'subtitles' => $Torrent['Subtitles'],
);

ajax_json_success(array('group' => $JsonTorrentDetails, 'torrent' => array_pop($JsonTorrentList)));
