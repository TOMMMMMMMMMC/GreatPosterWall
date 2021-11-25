<?php
View::show_header('Blog', 'bbcode');

if (check_perms('admin_manage_blog')) {
    $BlogID = 0;
    $Title = '';
    $Body = '';
    $ThreadID = null;
    if (!empty($_GET['action']) && $_GET['action'] === 'editblog' && !empty($_GET['id'])) {
        $BlogID = intval($_GET['id']);
        $DB->prepared_query("
            SELECT Title, Body, ThreadID
            FROM blog
            WHERE ID = ?", $BlogID);
        list($Title, $Body, $ThreadID) = $DB->fetch_record(0, 1);
        $ThreadID = $ThreadID ?? 0;
    }
?>
    <div class="box thin" id="blog_create_edit_box">
        <!-- <div class="head"> -->
        <h2><?= empty($_GET['action']) ? Lang::get('blog', 'create_a_blog_post') : Lang::get('blog', 'edit_blog_post') ?></h2>
        <!-- </div> -->
        <form class="<?= empty($_GET['action']) ? 'create_form' : 'edit_form' ?>" name="blog_post" action="blog.php" method="post">
            <div class="pad">
                <input type="hidden" name="action" value="<?= empty($_GET['action']) ? 'takenewblog' : 'takeeditblog' ?>" />
                <input type="hidden" name="auth" value="<?= G::$LoggedUser['AuthKey'] ?>" />
                <?php if (!empty($_GET['action']) && $_GET['action'] == 'editblog') { ?>
                    <input type="hidden" name="blogid" value="<?= $BlogID; ?>" />
                <?php } ?>
                <h3><?= Lang::get('blog', 'title') ?></h3>
                <input type="text" name="title" size="95" <?= !empty($Title) ? ' value="' . display_str($Title) . '"' : ''; ?> /><br />
                <h3><?= Lang::get('blog', 'body') ?></h3>
                <textarea name="body" cols="95" rows="15"><?= !empty($Body) ? display_str($Body) : ''; ?></textarea>
                <br />
                <input type="checkbox" value="1" name="important" id="important" checked="checked" /><label for="important"><?= Lang::get('blog', 'important') ?></label><br />
                <h3><?= Lang::get('blog', 'thread_id') ?></h3>
                <input type="text" name="thread" size="8" <?= $ThreadID !== null ? ' value="' . display_str($ThreadID) . '"' : ''; ?> /><?= Lang::get('blog', 'thread_id_note') ?>
                <br /><br />
                <input id="subscribebox" type="checkbox" name="subscribe" <?= !empty($HeavyInfo['AutoSubscribe']) ? ' checked="checked"' : ''; ?> tabindex="2" />
                <label for="subscribebox"><?= Lang::get('global', 'subscribe') ?></label>

                <div class="center">
                    <input type="submit" value="<?= !isset($_GET['action']) ? 'Create blog post' : 'Edit blog post'; ?>" />
                </div>
            </div>
        </form>
    </div>
    <br />
<?php
}

if (!isset($_GET['action']) || $_GET['action'] !== 'editblog') {
?>
    <div class="thin">
        <?php
        if (!$Blog = $Cache->get_value('blog')) {
            $DB->prepared_query("
		SELECT
			b.ID,
			um.Username,
			b.UserID,
			b.Title,
			b.Body,
			b.Time,
			b.ThreadID
		FROM blog AS b
			LEFT JOIN users_main AS um ON b.UserID = um.ID
		ORDER BY Time DESC
		LIMIT 20");
            $Blog = $DB->to_array();
            $Cache->cache_value('blog', $Blog, 1209600);
        }

        if (count($Blog) > 0 && G::$LoggedUser['LastReadBlog'] < $Blog[0][0]) {
            $Cache->begin_transaction('user_info_heavy_' . G::$LoggedUser['ID']);
            $Cache->update_row(false, array('LastReadBlog' => $Blog[0][0]));
            $Cache->commit_transaction(0);
            $DB->prepared_query("
		UPDATE users_info
		SET LastReadBlog = ?
		WHERE UserID = ?", $Blog[0][0], G::$LoggedUser['ID']);
            G::$LoggedUser['LastReadBlog'] = $Blog[0][0];
        }

        foreach ($Blog as $BlogItem) {
            list($BlogID, $Author, $AuthorID, $Title, $Body, $BlogTime, $ThreadID) = $BlogItem;
        ?>
            <div id="blog<?= $BlogID ?>" class="box blog_post">
                <div class="head">
                    <strong><?= $Title ?></strong> - <?= Lang::get('blog', 'posted') ?> <?= time_diff($BlogTime); ?> <?= Lang::get('blog', 'by') ?> <a href="user.php?id=<?= $AuthorID ?>"><?= $Author ?></a>
                    <?php if (check_perms('admin_manage_blog')) { ?>
                        - <a href="blog.php?action=editblog&amp;id=<?= $BlogID ?>" class="brackets"><?= Lang::get('global', 'edit') ?></a>
                        <a href="blog.php?action=deleteblog&amp;id=<?= $BlogID ?>&amp;auth=<?= G::$LoggedUser['AuthKey'] ?>" class="brackets"><?= Lang::get('global', 'delete') ?></a>
                    <?php    } ?>
                </div>
                <div class="pad">
                    <?= Text::full_format($Body) ?>
                    <?php if ($ThreadID) { ?>
                        <br /><br />
                        <em><a href="forums.php?action=viewthread&amp;threadid=<?= $ThreadID ?>"><?= Lang::get('blog', 'discuss_this_post_here') ?></a></em>
                        <?php if (check_perms('admin_manage_blog')) { ?>
                            <a href="blog.php?action=deadthread&amp;id=<?= $BlogID ?>&amp;auth=<?= G::$LoggedUser['AuthKey'] ?>" class="brackets"><?= Lang::get('blog', 'remove_link') ?></a>
                    <?php
                        }
                    }
                    ?>

                </div>
            </div>
            <br />
    <?php
        }
    }
    ?>
    </div>
    <?php
    View::show_footer();
    ?>