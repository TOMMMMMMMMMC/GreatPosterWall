<?
if (!check_perms('admin_donor_log')) {
    error(403);
}
$Title = Lang::get('tools', 'bitcoin_donation_balance');
View::show_header($Title);

$Balance = DonationsBitcoin::get_balance() . ' BTC';
?>
<div class="header">
    <h2><?= $Title ?></h2>
</div>
<div class="thin">
    <div class="header">
        <h3><?= $Balance ?></h3>
    </div>
    <?
    if (empty($_GET['list'])) {
    ?>
        <a href="?action=<?= $_REQUEST['action'] ?>&amp;list=1" class="brackets"><?= Lang::get('tools', 'show_donor_list') ?></a>
    <?
    } else {
        $BitcoinAddresses = DonationsBitcoin::get_received();
        $DB->query("
		SELECT i.UserID, i.BitcoinAddress
		FROM users_info AS i
			JOIN users_main AS m ON m.ID = i.UserID
		WHERE BitcoinAddress != ''
		ORDER BY m.Username ASC");
    ?>
        <table>
            <tr class="colhead">
                <th><?= Lang::get('tools', 'username') ?></th>
                <th><?= Lang::get('tools', 'receiving_bitcoin_address') ?></th>
                <th><?= Lang::get('tools', 'amount') ?></th>
            </tr>
            <?
            while (list($UserID, $BitcoinAddress) = $DB->next_record(MYSQLI_NUM, false)) {
                if (!isset($BitcoinAddresses[$BitcoinAddress])) {
                    continue;
                }
            ?>
                <tr>
                    <td><?= Users::format_username($UserID, true, false, false, false) ?></td>
                    <td><tt><?= $BitcoinAddress ?></tt></td>
                    <td><?= $BitcoinAddresses[$BitcoinAddress] ?> BTC</td>
                </tr>
            <?
            }
            ?>
        </table>
    <?
    }
    ?>
</div>
<? View::show_footer(); ?>