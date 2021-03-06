<?
if (!check_perms('users_mod')) {
    error(403);
}

define('USERS_PER_PAGE', 50);
list($Page, $Limit) = Format::page_limit(USERS_PER_PAGE);

$SearchQuery = '';
if ($_GET['username']) {
    $SearchString = db_string($_GET['username']);
    $SearchQuery = " WHERE Username LIKE '%$SearchString%' ";
}

$Title = Lang::get('tools', 'donor_rewards');

$DB->query("
	SELECT
		SQL_CALC_FOUND_ROWS
		u.Username,
		d.UserID,
		d.Rank,
		d.Hidden,
		d.DonationTime,
		r.IconMouseOverText,
		r.AvatarMouseOverText,
		r.CustomIcon,
		r.SecondAvatar,
		r.CustomIconLink
	FROM users_donor_ranks AS d
		LEFT JOIN users_main AS u ON u.ID = d.UserID
		LEFT JOIN donor_rewards AS r ON r.UserID = d.UserID
	$SearchQuery
	ORDER BY d.Rank DESC
	LIMIT $Limit");

$Users = $DB->to_array();
$DB->query('SELECT FOUND_ROWS()');
list($Results) = $DB->next_record();
$Pages = Format::get_pages($Page, $Results, USERS_PER_PAGE, 9);

View::show_header($Title);
?>
<div class="header">
    <h2><?= $Title ?></h2>
    <div class="linkbox">
        <?= $Pages ?>
    </div>
</div>

<form action="" method="get">
    <input type="hidden" name="action" value="donor_rewards" />
    <strong><?= Lang::get('tools', 'username_search') ?>: </strong>
    <input type="search" name="username" />
</form>
<table style="table-layout: fixed; width: 100%;">
    <tr class="colhead">
        <td><?= Lang::get('tools', 'username') ?></td>
        <td><?= Lang::get('tools', 'rank') ?></td>
        <td><?= Lang::get('tools', 'hidden') ?></td>
        <td><?= Lang::get('tools', 'last_donated') ?></td>
        <td><?= Lang::get('tools', 'icon_text') ?></td>
        <td><?= Lang::get('tools', 'icon') ?></td>
        <td><?= Lang::get('tools', 'icon_link') ?></td>
        <td><?= Lang::get('tools', 'avatar_text') ?></td>
        <td><?= Lang::get('tools', 'second_avatar') ?></td>
    </tr>
    <?
    $Row = 'b';
    foreach ($Users as $User) {
        $UserInfo = Users::user_info($User['UserID']);
        $Username = $UserInfo['Username'];
    ?>
        <tr class="row<?= $Row ?>">
            <td><?= Users::format_username($User['UserID'], false, true, true, false, false, true) ?></td>
            <td><?= $User['Rank'] ?></td>
            <td><?= $User['Hidden'] ? Lang::get('tools', 'yes') : Lang::get('tools', 'no') ?></td>
            <td><?= time_diff($User['DonationTime']) ?></td>
            <td style="word-wrap: break-word;">
                <?= $User['IconMouseOverText'] ?>
            </td>
            <td style="word-wrap: break-word;">
                <? if (!empty($User['CustomIcon'])) { ?>
                    <img src="<?= ImageTools::process($User['CustomIcon'], false, 'donoricon', $User['UserID']) ?>" width="15" height="13" alt="" />
                <?      } ?>
            </td>
            <td style="word-wrap: break-word;">
                <?= $User['CustomIconLink'] ?>
            </td>
            <td style="word-wrap: break-word;">
                <?= $User['AvatarMouseOverText'] ?>
            </td>
            <td style="word-wrap: break-word;">
                <?= $User['SecondAvatar'] ?>
            </td>
        </tr>
    <?
        $Row = $Row === 'b' ? 'a' : 'b';
    } // foreach
    ?>
</table>
<div class="linkbox"><?= $Pages ?></div>
<?
View::show_footer();
