<?

use Gazelle\Manager\Donation;
use Gazelle\Manager\PrepaidCardStatus;

View::show_header(Lang::get('donate', 'donate'));

$SiteName = SITE_NAME;
$UserID = $LoggedUser['ID'];

$donation = new Donation();
$PrepaidCardDonations = $donation->getPrepaidCardDonations($UserID);
?>

<div class="thin" id="donate_information">
    <h2><?= Lang::get('donate', 'donate') ?></h2>
    <h3><?= Lang::get('donate', 'prepaid_card') ?></h3>
    <div class="box pad">
        <form class="send_form pad" name="donate" action="donate.php" method="post">
            <input type="hidden" name="action" value="donate" />
            <div class="field_div">
                <div class="label"><?= Lang::get('donate', 'card_num') ?>:</div>
                <input type="text" name="card_num" size="60" placeholder="<?= Lang::get('donate', 'card_num_length') ?>" />
            </div>
            <div class="field_div">
                <div class="label"><?= Lang::get('donate', 'card_secret') ?>:</div>
                <input type="text" name="card_secret" size="60" maxlength="180" placeholder="<?= Lang::get('donate', 'card_secret_length') ?>" />
            </div>
            <div class="field_div">
                <div class="label"><?= Lang::get('donate', 'face_value') ?>:</div>
                <div>
                    <select name="face_value">
                        <option value="50"><?= '50 ' . Lang::get('donate', 'yuan') ?></option>
                        <option value="100"><?= '100 ' . Lang::get('donate', 'yuan') ?> </option>
                        <option value="300"><?= '300 ' . Lang::get('donate', 'yuan') ?> </option>
                        <option value="500"><?= '500 ' . Lang::get('donate', 'yuan') ?> </option>
                    </select>
                </div>
            </div>
            <div class="field_div">
                <div></div>
                <div class="center">
                    <input type="submit" value="<?= Lang::get('global', 'submit') ?>" />
                </div>
            </div>
        </form>
    </div>
    <?
    if (count($PrepaidCardDonations) > 0) {
    ?>
        <h3><?= Lang::get('donate', 'history') ?>
        </h3>

        <div class="" id="donate_table_container">
            <table class="donate_table m_table " width="100%">
                <tr class="colhead">
                    <td><?= Lang::get('donate', 'added_time') ?></td>
                    <td><?= Lang::get('donate', 'card_num') ?></td>
                    <td><?= Lang::get('donate', 'card_secret') ?></td>
                    <td><?= Lang::get('donate', 'face_value') ?></td>
                    <td><?= Lang::get('donate', 'status') ?></td>
                </tr>
                <?
                $Row = 'a';
                foreach ($PrepaidCardDonations as $Item) {
                    list(,, $CreateTime, $CardNum, $CardSecret, $FaceValue, $Status) = $Item;
                    $Row = $Row === 'a' ? 'b' : 'a';
                ?>
                    <tr class="row<?= $Row ?>">
                        <td><?= $CreateTime ?></td>
                        <td><?= $CardNum ?></td>
                        <td><?= $CardSecret ?></td>
                        <td><?= $FaceValue ?></td>
                        <td>
                            <? if ($Status == PrepaidCardStatus::Pending) {
                                echo Lang::get('donate', 'pending');
                            } else if ($Status == PrepaidCardStatus::Passed) {
                                echo '<span class="important_text_alt">' . Lang::get('donate', 'success') . '</span>';
                            } else if ($Status == PrepaidCardStatus::Reject) {
                                echo '<span class="important_text">' . Lang::get('donate', 'failed') . '</span>';
                            } ?>
                        </td>
                    </tr>
                <?  } ?>
            </table>
        </div>
    <?
    }
    ?>
    <h3><?= Lang::get('donate', 'tutorials') ?>
    </h3>
    <div class="box pad" id="donate_guide"><?= Lang::get('donate', 'donate_guide') ?></div>

</div>

<? View::show_footer();
