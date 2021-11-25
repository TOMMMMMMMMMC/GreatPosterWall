<?
View::show_header(Lang::get('rules', 'clients_title'));

if (!$WhitelistedClients = $Cache->get_value('whitelisted_clients')) {
    $DB->query('
		SELECT vstring, peer_id
		FROM xbt_client_whitelist
		WHERE vstring NOT LIKE \'//%\'
		ORDER BY vstring ASC');
    $WhitelistedClients = $DB->to_array(false, MYSQLI_NUM, false);
    $Cache->cache_value('whitelisted_clients', $WhitelistedClients, 604800);
}
?>
<div class="thin">
    <? include('jump.php'); ?>
    <div class="header">
        <h2 class="general"><?= Lang::get('rules', 'clients_title') ?></h2>
        <p><?= Lang::get('rules', 'clients_summary') ?></p>
    </div>
    <div class="box pad">
        <table cellpadding="5" cellspacing="1" border="0" class="border" width="100%">
            <tr class="colhead">
                <td><strong><?= Lang::get('rules', 'clients_list') ?></strong></td>
                <td style="width: 75px"><strong>Peer ID</strong></td>
                <!-- td style="width: 400px;"><strong>Additional Notes</strong></td> -->
            </tr>
            <?
            $Row = 'a';
            foreach ($WhitelistedClients as $Client) {
                //list($ClientName, $Notes) = $Client;
                list($ClientName, $PeerID) = $Client;
                $Row = $Row === 'a' ? 'b' : 'a';
            ?>
                <tr class="row<?= $Row ?>">
                    <td><?= $ClientName ?></td>
                    <td>----</td>
                </tr>
            <?  } ?>
        </table>
    </div>
</div>
<? View::show_footer(); ?>