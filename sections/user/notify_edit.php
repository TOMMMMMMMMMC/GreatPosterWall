<?
if (!check_perms('site_torrents_notify')) {
    error(403);
}
View::show_header(Lang::get('user', 'manage_notifications'), 'jquery.validate,form_validate');
?>
<div class="thin">
    <div class="header">
        <h2><?= Lang::get('user', 'notify_me_of_all_new_torrents_with') ?></h2>
        <div class="linkbox">
            <a href="torrents.php?action=notify" class="brackets"><?= Lang::get('user', 'view_notifications') ?></a>
        </div>
    </div>
    <script>
        function alwaysOn(event) {
            if (event.data.id == 0) event.data.id = ""
            if ($("#good_" + event.data.id).prop('checked')) {
                $(this).prop('checked', true);
            }
        }

        function alwaysOff(event) {
            if (event.data.id == 0) event.data.id = ""
            if ($("#good_" + event.data.id).prop('checked')) {
                $(this).prop('checked', false);
            }
        }

        function rssGroup(event) {
            if (event.data.id == 0) event.data.id = ""
            if ($("#good_" + event.data.id).prop('checked')) {
                $('#FLAC_' + event.data.id).prop('checked', true);
                $('#WAV_' + event.data.id).prop('checked', true);
                $('#CD_' + event.data.id).prop('checked', true);
                $('#fromlogscore_' + event.data.id).attr('value', 85);
                $('#tologscore_' + event.data.id).attr('value', 100);
                $('#fromsize_' + event.data.id).attr('value', 0);
                $('#tosize_' + event.data.id).attr('value', 640);
                $('#fromsize_' + event.data.id).attr('readonly', true);
                $('#tosize_' + event.data.id).attr('readonly', true);
            } else {
                $('#FLAC_' + event.data.id).prop('checked', false);
                $('#WAV_' + event.data.id).prop('checked', false);
                $('#CD_' + event.data.id).prop('checked', false);
                $('#fromlogscore_' + event.data.id).attr('value', '');
                $('#tologscore_' + event.data.id).attr('value', '');
                $('#fromsize_' + event.data.id).attr('value', '');
                $('#tosize_' + event.data.id).attr('value', '');
                $('#fromsize_' + event.data.id).attr('readonly', false);
                $('#tosize_' + event.data.id).attr('readonly', false);
            }
        }

        function smallerThen(first, second, error) {
            console.log(first, second, error)
            var firstval = $("#" + first).val(),
                secondval = $("#" + second).val()
            if (firstval && secondval) {
                if (parseInt(firstval) > parseInt(secondval)) {

                    $("#" + error).show()

                } else {
                    $("#" + error).hide()
                }
            }
        }
    </script>
    <?
    $DB->query("
	SELECT
		ID,
		Label,
		Artists,
		ExcludeVA,
		NewGroupsOnly,
		Tags,
		NotTags,
		ReleaseTypes,
		Categories,
        Codecs, 
        Sources, 
        Containers, 
        Resolutions,
        Processings,
        FreeTorrents,
		FromYear,
		ToYear,
		FromSize,
		ToSize,
		Users,
		NotUsers,
        FromIMDBRating, 
        Regions, 
        Languages, 
        RemasterTitles
	FROM users_notify_filters
	WHERE UserID=$LoggedUser[ID]");

    $NumFilters = $DB->record_count();

    $Notifications = $DB->to_array();
    $Notifications[] = array(
        'ID' => false,
        'Label' => '',
        'Artists' => '',
        'ExcludeVA' => false,
        'NewGroupsOnly' => true,
        'Tags' => '',
        'NotTags' => '',
        'ReleaseTypes' => '',
        'Categories' => '',
        'Codecs' => '',
        'Sources' => '',
        'Resolutions' => '',
        'Containers' => '',
        'FreeTorrents' => '',
        'Processings' => '',
        'FromYear' => 0,
        'ToYear' => 0,
        'FromSize' => 0,
        'ToSize' => 0,
        'Users' => '',
        'NotUsers' => '',
        'FromIMDBRating' => 0,
        'Regions' => '',
        'Languages' => '',
        'RemasterTitles' => '',
    );

    $i = 0;
    foreach ($Notifications as $N) { // $N stands for Notifications
        $i++;
        $NewFilter = $N['ID'] === false;
        $N['Artists']       = implode(', ', explode('|', substr($N['Artists'], 1, -1)));
        $N['Tags']          = implode(', ', explode('|', substr($N['Tags'], 1, -1)));
        $N['NotTags']       = implode(', ', explode('|', substr($N['NotTags'], 1, -1)));
        $N['Regions']       = implode(', ', explode('|', substr($N['Regions'], 1, -1)));
        $N['Languages']       = implode(', ', explode('|', substr($N['Languages'], 1, -1)));
        $N['RemasterTitles']       = implode(', ', explode('|', substr($N['RemasterTitles'], 1, -1)));
        $N['ReleaseTypes']  = explode('|', substr($N['ReleaseTypes'], 1, -1));
        $N['Categories']    = explode('|', substr($N['Categories'], 1, -1));
        $N['Codecs']       = explode('|', substr($N['Codecs'], 1, -1));
        $N['Sources']     = explode('|', substr($N['Sources'], 1, -1));
        $N['Resolutions']         = explode('|', substr($N['Resolutions'], 1, -1));
        $N['Containers']         = explode('|', substr($N['Containers'], 1, -1));
        $N['Processings']         = explode('|', substr($N['Processings'], 1, -1));
        $N['FreeTorrents']         = explode('|', substr($N['FreeTorrents'], 1, -1));
        $N['Users']         = explode('|', substr($N['Users'], 1, -1));
        $N['NotUsers']      = explode('|', substr($N['NotUsers'], 1, -1));

        $Usernames = '';
        foreach ($N['Users'] as $UserID) {
            $UserInfo = Users::user_info($UserID);
            $Usernames .= $UserInfo['Username'] . ', ';
        }
        $Usernames = rtrim($Usernames, ', ');

        $NotUsernames = '';
        foreach ($N['NotUsers'] as $UserID) {
            $UserInfo = Users::user_info($UserID);
            $NotUsernames .= $UserInfo['Username'] . ', ';
        }
        $NotUsernames = rtrim($NotUsernames, ', ');
        if ($N['FromYear'] + $N['ToYear'] == 0) {
            $N['FromYear'] = '';
            $N['ToYear'] = '';
        }
        if ($N['FromIMDBRating'] == 0) {
            $N['FromIMDBRating'] = '';
        }
        $N['FromSize'] /= 1024 * 1024 * 1024;
        $N['ToSize'] /= 1024 * 1024 * 1024;
        if ($N['FromSize'] + $N['ToSize'] == 0) {
            $N['FromSize'] = '';
            $N['ToSize'] = '';
        }
        if ($N['ToSize'] == 0) {
            $N['ToSize'] = '';
        }
        if ($NewFilter && $NumFilters > 0) {
    ?>
            <br /><br />
            <h3><?= Lang::get('user', 'create_a_new_notification_filter') ?></h3>
        <?  } elseif ($NumFilters > 0) { ?>
            <h3>
                <a href="feeds.php?feed=torrents_notify_<?= $N['ID'] ?>_<?= $LoggedUser['torrent_pass'] ?>&amp;user=<?= $LoggedUser['ID'] ?>&amp;auth=<?= $LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= $LoggedUser['torrent_pass'] ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;name=<?= urlencode($N['Label']) ?>"><img src="<?= STATIC_SERVER ?>/common/symbols/rss.png" alt="RSS feed" /></a>
                <?= display_str($N['Label']) ?>
                <a href="user.php?action=notify_delete&amp;id=<?= $N['ID'] ?>&amp;auth=<?= $LoggedUser['AuthKey'] ?>" onclick="return confirm('<?= Lang::get('user', 'are_you_sure_delete_notification_filter') ?>')" class="brackets"><?= Lang::get('global', 'delete') ?></a>
                <a href="#" onclick="$('#filter_<?= $N['ID'] ?>').gtoggle(); return false;" class="brackets"><?= Lang::get('global', 'show') ?></a>
                <a <a class="brackets" href="feeds.php?feed=torrents_notify_<?= $N['ID'] ?>_<?= $LoggedUser['torrent_pass'] ?>&amp;user=<?= $LoggedUser['ID'] ?>&amp;auth=<?= $LoggedUser['RSS_Auth'] ?>&amp;passkey=<?= $LoggedUser['torrent_pass'] ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;name=<?= urlencode($N['Label']) ?>"><?= Lang::get('user', 'rss_address') ?></a>

            </h3>
        <?  } ?>
        <script>
            $(document).ready(function() {
                $("#good_<?= $N['ID'] ?>").click({
                    id: <?= $N['ID'] ? $N['ID'] : 0 ?>
                }, rssGroup);
                $('#FLAC_<?= $N['ID'] ?>,#WAV_<?= $N['ID'] ?>,#CD_<?= $N['ID'] ?>').click({
                    id: <?= $N['ID'] ? $N['ID'] : 0 ?>
                }, alwaysOn);
                $('.f_<?= $N['ID'] ?>:not(#FLAC_<?= $N['ID'] ?>,#WAV_<?= $N['ID'] ?>),.r_<?= $N['ID'] ?>,.m_<?= $N['ID'] ?>:not(#CD_<?= $N['ID'] ?>)').click({
                    id: <?= $N['ID'] ? $N['ID'] : 0 ?>
                }, alwaysOff);
            })
        </script>
        <form class="<?= ($NewFilter ? 'create_form' : 'edit_form') ?>" id="<?= ($NewFilter ? 'filter_form' : '') ?>" name="notification" action="user.php" method="post">
            <input type="hidden" name="formid" value="<?= $i ?>" />
            <input type="hidden" name="action" value="notify_handle" />
            <input type="hidden" name="auth" value="<?= $LoggedUser['AuthKey'] ?>" />
            <? if (!$NewFilter) { ?>
                <input type="hidden" name="id<?= $i ?>" value="<?= $N['ID'] ?>" />
            <?  } ?>
            <table <?= (!$NewFilter ? 'id="filter_' . $N['ID'] . '" class="layout hidden"' : 'class="layout"') ?>>
                <? if ($NewFilter) { ?>
                    <tr>
                        <td class="label"><strong><?= Lang::get('user', 'notification_filter_name') ?>:</strong></td>
                        <td>
                            <input type="text" class="required" name="label<?= $i ?>" style="width: 100%;" placeholder="<?= Lang::get('user', 'notification_filter_name_note') ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center">
                            <strong><?= Lang::get('user', 'all_fields_below_here_are_optional') ?></strong>
                        </td>
                    </tr>
                <?  } ?>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'one_of_these_artists') ?>:</strong></td>
                    <td>
                        <textarea name="artists<?= $i ?>" style="width: 100%;" rows="5" placeholder="<?= Lang::get('user', 'comma_seperated_artists_list') ?>"><?= display_str($N['Artists']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'one_of_these_users') ?>:</strong></td>
                    <td>
                        <textarea name="users<?= $i ?>" style="width: 100%;" rows="5" placeholder="<?= Lang::get('user', 'comma_seperated_usernames_list') ?>"><?= display_str($Usernames) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'none_of_these_users') ?>:</strong></td>
                    <td>
                        <textarea name="notusers<?= $i ?>" style="width: 100%;" rows="2" placeholder="<?= Lang::get('user', 'comma_seperated_usernames_list') ?>"><?= display_str($NotUsernames) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'at_least_one_of_these_tags') ?>:</strong></td>
                    <td>
                        <textarea name="tags<?= $i ?>" style="width: 100%;" rows="2" placeholder="<?= Lang::get('user', 'comma_seperated_tags_list') ?>"><?= display_str($N['Tags']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'none_of_these_tags') ?>:</strong></td>
                    <td>
                        <textarea name="nottags<?= $i ?>" style="width: 100%;" rows="2" placeholder="<?= Lang::get('user', 'comma_seperated_tags_list') ?>"><?= display_str($N['NotTags']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_countries') ?>:</strong></td>
                    <td>
                        <textarea name="regions<?= $i ?>" style="width: 100%;" rows="2" placeholder="<?= Lang::get('user', 'comma_seperated_countries_list') ?>"><?= display_str($N['Regions']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_languages') ?>:</strong></td>
                    <td>
                        <textarea name="languages<?= $i ?>" style="width: 100%;" rows="2" placeholder="<?= Lang::get('user', 'comma_seperated_languages_list') ?>"><?= display_str($N['Languages']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_editions') ?>:</strong></td>
                    <td>
                        <textarea name="remastertitles<?= $i ?>" style="width: 100%;" rows="2" placeholder="<?= Lang::get('user', 'comma_seperated_editions_list') ?>"><?= display_str($N['RemasterTitles']) ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_categories') ?>:</strong></td>
                    <td>
                        <? foreach ($ReleaseTypes as $key => $ReleaseType) { ?>
                            <input type="checkbox" name="releasetypes<?= $i ?>[]" id="<?= $ReleaseType ?>_<?= $N['ID'] ?>" value="<?= $ReleaseTypes[$key] ?>" <? if (in_array($ReleaseType, $N['ReleaseTypes'])) {
                                                                                                                                                                    echo ' checked="checked"';
                                                                                                                                                                } ?> />
                            <label for="<?= $ReleaseType ?>_<?= $N['ID'] ?>"><?= Lang::get('torrents', 'release_types')[$key] ?></label>
                        <?  } ?>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'between_the_years') ?>:</strong></td>
                    <td>
                        <input min="1888" max="9999" type="number" name="fromyear<?= $i ?>" value="<?= $N['FromYear'] ?>" id="fromyear_<?= $N['ID'] ?>" placeholder="<?= Lang::get('user', 'min_1888') ?>" onchange='smallerThen("fromyear_<?= $N['ID'] ?>", "toyear_<?= $N['ID'] ?>", "yearerror_<?= $N['ID'] ?>")' />
                        -
                        <input min="0" max="9999" type="number" name="toyear<?= $i ?>" value="<?= $N['ToYear'] ?>" id="toyear_<?= $N['ID'] ?>" onchange='smallerThen("fromyear_<?= $N['ID'] ?>", "toyear_<?= $N['ID'] ?>", "yearerror_<?= $N['ID'] ?>")' />
                        <?= Lang::get('user', 'year') ?>
                        <span>&ensp;<?= Lang::get('user', 'leaving_blank_means_you_allow_all_years') ?></span>
                        <label id="yearerror_<?= $N['ID'] ?>" class="error" style="display: none;"><?= Lang::get('user', 'please_enter_correct_numbers') ?></label>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'minimum_imdb_rating') ?>:</strong></td>
                    <td>
                        <input min="0" max="10" type="number" name="fromimdbrating<?= $i ?>" value="<?= $N['FromIMDBRating'] ?>" id="fromimdbrating_<?= $N['ID'] ?>" onchange='smallerThen("fromimdbrating_<?= $N['ID'] ?>", "toimdbrating_<?= $N['ID'] ?>", "imdbratingerror_<?= $N['ID'] ?>")' />
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_sources') ?>:</strong></td>
                    <td>
                        <? foreach ($Sources as $Source) { ?>
                            <input class="m_<?= $N['ID'] ?>" type="checkbox" name="sources<?= $i ?>[]" id="<?= $Source ?>_<?= $N['ID'] ?>" value="<?= $Source ?>" <? if (in_array($Source, $N['Sources'])) {
                                                                                                                                                                        echo ' checked="checked"';
                                                                                                                                                                    } ?> />
                            <label for="<?= $Source ?>_<?= $N['ID'] ?>"><?= $Source ?></label>
                        <?  } ?>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_processings') ?>:</strong></td>
                    <td>
                        <? foreach ($Processings as $Processing) {
                            if ($Processing == '---') {
                                continue;
                            }
                        ?>
                            <input class="m_<?= $N['ID'] ?>" type="checkbox" name="processings<?= $i ?>[]" id="<?= $Processing ?>_<?= $N['ID'] ?>" value="<?= $Processing ?>" <? if (in_array($Processing, $N['Processings'])) {
                                                                                                                                                                                    echo ' checked="checked"';
                                                                                                                                                                                } ?> />
                            <label for="<?= $Processing ?>_<?= $N['ID'] ?>"><?= $Processing == 'Encode' ? 'Encode/---' : $Processing ?></label>
                        <?  } ?>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_codecs') ?>:</strong></td>
                    <td>
                        <? foreach ($Codecs as $Codec) { ?>
                            <input class="m_<?= $N['ID'] ?>" type="checkbox" name="codecs<?= $i ?>[]" id="<?= $Codec ?>_<?= $N['ID'] ?>" value="<?= $Codec ?>" <? if (in_array($Codec, $N['Codecs'])) {
                                                                                                                                                                    echo ' checked="checked"';
                                                                                                                                                                } ?> />
                            <label for="<?= $Codec ?>_<?= $N['ID'] ?>"><?= $Codec ?></label>
                        <?  } ?>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_resolutions') ?>:</strong></td>
                    <td>
                        <? foreach ($Resolutions as $Resolution) { ?>
                            <input class="m_<?= $N['ID'] ?>" type="checkbox" name="resolutions<?= $i ?>[]" id="<?= $Resolution ?>_<?= $N['ID'] ?>" value="<?= $Resolution ?>" <? if (in_array($Resolution, $N['Resolutions'])) {
                                                                                                                                                                                    echo ' checked="checked"';
                                                                                                                                                                                } ?> />
                            <label for="<?= $Resolution ?>_<?= $N['ID'] ?>"><?= $Resolution ?></label>
                        <?  } ?>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_containers') ?>:</strong></td>
                    <td>
                        <? foreach ($Containers as $Container) {
                        ?>
                            <input class="m_<?= $N['ID'] ?>" type="checkbox" name="containers<?= $i ?>[]" id="<?= $Container ?>_<?= $N['ID'] ?>" value="<?= $Container ?>" <? if (in_array($Container, $N['Containers'])) {
                                                                                                                                                                                echo ' checked="checked"';
                                                                                                                                                                            } ?> />
                            <label for="<?= $Container ?>_<?= $N['ID'] ?>"><?= $Container ?></label>
                        <?  } ?>
                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_these_discounts') ?>:</strong></td>
                    <td>
                        <input name="frees<?= $i ?>[]" id="free_leech" type="checkbox" value="<?= 1 ?>" <? if (in_array(1, $N['FreeTorrents'])) {
                                                                                                            echo ' checked="checked"';
                                                                                                        } ?>" />
                        <label for="free_leech"><?= Lang::get('tools', 'free_leech') ?></label>
                        <input name="frees<?= $i ?>[]" id="75_percent_off" type="checkbox" value="<?= 13 ?>" <? if (in_array(13, $N['FreeTorrents'])) {
                                                                                                                    echo ' checked="checked"';
                                                                                                                } ?> />
                        <label for="75_percent_off"><?= Lang::get('tools', '75_percent_off') ?></label>
                        <input name="frees<?= $i ?>[]" id="50_percent_off" type="checkbox" value="<?= 12 ?>" <? if (in_array(12, $N['FreeTorrents'])) {
                                                                                                                    echo ' checked="checked"';
                                                                                                                } ?> />
                        <label for="50_percent_off"><?= Lang::get('tools', '50_percent_off') ?></label>
                        <input name="frees<?= $i ?>[]" id="25_percent_off" type="checkbox" value="<?= 11 ?>" <? if (in_array(11, $N['FreeTorrents'])) {
                                                                                                                    echo ' checked="checked"';
                                                                                                                } ?> />
                        <label for="25_percent_off"><?= Lang::get('tools', '25_percent_off') ?></label>
                        <input name="frees<?= $i ?>[]" id="neutral_leech" type="checkbox" value="<?= 2 ?>" / <? if (in_array(2, $N['FreeTorrents'])) {
                                                                                                                    echo ' checked="checked"';
                                                                                                                } ?>>
                        <label for="neutral_leech"><?= Lang::get('tools', 'neutral_leech') ?></label>

                    </td>
                </tr>
                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'between_the_sizes') ?>:</strong></td>
                    <td>
                        <input min="1" type="number" name="fromsize<?= $i ?>" value="<?= $N['FromSize'] ? $N['FromSize'] : 1 ?>" id="fromsize_<?= $N['ID'] ?>" placeholder="<?= Lang::get('user', 'min_1') ?>" oninput="if(value<1)value=1" onchange='smallerThen("fromsize_<?= $N['ID'] ?>", "tosize_<?= $N['ID'] ?>", "sizeerror_<?= $N['ID'] ?>")' />
                        -
                        <input min="0" type="number" name="tosize<?= $i ?>" value="<?= $N['ToSize'] ?>" id="tosize_<?= $N['ID'] ?>" onchange='smallerThen("fromsize_<?= $N['ID'] ?>", "tosize_<?= $N['ID'] ?>", "sizeerror_<?= $N['ID'] ?>")' />
                        GB
                        <span>&ensp;<?= Lang::get('user', 'leaving_blank_means_you_allow_all_sizes') ?></span>
                        <label id="sizeerror_<?= $N['ID'] ?>" class="error" style="display: none;"><?= Lang::get('user', 'please_enter_correct_numbers') ?></label>
                    </td>
                </tr>

                <tr>
                    <td class="label"><strong><?= Lang::get('user', 'only_new_releases') ?>:</strong></td>
                    <td>
                        <input type="checkbox" name="newgroupsonly<?= $i ?>" id="newgroupsonly_<?= $N['ID'] ?>" <? if ($N['NewGroupsOnly'] == '1') {
                                                                                                                    echo ' checked="checked"';
                                                                                                                } ?> />
                        <label for="newgroupsonly_<?= $N['ID'] ?>"><?= Lang::get('user', 'only_new_releases_label') ?></label>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="center">
                        <input type="submit" value="<?= ($NewFilter ? 'Create filter' : 'Update filter') ?>" />
                    </td>
                </tr>
            </table>
        </form>
    <? } ?>
</div>
<? View::show_footer(); ?>