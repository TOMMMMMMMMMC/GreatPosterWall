<?

class NotificationsManagerView {
    private static $Settings;

    public static function load_js() {
        $JSIncludes = array(
            'noty/noty.js',
            'noty/layouts/bottomRight.js',
            'noty/themes/default.js',
            'user_notifications.js'
        );
        foreach ($JSIncludes as $JSInclude) {
            $Path = STATIC_SERVER . "functions/$JSInclude";
?>
            <script src="<?= $Path ?>?v=<?= filemtime(SERVER_ROOT . static_prefix() . "/$Path") ?>" type="text/javascript"></script>
        <?
        }
    }

    private static function render_push_settings() {
        $PushService = self::$Settings['PushService'];
        $PushOptions = unserialize(self::$Settings['PushOptions']);
        if (empty($PushOptions['PushDevice'])) {
            $PushOptions['PushDevice'] = '';
        }
        ?>
        <tr>
            <td class="label"><strong><?= Lang::get('user', 'push_notifications') ?></strong></td>
            <td>
                <select name="pushservice" id="pushservice">
                    <option value="0" <? if (empty($PushService)) { ?> selected="selected" <? } ?>><?= Lang::get('user', 'disable_push_notifications') ?></option>
                    <option value="1" <? if ($PushService == 1) { ?> selected="selected" <? } ?>><?= Lang::get('user', 'notify_my_android') ?></option>
                    <option value="2" <? if ($PushService == 2) { ?> selected="selected" <? } ?>><?= Lang::get('user', 'prowl') ?></option>
                    <!--                        No option 3, notifo died. -->
                    <option value="4" <? if ($PushService == 4) { ?> selected="selected" <? } ?>><?= Lang::get('user', 'super_toasty') ?></option>
                    <option value="5" <? if ($PushService == 5) { ?> selected="selected" <? } ?>><?= Lang::get('user', 'pushover') ?></option>
                    <option value="6" <? if ($PushService == 6) { ?> selected="selected" <? } ?>><?= Lang::get('user', 'pushbullet') ?></option>
                </select>
                <div id="pushsettings" style="display: none;">
                    <label id="pushservice_title" for="pushkey"><?= Lang::get('user', 'api_key') ?></label>
                    <input type="text" size="50" name="pushkey" id="pushkey" value="<?= display_str($PushOptions['PushKey']) ?>" />
                    <label class="pushdeviceid" id="pushservice_device" for="pushdevice"><?= Lang::get('user', 'device_id') ?></label>
                    <select class="pushdeviceid" name="pushdevice" id="pushdevice">
                        <option value="<?= display_str($PushOptions['PushDevice']) ?>" selected="selected"><?= display_str($PushOptions['PushDevice']) ?></option>
                    </select>
                    <br />
                    <a href="user.php?action=take_push&amp;push=1&amp;userid=<?= G::$LoggedUser['ID'] ?>&amp;auth=<?= G::$LoggedUser['AuthKey'] ?>" class="brackets"><?= Lang::get('user', 'test_push') ?></a>
                    <a href="wiki.php?action=article&amp;id=246" class="brackets"><?= Lang::get('user', 'view_wiki_guide') ?></a>
                </div>
            </td>
        </tr>
    <?  }

    public static function render_settings($Settings) {
        self::$Settings = $Settings;
        self::render_push_settings();
    ?>
        <tr>
            <td class="label">
                <strong><?= Lang::get('user', 'news_announcements') ?></strong>
            </td>
            <td>
                <? self::render_checkbox(NotificationsManager::NEWS); ?>
            </td>
        </tr>
        <tr>
            <td class="label">
                <strong><?= Lang::get('user', 'blog_announcements') ?></strong>
            </td>
            <td>
                <? self::render_checkbox(NotificationsManager::BLOG); ?>
            </td>
        </tr>
        <tr>
            <td class="label">
                <strong><?= Lang::get('user', 'inbox_messages') ?></strong>
            </td>
            <td>
                <? self::render_checkbox(NotificationsManager::INBOX, true); ?>
            </td>
        </tr>
        <tr>
            <td class="label tooltip" title="<?= Lang::get('user', 'staff_messages_title') ?>">
                <strong><?= Lang::get('user', 'staff_messages') ?></strong>
            </td>
            <td>
                <? self::render_checkbox(NotificationsManager::STAFFPM, false, false); ?>
            </td>
        </tr>
        <tr>
            <td class="label">
                <strong><?= Lang::get('user', 'thread_subscriptions') ?></strong>
            </td>
            <td>
                <? self::render_checkbox(NotificationsManager::SUBSCRIPTIONS, false, false); ?>
            </td>
        </tr>
        <tr>
            <td class="label tooltip" title="<?= Lang::get('user', 'quote_notifications_title') ?>">
                <strong><?= Lang::get('user', 'quote_notifications') ?></strong>
            </td>
            <td>
                <? self::render_checkbox(NotificationsManager::QUOTES); ?>
            </td>
        </tr>
        <? if (check_perms('site_torrents_notify')) { ?>
            <tr>
                <td class="label tooltip" title="<?= Lang::get('user', 'torrent_notifications_title') ?>">
                    <strong><?= Lang::get('user', 'torrent_notifications') ?></strong>
                </td>
                <td>
                    <? self::render_checkbox(NotificationsManager::TORRENTS, true, false); ?>
                </td>
            </tr>
        <?      } ?>

        <tr>
            <td class="label tooltip" title="<?= Lang::get('user', 'collage_subscriptions_title') ?>">
                <strong><?= Lang::get('user', 'collage_subscriptions') ?></strong>
            </td>
            <td>
                <? self::render_checkbox(NotificationsManager::COLLAGES . false, false); ?>
            </td>
        </tr>
    <?  }

    private static function render_checkbox($Name, $Traditional = false, $Push = true) {
        $Checked = self::$Settings[$Name];
        $PopupChecked = $Checked == NotificationsManager::OPT_POPUP || $Checked == NotificationsManager::OPT_POPUP_PUSH || !isset($Checked) ? ' checked="checked"' : '';
        $TraditionalChecked = $Checked == NotificationsManager::OPT_TRADITIONAL || $Checked == NotificationsManager::OPT_TRADITIONAL_PUSH ? ' checked="checked"' : '';
        $PushChecked = $Checked == NotificationsManager::OPT_TRADITIONAL_PUSH || $Checked == NotificationsManager::OPT_POPUP_PUSH || $Checked == NotificationsManager::OPT_PUSH ? ' checked="checked"' : '';

    ?>
        <input type="checkbox" name="notifications_<?= $Name ?>_popup" id="notifications_<?= $Name ?>_popup" <?= $PopupChecked ?> />
        <label for="notifications_<?= $Name ?>_popup"><?= Lang::get('user', 'pop_up') ?></label>
        <? if ($Traditional) { ?>

            <input type="checkbox" name="notifications_<?= $Name ?>_traditional" id="notifications_<?= $Name ?>_traditional" <?= $TraditionalChecked ?> />
            <label for="notifications_<?= $Name ?>_traditional"><?= Lang::get('user', 'traditional') ?></label>
        <?      }
        if ($Push) { ?>
            <input type="checkbox" name="notifications_<?= $Name ?>_push" id="notifications_<?= $Name ?>_push" <?= $PushChecked ?> />
            <label for="notifications_<?= $Name ?>_push"><?= Lang::get('user', 'push') ?></label>
<?      }
    }

    public static function format_traditional($Contents) {
        return "<a href=\"$Contents[url]\">$Contents[message]</a>";
    }
}
