<?php

if (empty($_GET['groupid']) || !is_numeric($_GET['groupid'])) {
    error(404);
}
$GroupID = intval($_GET['groupid']);

include(SERVER_ROOT . '/sections/torrents/functions.php');
$TorrentCache = get_group_info($GroupID, true);

$TorrentDetails = $TorrentCache[0];
$TorrentList = $TorrentCache[1];

// Group details
list(
    $WikiBody, $WikiImage, $IMDBID, $IMDBRating, $Duration, $ReleaseDate, $Region, $Language, $RTRating, $DoubanRating, $IMDBVote, $DoubanVote, $DoubanID, $RTTitle, $GroupID, $GroupName, $GroupYear,
    $GroupRecordLabel, $GroupCatalogueNumber, $ReleaseType, $GroupCategoryID,
    $GroupTime, $GroupVanityHouse, $TorrentTags, $TorrentTagIDs, $TorrentTagUserIDs,
    $TagPositiveVotes, $TagNegativeVotes, $SubName
) = array_values($TorrentDetails);

$Title = Torrents::torrent_group_name($TorrentDetails, true);

$Title = Lang::get('torrents', 'request_an_edit') . ": " . $Title;

View::show_header($Title);

?>
<div class="thin">
    <div class="header">
        <h2><?= $Title ?></h2>
    </div>
    <div class="box pad">
        <div style="margin-bottom: 10px">
            <p><strong class="important_text"><?= Lang::get('torrents', 'you_are_req') ?></strong></p>
            <p class="center"><?= $ArtistName ?><a href="torrents.php?id=<?= $GroupID ?>"><?= $GroupName ?><?= $Extra ?></a></p>
        </div>
        <div style="margin-bottom: 10px">
            <?= Lang::get('torrents', 'you_are_req_note') ?>
        </div>
        <div>
            <p><strong class="important_text"><?= Lang::get('torrents', 'edit_details') ?></strong></p>

            <div class="center">
                <form action="torrents.php" method="POST">
                    <input type="hidden" name="action" value="takeeditrequest" />
                    <input type="hidden" name="groupid" value="<?= $GroupID ?>" />
                    <input type="hidden" name="auth" value="<?= G::$LoggedUser['AuthKey'] ?>" />
                    <textarea name="edit_details" style="width: 95%" required="required"></textarea><br /><br />
                    <input type="submit" value="Submit Edit Request" />
                </form>
            </div>
        </div>
    </div>
</div>

<?php
View::show_footer();
