<?
$FeaturedAlbum = $Cache->get_value('album_of_the_month');
if ($FeaturedAlbum === false) {
    $DB->query('
		SELECT
			fa.GroupID,
			tg.Name,
			tg.WikiImage,
			fa.ThreadID,
			fa.Title
		FROM featured_albums AS fa
			JOIN torrents_group AS tg ON tg.ID = fa.GroupID
		WHERE Ended = 0 AND type = 0');
    $FeaturedAlbum = $DB->next_record();
    $Cache->cache_value('album_of_the_month', $FeaturedAlbum, 0);
}
if (is_number($FeaturedAlbum['GroupID'])) {
    $Artists = Artists::get_artist($FeaturedAlbum['GroupID']);
?>
    <div class="box box_image">
        <div class="head colhead_dark">
            <strong><?= Lang::get('index', 'album') ?></strong>
            <a href="forums.php?action=viewthread&amp;threadid=<?= $FeaturedAlbum['ThreadID'] ?>">[<?= Lang::get('index', 'discuss') ?>]</a>
        </div>
        <div class="center pad">
            <a href="torrents.php?id=<?= $FeaturedAlbum['GroupID'] ?>" class="tooltip" title="<?= Artists::display_artists($Artists, false, false) ?> - <?= $FeaturedAlbum['Name'] ?>">
                <img src="<?= ImageTools::process($FeaturedAlbum['WikiImage'], true) ?>" alt="<?= Artists::display_artists($Artists, false, false) ?> - <?= $FeaturedAlbum['Name'] ?>" width="100%" />
            </a>
        </div>
    </div>
<?
}
?>