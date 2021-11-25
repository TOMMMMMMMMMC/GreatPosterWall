<?

use Gazelle\Manager\Donation;

class DonationsView {
    public static function render_mod_donations($Rank, $TotalRank) {
?>
        <table class="layout" id="donation_box">
            <tr class="colhead">
                <td colspan="2"><?= Lang::get('user', 'donor_system_add_points') ?></td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('user', 'value') ?>:</td>
                <td>
                    <input type="text" name="donation_value" onkeypress="return isNumberKey(event);" />
                    <select name="donation_currency">
                        <option value="CNY"><?= Lang::get('user', 'cny') ?></option>
                        <option value="BTC"><?= Lang::get('user', 'btc') ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('user', 'reason') ?>:</td>
                <td><input type="text" class="wide_input_text" name="donation_reason" /></td>
            </tr>
            <tr>
                <td align="right" colspan="2">
                    <input type="submit" name="donor_points_submit" value="Add donor points" />
                </td>
            </tr>
        </table>

        <table class="layout" id="donor_points_box">
            <tr class="colhead">
                <td colspan="3" class="tooltip" title='<?= Lang::get('user', 'donor_system_modify_values_title') ?>'><?= Lang::get('user', 'donor_system_modify_values') ?></td>
            </tr>
            <tr>
                <td class="label tooltip" title="<?= Lang::get('user', 'active_points_title') ?>"><?= Lang::get('user', 'active_points') ?>:</td>
                <td><input type="text" name="donor_rank" onkeypress="return isNumberKey(event);" value="<?= $Rank ?>" /></td>
            </tr>
            <tr>
                <td class="label tooltip" title="<?= Lang::get('user', 'total_points_title') ?>"><?= Lang::get('user', 'total_points') ?>:</td>
                <td><input type="text" name="total_donor_rank" onkeypress="return isNumberKey(event);" value="<?= $TotalRank ?>" /></td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('user', 'reason') ?>:</td>
                <td><input type="text" class="wide_input_text" name="reason" /></td>
            </tr>
            <tr>
                <td align="right" colspan="2">
                    <input type="submit" name="donor_values_submit" value="Change point values" />
                </td>
            </tr>
        </table>
        <?
    }

    public static function render_donor_stats($OwnProfile, $DonationInfo, $leadboardRank, $Visible, $IsDonor) {
        if (check_perms("users_mod") || $OwnProfile || $Visible) {
        ?>
            <div class="box box_info box_userinfo_donor_stats">
                <div class="head colhead_dark"><?= Lang::get('user', 'donor_statistics') ?></div>
                <ul class="stats nobullet">
                    <?
                    if ($IsDonor) {
                        if (check_perms('users_mod') || $OwnProfile) {
                    ?>
                            <li>
                                <?= Lang::get('user', 'total_donor_points') ?>: <?= $DonationInfo['TotRank'] ?>
                            </li>
                        <?              } ?>
                        <li>
                            <?= Lang::get('user', 'current_donor_rank') ?>: <?= self::render_rank($DonationInfo['Rank'], $DonationInfo['SRank']) ?>
                        </li>
                        <li>
                            <?= Lang::get('user', 'current_special_donor_rank') ?>: <?= $DonationInfo['SRank'] ?>
                        </li>

                        <li>
                            <?= Lang::get('user', 'leaderboard_position') ?>: <?= $leadboardRank ?>
                        </li>
                        <li>
                            <?= Lang::get('user', 'last_donated') ?>: <?= time_diff($DonationInfo['Time']) ?>
                        </li>
                        <li>
                            <?= Lang::get('user', 'rank_expires') ?>: <?= ($DonationInfo['ExpireTime']) ?>
                        </li>
                    <?          } else { ?>
                        <li>
                            <?= Lang::get('user', 'rank_expires') ?>
                        </li>
                    <?          } ?>
                </ul>
            </div>
            <?
        }
    }

    public static function render_profile_rewards($EnabledRewards, $ProfileRewards) {
        for ($i = 1; $i <= 4; $i++) {
            if ($EnabledRewards['HasProfileInfo' . $i] && $ProfileRewards['ProfileInfo' . $i]) {
            ?>
                <div class="box">
                    <div class="head">
                        <span><?= !empty($ProfileRewards['ProfileInfoTitle' . $i]) ? display_str($ProfileRewards['ProfileInfoTitle' . $i]) : "Extra Profile " . ($i + 1) ?></span>
                        <span style="float: right;"><a href="#" onclick="$('#profilediv_<?= $i ?>').gtoggle(); this.innerHTML = (this.innerHTML == '<?= Lang::get('global', 'hide') ?>' ? '<?= Lang::get('global', 'show') ?>' : '<?= Lang::get('global', 'hide') ?>'); return false;" class="brackets"><?= Lang::get('global', 'hide') ?></a></span>
                    </div>
                    <div class="pad profileinfo" id="profilediv_<?= $i ?>">
                        <? echo Text::full_format($ProfileRewards['ProfileInfo' . $i]); ?>
                    </div>
                </div>
        <?
            }
        }
    }

    public static function render_donation_history($DonationHistory) {
        if (empty($DonationHistory)) {
            return;
        }
        ?>
        <div class="box box2" id="donation_history_box">
            <div class="head">
                <?= Lang::get('user', 'donation_history') ?> <a href="#" onclick="$('#donation_history').gtoggle(); return false;" class="brackets"><?= Lang::get('user', 'view') ?></a>
            </div>
            <? $Row = 'b'; ?>
            <div class="hidden" id="donation_history">
                <table cellpadding="6" cellspacing="1" border="0" class="border" width="100%">
                    <tbody>
                        <tr class="colhead_dark">
                            <td>
                                <strong><?= Lang::get('user', 'source') ?></strong>
                            </td>
                            <td>
                                <strong><?= Lang::get('user', 'date') ?></strong>
                            </td>
                            <td>
                                <strong><?= Lang::get('user', 'amount_cny') ?></strong>
                            </td>
                            <td>
                                <strong><?= Lang::get('user', 'added_points') ?></strong>
                            </td>
                            <td>
                                <strong><?= Lang::get('user', 'total_points') ?></strong>
                            </td>
                            <td>
                                <strong><?= Lang::get('user', 'email') ?></strong>
                            </td>
                            <td style="width: 30%;">
                                <strong><?= Lang::get('user', 'reason') ?></strong>
                            </td>
                        </tr>
                        <? foreach ($DonationHistory as $Donation) { ?>
                            <tr class="row<?= $Row ?>">
                                <td>
                                    <?= display_str($Donation['Source']) ?> (<?= Users::format_username($Donation['AddedBy']) ?>)
                                </td>
                                <td>
                                    <?= $Donation['Time'] ?>
                                </td>
                                <td>
                                    <?= $Donation['Amount'] ?>
                                </td>
                                <td>
                                    <?= $Donation['Rank'] ?>
                                </td>
                                <td>
                                    <?= $Donation['TotalRank'] ?>
                                </td>
                                <td>
                                    <?= display_str($Donation['Email']) ?>
                                </td>
                                <td>
                                    <?= display_str($Donation['Reason']) ?>
                                </td>
                            </tr>
                        <?
                            $Row = $Row === 'b' ? 'a' : 'b';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
<?
    }

    public static function render_rank($rank, $specialRank, $ShowOverflow = true) {
        echo Donation::rankLabel($rank, $specialRank, $ShowOverflow);
    }
}
