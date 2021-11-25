<?

use Gazelle\Manager\Donation;
use Gazelle\Manager\PrepaidCardStatus;

if (!check_perms('users_give_donor')) {
    error(403);
}
$CountPerPage = 10;
$donation = new Donation();
list($Page, $Limit) = Format::page_limit($CountPerPage);
list($All, $Result) = $donation->getAllPrepaidCardDonations($Limit);
$PageView = Format::get_pages($Page, $All, $CountPerPage);
$Title = Lang::get('tools', 'prepaid_card_donor');
View::show_header($Title);
?>
<div class="header">
    <h2><?= $Title ?></h2>
</div>
<div class="thin">
    <div class="linkbox">
        <?= $PageView ?>
    </div>
    <table id="donation_manager">
        <tr class="colhead">
            <td>用户</td>
            <td><?= Lang::get('donate', 'added_time') ?></td>
            <td><?= Lang::get('donate', 'card_num') ?></td>
            <td><?= Lang::get('donate', 'card_secret') ?></td>
            <td><?= Lang::get('donate', 'face_value') ?></td>
            <td><?= Lang::get('tools', 'action') ?></td>
        </tr>
        <?
        $Row = 'a';
        foreach ($Result as $Item) {
            list($ID, $UserID, $CreateTime, $CardNum, $CardSecret, $FaceValue, $Status) = $Item;
            $Row = $Row === 'a' ? 'b' : 'a';
        ?>
            <tr class="row<?= $Row ?>">
                <form method="post">
                    <input type="hidden" name="action" value="take_prepaid_card">
                    <input type="hidden" name="id" value="<?= $ID ?>">

                    <td><?= Users::format_username($UserID) ?></td>
                    <td><?= $CreateTime ?></td>
                    <td><?= $CardNum ?></td>
                    <td><?= $CardSecret ?></td>
                    <td><?= $FaceValue ?></td>
                    <td>
                        <?
                        if ($Status == PrepaidCardStatus::Reject) {
                        ?>
                            <span class="important_text"><?= Lang::get('tools', 'rejected') ?></span>
                        <?
                        } else if ($Status == PrepaidCardStatus::Passed) {
                        ?>
                            <span class="important_text_alt"><?= Lang::get('tools', "passed") ?></span>
                        <?
                        } else {
                        ?>
                            <button type="submit" name="result" value="2" onclick="return confirm('<?= Lang::get('tools', 'sure_delete_staff_group_title') ?>')"><?= Lang::get('tools', 'pass') ?></button>
                            <button type="submit" name="result" value="3" onclick="return confirm('<?= Lang::get('tools', 'sure_delete_staff_group_title') ?>')"><?= Lang::get('tools', 'reject') ?></button>
                        <?
                        }
                        ?>
                    </td>
                </form>
            </tr>
        <?  } ?>
        </tr>

    </table>
    <div class="linkbox">
        <?= $PageView ?>
    </div>

</div>
<?
View::show_footer();
