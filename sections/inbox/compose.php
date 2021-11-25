<?

if (empty($Return)) {
    $ToID = $_GET['to'];
    if ($ToID == $LoggedUser['ID']) {
        error(Lang::get('inbox', 'you_cannot_start_a_conversation_with_yourself'));
        header('Location: ' . Inbox::get_inbox_link());
    }
}

if (!$ToID || !is_number($ToID)) {
    error(404);
}

if (!empty($LoggedUser['DisablePM']) && !isset($StaffIDs[$ToID])) {
    error(403);
}

$DB->query("
	SELECT Username
	FROM users_main
	WHERE ID='$ToID'");
list($Username) = $DB->next_record();
if (!$Username) {
    error(404);
}
View::show_header(Lang::get('inbox', 'compose'), 'inbox,bbcode,jquery.validate,form_validate');
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('inbox', 'send_a_message_to_user_before') ?><a href="user.php?id=<?= $ToID ?>"><?= $Username ?></a><?= Lang::get('inbox', 'send_a_message_to_user_after') ?></h2>
    </div>
    <form class="send_form" name="message" action="inbox.php" method="post" id="messageform">
        <div class="box pad">
            <input type="hidden" name="action" value="takecompose" />
            <input type="hidden" name="toid" value="<?= $ToID ?>" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <div id="quickpost">
                <h3><?= Lang::get('inbox', 'subject') ?></h3>
                <input type="text" class="required" name="subject" size="95" value="<?= (!empty($Subject) ? $Subject : '') ?>" /><br />
                <h3><?= Lang::get('inbox', 'body') ?></h3>
                <textarea id="body" class="required" name="body" cols="95" rows="10" onkeyup="resize('body')"><?= (!empty($Body) ? $Body : '') ?></textarea>
            </div>
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