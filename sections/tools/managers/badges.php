<?
if (isset($_POST['do'])) {
    switch ($_POST['do']) {
        case 'addItem':
            Badges::addItem($_POST['label'], $_POST['bigimage'], $_POST['smallimage'], $_POST['level'], $_POST['count']);
            break;
        case 'editItem':
            Badges::editItem($_POST['id'], $_POST['label'], $_POST['bigimage'], $_POST['smallimage'], $_POST['level'], $_POST['count']);
            break;
        case 'deleteItem':
            if (!Badges::deleteItem($_POST['id'])) {
                // error("删除失败");
                error(Lang::get('tools', 'delete_failed'));
            }
            break;
        case 'addLabel':
            Badges::addLabel($_POST['label'], $_POST['disimage'], $_POST['type'], $_POST['auto'], $_POST['father'], $_POST['progress'], $_POST['rank'], $_POST['remark']);
            break;
        case 'editLabel':
            Badges::editLabel($_POST['label'], $_POST['disimage'], $_POST['type'], $_POST['auto'], $_POST['father'], $_POST['progress'], $_POST['rank'], $_POST['remark']);
            break;
        case 'deleteLabel':
            if (!Badges::deleteLabel($_POST['label'])) {
                // error("删除失败");
                error(Lang::get('tools', 'delete_failed'));
            }
            break;
    }
    header("Location: /tools.php?action=badges&label=" . $_POST['label']);
}
View::show_header(Lang::get('tools', 'badge_management')); ?>
<h2><?= Lang::get('tools', 'badge_management') ?></h2>
<?
$BadgeLabels = Badges::get_badge_labels();
?>
<table id="badge_management_table">
    <tr>
        <th></th>
        <th><?= Lang::get('tools', 'badge_tag') ?></th>
        <th><?= Lang::get('tools', 'badge_image') ?></th>
        <th><?= Lang::get('tools', 'badge_type') ?></th>
        <th><?= Lang::get('tools', 'badge_auto') ?></th>
        <th><?= Lang::get('tools', 'badge_class') ?></th>
        <th><?= Lang::get('tools', 'badge_progress') ?></th>
        <th><?= Lang::get('tools', 'badge_sort') ?></th>
        <th><?= Lang::get('tools', 'badge_note') ?></th>
        <th><?= Lang::get('tools', 'badge_operations') ?></th>
    </tr>
    <tr>
        <form action="tools.php?action=badges" method="POST">
            <td></td>
            <td><input name="label" type="text"></td>
            <td><input name="disimage" type="text"></td>
            <td><input name="type" type="text"></td>
            <td><input name="auto" type="number" min="0" max="1"></td>
            <td><input name="father" type="number" min="0" max="1"></td>
            <td><input name="progress" type="number" min="0" max="1"></td>
            <td><input name="rank" type="number" min="1" max="9999"></td>
            <td><input name="remark" type="text"></td>
            <td><input name="do" type="submit" value="addLabel"></td>
        </form>
    </tr>
    <tr>
        <th></th>
        <th><?= Lang::get('tools', 'badge_tag') ?></th>
        <th><?= Lang::get('tools', 'badge_image') ?></th>
        <th><?= Lang::get('tools', 'badge_icon') ?></th>
        <th><?= Lang::get('tools', 'badge_level') ?></th>
        <th><?= Lang::get('tools', 'badge_level_number') ?></th>
    </tr>
    <tr>
        <form action="tools.php?action=badges" method="POST">
            <td></td>
            <td><input name="label" type="text"></td>
            <td><input name="bigimage" type="text"></td>
            <td><input name="smallimage" type="text"></td>
            <td><input name="level" type="number" min="-1" max="7"></td>
            <td><input name="count" type="number" min="-1" max="9999"></td>
            <td><input name="do" type="submit" value="addItem"></td>
        </form>
    </tr>
    <?
    foreach ($BadgeLabels as $BadgeLabel) {
    ?>
        <tr>
            <th></th>
            <th><?= Lang::get('tools', 'badge_tag') ?></th>
            <th><?= Lang::get('tools', 'badge_image') ?></th>
            <th><?= Lang::get('tools', 'badge_type') ?></th>
            <th><?= Lang::get('tools', 'badge_auto') ?></th>
            <th><?= Lang::get('tools', 'badge_class') ?></th>
            <th><?= Lang::get('tools', 'badge_progress') ?></th>
            <th><?= Lang::get('tools', 'badge_sort') ?></th>
            <th><?= Lang::get('tools', 'badge_note') ?></th>
            <th><?= Lang::get('tools', 'badge_operations') ?></th>
        </tr>
        <tr>
            <form action="tools.php?action=badges" method="POST">
                <td><a href="javascript:toggle('<?= $BadgeLabel['Label'] ?>')">+</a></td>
                <td><input name="label" value="<?= $BadgeLabel['Label'] ?>" type="text"></td>
                <td><input name="disimage" value="<?= $BadgeLabel['DisImage'] ?>" type="text"></td>
                <td><input name="type" value="<?= $BadgeLabel['Type'] ?>" type="text"></td>
                <td><input name="auto" value="<?= $BadgeLabel['Auto'] ?>" type="number" min="0" max="1"></td>
                <td><input name="father" value="<?= $BadgeLabel['Father'] ?>" type="number" min="0" max="1"></td>
                <td><input name="progress" value="<?= $BadgeLabel['Progress'] ?>" type="number" min="0" max="1"></td>
                <td><input name="rank" value="<?= $BadgeLabel['Rank'] ?>" type="number" min="1" max="9999"></td>
                <td><input name="remark" value="<?= $BadgeLabel['Remark'] ?>" type="text"></td>
                <td><input name="do" type="submit" value="editLabel"></td>
                <td><input name="do" type="submit" value="deleteLabel"></td>
            </form>
        </tr>
        <tr class="badge_<?= $BadgeLabel['Label'] ?>" <?= $_GET['label'] == $BadgeLabel['Label'] ? "" : "style=\"display: none;\"" ?>>
            <th></th>
            <th><?= Lang::get('tools', 'badge_tag') ?></th>
            <th><?= Lang::get('tools', 'badge_image') ?></th>
            <th><?= Lang::get('tools', 'badge_icon') ?></th>
            <th><?= Lang::get('tools', 'badge_level') ?></th>
            <th><?= Lang::get('tools', 'badge_level_number') ?></th>
        </tr>
        <?
        $Badges = Badges::get_badges_by_label($BadgeLabel['Label']);

        foreach ($Badges as $Badge) {
        ?>
            <tr class="badge_<?= $BadgeLabel['Label'] ?>" <?= $_GET['label'] == $BadgeLabel['Label'] ? "" : "style=\"display: none;\"" ?>>
                <form action="tools.php?action=badges" method="POST">
                    <input type="hidden" name="id" value="<?= $Badge['ID'] ?>">
                    <td></td>
                    <td><input name="label" value="<?= $Badge['Label'] ?>" type="text"></td>
                    <td><input name="bigimage" value="<?= $Badge['BigImage'] ?>" type="text"></td>
                    <td><input name="smallimage" value="<?= $Badge['SmallImage'] ?>" type="text"></td>
                    <td><input name="level" value="<?= $Badge['Level'] ?>" type="number" min="-1" max="7"></td>
                    <td><input name="count" value="<?= $Badge['Count'] ?>" type="number" min="-1" max="9999"></td>
                    <td><input name="do" type="submit" value="editItem"></td>
                    <td><input name="do" type="submit" value="deleteItem"></td>
                </form>
            </tr>
    <?
        }
    }
    ?>
</table>
<script>
    function toggle(label) {
        $(".badge_" + label).toggle()
    }
</script>
<? View::show_footer(); ?>