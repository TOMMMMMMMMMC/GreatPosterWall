<?
if (!check_perms("users_mod")) {
    error(403);
}

$Classes = Users::get_classes()[0];
// If your user base is large, sending a PM to the lower classes will take a long time
// add the class ID into this array to skip it when presenting the list of classes


View::show_header(Lang::get('tools', 'compose_mass_pm'), 'inbox,bbcode,jquery.validate,form_validate');
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('tools', 'send_a_mass_pm') ?></h2>
    </div>
    <form class="send_form" name="message" action="tools.php" method="post" id="messageform">
        <div class="box pad">
            <input type="hidden" name="action" value="take_mass_pm" />
            <input type="hidden" name="auth" value="<?= G::$LoggedUser['AuthKey'] ?>" />
            <div id="quickpost">
                <h3><?= Lang::get('tools', 'mass_pm_class') ?></h3>
                <select id="class_id" name="class_id">
                    <option>---</option>
                    <option value="99"><?= Lang::get('tools', 'all_users') ?></option>
                    <option value="100"><?= Lang::get('tools', 'staff') ?></option>
                    <? foreach ($Classes as $Class) {
                        if (!in_array($Class['ID'])) { ?>
                            <option value="<?= $Class['ID'] ?>"><?= $Class['Name'] ?></option>
                    <?                      }
                    } ?>

                </select>
                <h3><?= Lang::get('tools', 'subject') ?></h3>
                <input type="text" class="required" name="subject" size="95" /><br />
                <h3><?= Lang::get('tools', 'body') ?></h3>
                <textarea id="body" class="required" name="body" cols="95" rows="10" onkeyup="resize('body')"></textarea>
            </div>
            <input type="checkbox" name="from_system" id="from_system" checked="checked" /><label for="from_system"><?= Lang::get('tools', 'send_as_system') ?></label>
            <div id="preview" class="hidden"></div>
            <div id="buttons" class="center">
                <input type="button" value="Preview" onclick="Quick_Preview();" />
                <input type="submit" value="Send message" />
            </div>
        </div>
    </form>
</div>
<?
View::show_footer();
?>