<?
if (!check_perms('users_mod')) {
    error(403);
}
$Title = Lang::get('tools', 'unprocessed_bitcoin_donations');
View::show_header($Title);

// Find all donors
$AllDonations = DonationsBitcoin::get_received();

$DB->query("
	SELECT BitcoinAddress, SUM(Amount)
	FROM donations_bitcoin
	GROUP BY BitcoinAddress");
$OldDonations = G::$DB->to_pair(0, 1, false);
?>
<div class="thin">
    <div class="header">
        <h2><?= $Title ?></h2>
    </div>
    <div class="box2">
        <div class="pad"><?= Lang::get('tools', 'do_not_process_these_donations_manually') ?></div>
    </div>
    <?
    $NewDonations = array();
    $TotalUnproc = 0;
    foreach ($AllDonations as $Address => $Amount) {
        if (isset($OldDonations[$Address])) {
            if ($Amount == $OldDonations[$Address]) { // Direct comparison should be fine as everything comes from bitcoind
                continue;
            }
            $Debug->log_var(array('old' => $OldDonations[$Address], 'new' => $Amount), Lang::get('tools', 'new_donations_from_before') . "$Address" . Lang::get('tools', 'new_donations_from_after'));
            // PHP doesn't do fixed-point math, and json_decode has already botched the precision
            // so let's just round this off to satoshis and pray that we're on a 64 bit system
            $Amount = round($Amount - $OldDonations[$Address], 8);
        }
        $TotalUnproc += $Amount;
        $NewDonations[$Address] = $Amount;
    }
    ?>
    <table class="border" width="100%">
        <tr class="colhead">
            <td><?= Lang::get('tools', 'bitcoin_address') ?></td>
            <td><?= Lang::get('tools', 'user') ?></td>
            <td><?= Lang::get('tools', 'unprocessed_amount_total') ?>: <?= $TotalUnproc ?: '0' ?>)</td>
            <td><?= Lang::get('tools', 'total_amount') ?></td>
            <td><?= Lang::get('tools', 'donor_rank') ?></td>
            <td><?= Lang::get('tools', 'special_rank') ?></td>
        </tr>
        <?
        if (!empty($NewDonations)) {
            foreach (DonationsBitcoin::get_userids(array_keys($NewDonations)) as $Address => $UserID) {
                $DonationEUR = Donations::currency_exchange($NewDonations[$Address], 'BTC');
        ?>
                <tr>
                    <td><?= $Address ?></td>
                    <td><?= Users::format_username($UserID, true, false, false) ?></td>
                    <td><?= $NewDonations[$Address] ?> (<?= "$DonationEUR EUR" ?>)</td>
                    <td><?= $AllDonations[$Address] ?></td>
                    <td><?= (int)Donations::get_rank($UserID) ?></td>
                    <td><?= (int)Donations::get_special_rank($UserID) ?></td>
                </tr>
            <?  }
        } else { ?>
            <tr>
                <td colspan="7"><?= Lang::get('tools', 'no_unprocessed_bitcoin_donations') ?></td>
            </tr>
        <? } ?>
    </table>
</div>
<?
View::show_footer();
