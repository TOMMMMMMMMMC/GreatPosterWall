<?php

$Inserted = false;
if (isset($_REQUEST['update']) && $_REQUEST['update'] === '1') {
    $CH = curl_init();

    curl_setopt($CH, CURLOPT_URL, 'http://www.accuraterip.com/driveoffsets.htm');
    curl_setopt($CH, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($CH, CURLOPT_CONNECTTIMEOUT, 5);
    $Doc = new DOMDocument();
    $Doc->loadHTML(curl_exec($CH), LIBXML_NOWARNING | LIBXML_NOERROR);
    curl_close($CH);

    $Rows = $Doc->getElementsByTagName('table')->item(1)->getElementsByTagName('tr');
    $Offsets = [];
    $Prepared = [];
    for ($I = 1; $I < $Rows->length; $I++) {
        $Row = $Rows->item($I);
        if ($Row->childNodes->length > 4 && $Row->childNodes->item(3)->nodeValue !== '[Purged]') {
            $Offsets[] = trim($Row->childNodes->item(1)->nodeValue, '- ');
            $Offsets[] = trim($Row->childNodes->item(3)->nodeValue);
            $Prepared[] = "(?, ?)";
        }
    }

    G::$DB->prepared_query('TRUNCATE drives');
    G::$DB->prepared_query('INSERT INTO drives (Name, Offset) VALUES ' . implode(', ', $Prepared), ...$Offsets);
    $Inserted = G::$DB->affected_rows();
}

View::show_header(Lang::get('tools', 'update_drive_offsets'));

?>
<div class="header">
    <h2><?= Lang::get('tools', 'drive_offsets') ?></h2>
</div>
<div class="thin">
    <div class="box pad">
        <p><?= Lang::get('tools', 'drive_offsets_notice') ?>
            <?= ($Inserted !== false) ? "<br />{$Inserted} " . Lang::get('tools', 'offsets_inserted') : "" ?>
        </p>
        <p>
            <a href="tools.php?action=update_offsets&update=1"><?= Lang::get('tools', 'update_offsets') ?></a>
        </p>
    </div>
    <div class="table_container border">
        <table width="100%">
            <tr class="colhead">
                <td><?= Lang::get('tools', 'drive') ?></td>
                <td><?= Lang::get('tools', 'offset') ?></td>
            </tr>
            <?php

            G::$DB->prepared_query('SELECT Name, Offset FROM drives ORDER BY DriveID');
            while (list($Name, $Offset) = G::$DB->fetch_record()) {
            ?>
                <tr>
                    <td><?= $Name ?></td>
                    <td><?= $Offset ?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
</div>
<?php
View::show_footer();
