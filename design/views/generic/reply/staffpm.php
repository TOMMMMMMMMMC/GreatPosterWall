<script>
    function addPM() {
        if ($("#select-level").val() == "1000") {
            $("#subject").val("捐助事项咨询")
        }
    }
</script>
<div id="compose" class="<?= ($Hidden ? 'hidden' : '') ?>">
    <form class="send_form" name="staff_message" action="staffpm.php" method="post">
        <input type="hidden" name="action" value="takepost" />
        <h3><label for="subject"><?= Lang::get('staff', 'subject') ?></label></h3>
        <input size="95" type="text" name="subject" id="subject" required />
        <br />

        <h3><label for="message"><?= Lang::get('staff', 'message') ?></label></h3>
        <?
        $TextPrev = new TEXTAREA_PREVIEW('message', 'message', '', 95, 10, true, false, false, array(), true);
        ?>
        <br />

        <strong><?= Lang::get('staff', 'send_to') ?>: </strong>
        <select id="select-level" name="level" onchange="addPM()">
            <? if (!isset(G::$LoggedUser['LockedAccount'])) { ?>
                <option value="0" <?= $_GET['action'] == 'first_line_support' ? 'selected="selected"' : '' ?>><?= Lang::get('staff', 'first_line_support') ?></option>
                <!-- <option value="800">Forum Moderators</option> -->
                <!-- <option value="850">Torrent Moderators</option> -->
            <?              } ?>
            <option value="800" <?= $_GET['action'] == 'staff' ? 'selected="selected"' : '' ?>><?= Lang::get('staff', 'staff') ?></option>
            <option value="1000" <?= $_GET['action'] == 'donate' ? 'selected="selected"' : '' ?>><?= Lang::get('staff', 'donation') ?></option>
        </select>

        <input type="button" value="Preview" class="hidden button_preview_<?= $TextPrev->getID() ?>" />
        <input type="submit" value="Send message" />
        <input type="button" value="Hide" onclick="$('#compose').gtoggle(); return false;" />
    </form>
</div>
<script>
    addPM()
</script>