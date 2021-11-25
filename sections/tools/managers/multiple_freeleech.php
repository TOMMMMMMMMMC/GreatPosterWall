<?
if (!check_perms('users_mod')) {
    error(403);
}

View::show_header(Lang::get('tools', 'global_torrents_sales_promotion_management'));

if (isset($_POST['torrents'])) {
    $GroupIDs = array();
    $Elements = explode("\r\n", $_POST['torrents']);
    foreach ($Elements as $Element) {
        // Get all of the torrent IDs
        if (strpos($Element, "torrents.php") !== false) {
            $Data = explode("id=", $Element);
            if (!empty($Data[1])) {
                $GroupIDs[] = (int) $Data[1];
            }
        } else if (strpos($Element, "collages.php") !== false) {
            $Data = explode("id=", $Element);
            if (!empty($Data[1])) {
                $CollageID = (int) $Data[1];
                $DB->query("
                    SELECT GroupID
                    FROM collages_torrents
                    WHERE CollageID = '$CollageID'");
                while (list($GroupID) = $DB->next_record()) {
                    $GroupIDs[] = (int) $GroupID;
                }
            }
        }
    }

    if (sizeof($GroupIDs) == 0) {
        $Err = Lang::get('tools', 'please_enter_properly_formatted_urls');
    } else {
        $FreeLeechType = (int) $_POST['freeleechtype'];
        $FreeLeechReason = (int) $_POST['freeleechreason'];

        if (!in_array($FreeLeechType, array(0, 1, 2)) || !in_array($FreeLeechReason, array(0, 1, 2, 3))) {
            $Err = Lang::get('tools', 'invalid_freeleech_type_or_freeleech_reason');
        } else {
            // Get the torrent IDs
            $DB->query("
                SELECT ID
                FROM torrents
                WHERE GroupID IN (" . implode(', ', $GroupIDs) . ")");
            $TorrentIDs = $DB->collect('ID');

            if (sizeof($TorrentIDs) == 0) {
                $Err = Lang::get('tools', 'invalid_group_ids');
            } else {
                if (isset($_POST['NLOver']) && $FreeLeechType == 1) {
                    // Only use this checkbox if freeleech is selected
                    $Size = (int) $_POST['size'];
                    $Units = db_string($_POST['scale']);

                    if (empty($Size) || !in_array($Units, array('k', 'm', 'g'))) {
                        $Err = Lang::get('tools', 'invalid_size_or_units');
                    } else {
                        $Bytes = Format::get_bytes($Size . $Units);

                        $DB->query("
                            SELECT ID
                            FROM torrents
                            WHERE ID IN (" . implode(', ', $TorrentIDs) . ")
                              AND Size > '$Bytes'");
                        $LargeTorrents = $DB->collect('ID');
                        $TorrentIDs = array_diff($TorrentIDs, $LargeTorrents);
                    }
                }

                if (sizeof($TorrentIDs) > 0) {
                    Torrents::freeleech_torrents($TorrentIDs, $FreeLeechType, $FreeLeechReason);
                }

                if (isset($LargeTorrents) && sizeof($LargeTorrents) > 0) {
                    Torrents::freeleech_torrents($LargeTorrents, 2, $FreeLeechReason);
                }

                $Err = 'Done!';
            }
        }
    }
}
?>
<div class="thin">
    <h2><?= Lang::get('tools', 'h2_global_torrents_sales_promotion_management') ?></h2>
    <div class="pad box" id="torrent_sale_management">
        <table id="torrent_sale_management_table">
            <tr>
                <td class="label"><?= Lang::get('tools', 'sales_promotion_range') ?>:</td>
                <td>
                    <select>
                        <option><?= Lang::get('tools', 'all_torrents_include_new') ?></option>
                        <option><?= Lang::get('tools', 'all_current_torrents') ?></option>
                        <option><?= Lang::get('tools', 'all_new_torrents') ?></option>
                        <!-- <option><?= Lang::get('tools', 'internal_torrents') ?></option> -->
                        <option><?= Lang::get('tools', 'certain_torrent_groups') ?></option>
                        <option><?= Lang::get('tools', 'certain_torrents') ?></option>
                        <!-- <option><?= Lang::get('tools', 'certain_size_torrents') ?></option> -->
                    </select>
                </td>
            </tr>
            <tr class="hidden">
                <td colspan="2">
                    <textarea>
                <!-- 如果选了「指定种子组」，就显示这个模块，要求填写种子组 ID，用英文逗号隔开 -->
            </textarea>
                </td>
            </tr>
            <tr class="hidden">
                <td colspan="2">
                    <textarea>
                <!-- 如果选了「指定种子」，就显示这个模块，要求填写种子 PL ID，用英文逗号隔开 -->
            </textarea>
                </td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('tools', 'specifications') ?>:</td>
                <td>
                    <select>
                        <option><?= Lang::get('global', 'type') ?></option>
                        <option><?= Lang::get('global', 'feature_film') ?></option>
                        <option><?= Lang::get('global', 'short_film') ?></option>
                        <option><?= Lang::get('global', 'miniseries') ?></option>
                        <option><?= Lang::get('global', 'stand_up_comedy') ?></option>
                        <option><?= Lang::get('global', 'live_performance') ?></option>
                        <option><?= Lang::get('global', 'movie_collection') ?></option>
                    </select>
                    <select>
                        <option><?= Lang::get('tools', 'resolution') ?></option>
                        <option><?= Lang::get('tools', 'standard_definition') ?></option>
                        <option>720p</option>
                        <option>1080p</option>
                        <option>2160p</option>
                    </select>
                    <select>
                        <option><?= Lang::get('tools', 'processing') ?></option>
                        <option>Encode</option>
                        <option>Remux</option>
                        <option>DIY</option>
                        <option>Untouched</option>
                    </select>
                    <select>
                        <option><?= Lang::get('tools', 'source') ?></option>
                        <option>DVD</option>
                        <option>WEB</option>
                        <option>Blu-ray</option>
                        <option>HD-DVD</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('tools', 'misc') ?>:</td>
                <td>
                    <select>
                        <option><?= Lang::get('tools', 'original_options') ?></option>
                        <option><?= Lang::get('tools', 'both') ?></option>
                        <option><?= Lang::get('upload', 'self_rip') ?></option>
                        <option><?= Lang::get('upload', 'self_purchase') ?></option>
                    </select>
                    <select>
                        <option><?= Lang::get('tools', 'internal_torrent') ?></option>
                        <option><?= Lang::get('tools', 'yes') ?></option>
                        <option><?= Lang::get('tools', 'no') ?></option>
                    </select>
                    <select>
                        <option><?= Lang::get('tools', 'feature_torrent') ?></option>
                        <option><?= Lang::get('tools', 'both') ?></option>
                        <option><?= Lang::get('tools', 'chinese_dubbed') ?></option>
                        <option><?= Lang::get('tools', 'special_effects_subtitles') ?></option>
                        <option><?= Lang::get('tools', 'no') ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('tools', 'size_limitation') ?>:</td>
                <td>
                    <span><?= Lang::get('tools', 'above') ?></span>
                    <input type="number" style="width: 80px" min="0">
                    <span><?= Lang::get('tools', 'below') ?></span>
                    <input type="number" style="width: 80px" min="0">
                    <span><?= Lang::get('tools', 'equal') ?></span>
                    <input type="number" style="width: 80px" min="0">
                    <span>GB</span>
                    <p><?= Lang::get('tools', 'size_limitation_note') ?></p>
                </td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('tools', 'sales_promotion_plan') ?>:</td>
                <td>
                    <select>
                        <option><?= Lang::get('tools', 'free_leech') ?></option>
                        <option><?= Lang::get('tools', '25_percent_off') ?></option>
                        <option><?= Lang::get('tools', '50_percent_off') ?></option>
                        <option><?= Lang::get('tools', '75_percent_off') ?></option>
                        <option><?= Lang::get('tools', 'neutral_leech') ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?= Lang::get('tools', 'sales_promotion_period') ?>:</td>
                <td>
                    <input type="number" min="1" max="">
                    <span><?= Lang::get('tools', 'hour_s') ?></span>
                    <input type="checkbox" id="permanent">
                    <label for="permanent"><?= Lang::get('tools', 'permanent') ?></label>
                </td>
            </tr>
            <tr>
                <td colspan="2" class="center">
                    <button>
                        <span><?= Lang::get('tools', 'add_rule') ?></span>
                    </button>
                </td>
            </tr>
        </table>
    </div>

    <h3><?= Lang::get('tools', 'current_sales_promotion_rules') ?></h3>
    <div id="current_sale_rules">
        <table id="current_sale_rules_table" class="multiple_freeleech_table">
            <tr class="colhead">
                <td><?= Lang::get('tools', 'sales_promotion_range') ?></td>
                <td><?= Lang::get('tools', 'sales_promotion_plan') ?></td>
                <td><?= Lang::get('tools', 'sales_promotion_period') ?></td>
                <td><?= Lang::get('tools', 'added_on') ?></td>
                <td><?= Lang::get('tools', 'operator') ?></td>
                <td><?= Lang::get('tools', 'operation') ?></td>
            </tr>
            <tr>
                <td><a><?= Lang::get('tools', 'click_to_see') ?></a></td><!-- 这样设计是因为考虑到自定义一批种子或种子组，需要另行显示；如果是全局的批量，就直接显示文字就可以。 -->
                <td>比方说免费</td>
                <td>比方说 3 天</td>
                <td>2021-05-13</td>
                <td>Username</td>
                <td>
                    <a><?= Lang::get('global', 'delete') ?></a><!-- 如果要调整促销规则，删掉旧的新增就可以 -->
                </td>
            </tr>
            <tr>
                <td>所有种子</td>
                <td>比方说免费</td>
                <td>比方说 3 天</td>
                <td>2021-05-14</td>
                <td>Username</td>
                <td>
                    <a><?= Lang::get('global', 'delete') ?></a>
                </td>
            </tr>
        </table>
    </div>

    <h3><?= Lang::get('tools', 'sales_promotion_rules_history') ?></h3>
    <div id="sale_rules_history">
        <table id="sale_rules_history_table" class="multiple_freeleech_table">
            <tr class="colhead">
                <td><?= Lang::get('tools', 'sales_promotion_range') ?></td>
                <td><?= Lang::get('tools', 'sales_promotion_plan') ?></td>
                <td><?= Lang::get('tools', 'sales_promotion_period') ?></td>
                <td><?= Lang::get('tools', 'added_on') ?></td>
                <td><?= Lang::get('tools', 'deleted_on') ?></td>
                <td><?= Lang::get('tools', 'operator') ?></td>
            </tr>
            <tr>
                <td><a><?= Lang::get('tools', 'click_to_see') ?></a></td><!-- 这样设计是因为考虑到自定义一批种子或种子组，需要另行显示；如果是全局的批量，就直接显示文字就可以。 -->
                <td>比方说免费</td>
                <td>比方说 3 天</td>
                <td>2021-05-13</td>
                <td>2021-05-14</td>
                <td>Username</td>
            </tr>
        </table>
    </div>







    <div class="box pad box2">
        <? if (isset($Err)) { ?>
            <strong class="important_text"><?= $Err ?></strong><br />
        <?  } ?>
        <?= Lang::get('tools', 'paste_a_list_of_collage_or_torrent_group_urls') ?>
    </div>
    <div class="box pad">
        <form class="send_form center" action="" method="post">
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <textarea name="torrents" style="width: 95%; height: 200px;"><?= $_POST['torrents'] ?></textarea><br /><br />
            <?= Lang::get('tools', 'mark_torrents_as') ?>:&nbsp;
            <select name="freeleechtype">
                <option value="1" <?= $_POST['freeleechtype'] == '1' ? 'selected' : '' ?>><?= Lang::get('tools', 'fl') ?></option>
                <option value="2" <?= $_POST['freeleechtype'] == '2' ? 'selected' : '' ?>><?= Lang::get('tools', 'nl') ?></option>
                <option value="0" <?= $_POST['freeleechtype'] == '0' ? 'selected' : '' ?>><?= Lang::get('tools', 'normal') ?></option>
            </select>&nbsp;<?= Lang::get('tools', 'for_reason') ?>&nbsp;<select name="freeleechreason">
                <? $FL = array('N/A', 'Staff Pick', 'Perma-FL', 'Vanity House');
                foreach ($FL as $Key => $FLType) { ?>
                    <option value="<?= $Key ?>" <?= $_POST['freeleechreason'] == $Key ? 'selected' : '' ?>><?= $FLType ?></option>
                <?      } ?>
            </select><br /><br />
            <input type="checkbox" name="NLOver" checked />&nbsp;<?= Lang::get('tools', 'nl_torrents_over') ?> <input type="text" name="size" value="<?= isset($_POST['size']) ? $_POST['size'] : '1' ?>" size=1 />
            <select name="scale">
                <option value="k" <?= $_POST['scale'] == 'k' ? 'selected' : '' ?>>KB</option>
                <option value="m" <?= $_POST['scale'] == 'm' ? 'selected' : '' ?>>MB</option>
                <option value="g" <?= !isset($_POST['scale']) || $_POST['scale'] == 'g' ? 'selected' : '' ?>>GB</option>
            </select><?= Lang::get('tools', 'nl_torrents_over_after') ?><br /><br />
            <input type="submit" value="Submit" />
        </form>
    </div>
</div>
<?
View::show_footer();
