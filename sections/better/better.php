<?
//Include the header
View::show_header(Lang::get('better', 'header_better'));
?>
<div class="thin">
    <h3 id="general"><?= Lang::get('better', 'pursuit_of_perfection') ?></h3>
    <div class="box pad better_intro">
        <p><?= Lang::get('better', 'pursuit_of_perfection_note') ?></p>
    </div>
    <h3 id="lists"><?= Lang::get('better', 'lists') ?></h3>
    <div class="table_container border">
        <table width="100%">
            <tr class="colhead">
                <td style="width: 150px;"><?= Lang::get('better', 'method') ?></td>
                <td style="width: 400px;"><?= Lang::get('better', 'additional_information') ?></td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=transcode&amp;type=0"><?= Lang::get('better', 'transcoding_v0') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'when_a_perfect_available') ?><a href="<?= STATIC_SERVER ?>common/perfect.gif"><?= Lang::get('better', 'no_v0') ?>
                </td>
            </tr>
            <!--
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=transcode&amp;type=1">转码 V2</a>
                </td>
                <td class="nobr">
                完美的无损资源已经存在，但我们还没有 <a href="<?= STATIC_SERVER ?>common/perfect.gif">V2</a> 种子。
                </td>
            </tr>
            -->
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=transcode&amp;type=2"><?= Lang::get('better', 'transcoding_320') ?></a>
                </td>
                <td class="nobr"><?= Lang::get('better', 'when_a_perfect_available') ?><a href="<?= STATIC_SERVER ?>common/perfect.gif"><?= Lang::get('better', 'no_320') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=transcode&amp;type=3"><?= Lang::get('better', 'transcoding_all') ?></a>
                </td>
                <td class="nobr"><?= Lang::get('better', 'when_a_perfect_available_any') ?><a href="<?= STATIC_SERVER ?>common/perfect.gif"><?= Lang::get('better', 'no_v0_320') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=snatch"><?= Lang::get('better', 'snatch') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'snatch_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=upload"><?= Lang::get('better', 'upload') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'upload_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=checksum"><?= Lang::get('better', 'checksum') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'checksum_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=tags&amp;filter=all"><?= Lang::get('better', 'tags') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'tags_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=folders"><?= Lang::get('better', 'folder_names') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'folder_names_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=files"><?= Lang::get('better', 'file_names') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'file_names_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=img"><?= Lang::get('better', 'artwork') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'artwork_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=compress"><?= Lang::get('better', 'compress') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'compress_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=custom"><?= Lang::get('better', 'custom_trump') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'custom_trump_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=lineage"><?= Lang::get('better', 'missing_lineage') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'missing_lineage_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=artwork"><?= Lang::get('better', 'missing_torrent_artwork') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'missing_torrent_artwork_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=artistimage"><?= Lang::get('better', 'missing_artist_image') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'missing_artist_image_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=description"><?= Lang::get('better', 'missing_artist_description') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'missing_artist_description_note') ?>
                </td>
            </tr>
            <tr class="rowb">
                <td class="nobr">
                    <a href="better.php?method=single"><?= Lang::get('better', 'single_seeded_flac_torrents') ?></a>
                </td>
                <td class="nobr">
                    <?= Lang::get('better', 'single_seeded_flac_torrents_note') ?>
                </td>
            </tr>

        </table>
    </div>
</div>
<? View::show_footer(); ?>