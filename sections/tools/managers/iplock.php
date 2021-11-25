<?
if (isset($_POST['do'])) {
    switch ($_POST['do']) {
        case 'add':
            $r = IPLock::add($_POST['userid'], $_POST['ips']);
            if (!$r) {
                error(Lang::get('tools', 'should_edit'));
            }
            break;
        case 'edit':
            IPLock::edit($_POST['userid'], $_POST['ips']);
            break;
        case 'delete':
            IPLock::delete($_POST['userid']);
            break;
    }
    header("Location: /tools.php?action=iplock");
    exit();
}
View::show_header(Lang::get('tools', 'ip_lock_management')); ?>
<h2><?= Lang::get('tools', 'ip_lock_management') ?></h2>
<?
$IPLockList = IPLock::getIPLock();
?>
<table id="ip_lock_management_table">
    <tr>
        <th>UserID</th>
        <th>IPs</th>
    </tr>
    <tr>
        <form action="tools.php?action=iplock" method="POST">
            <td><input name="userid" type="number"></td>
            <td><input name="ips" type="text"></td>
            <td><input name="do" type="submit" value="add"></td>
        </form>
    </tr>
    <?
    foreach ($IPLockList as $UserID => $IPs) {
    ?>
        <tr>
            <form action="tools.php?action=iplock" method="POST">
                <td><input name="userid" value="<?= $UserID ?>" type="number" readonly></td>
                <td><input name="ips" value="<?= $IPs ?>" type="text"></td>
                <td><input name="do" type="submit" value="edit"></td>
                <td><input name="do" type="submit" value="delete"></td>
            </form>
        </tr>
    <?
    }
    ?>
</table>
<? View::show_footer(); ?>