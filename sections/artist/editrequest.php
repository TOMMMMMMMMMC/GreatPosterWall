<?php

if (empty($_GET['artistid']) || !is_numeric($_GET['artistid'])) {
    error(404);
}
$ArtistID = intval($_GET['artistid']);

$DB->prepared_query("SELECT
			Name,
			VanityHouse
		FROM artists_group
		WHERE ArtistID = ?", $ArtistID);

if (!$DB->has_results()) {
    error(404);
}

list($Name, $VanityHouseArtist) = $DB->fetch_record();

if ($VanityHouseArtist) {
    $Name .= ' [Vanity House]';
}

View::show_header(Lang::get('artist', 'request_an_edit') . ": " . $Name);

?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('artist', 'request_an_edit') ?>: <?= display_str($Name) ?></h2>
    </div>
    <div class="box pad">
        <div style="margin-bottom: 10px">
            <p><strong class="important_text"><?= Lang::get('artist', 'you_are_req_for') ?></strong></p>
            <p class="center"><a href="artist.php?id=<?= $ArtistID ?>"><?= display_str($Name) ?></a></p>
        </div>
        <div style="margin-bottom: 10px">
            <?= Lang::get('artist', 'you_are_req_for_note') ?>
        </div>
        <div>
            <p><strong class="important_text"><?= Lang::get('artist', 'edit_details') ?></strong></p>

            <div class="center">
                <form action="artist.php" method="POST">
                    <input type="hidden" name="action" value="takeeditrequest" />
                    <input type="hidden" name="artistid" value="<?= $ArtistID ?>" />
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
