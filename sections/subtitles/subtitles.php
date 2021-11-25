<?
//ini_set('max_file_uploads', 1);
View::show_header(Lang::get('subtitles', 'h2_subtitles'), 'validate_subtitles');
$TorrentID = $_GET['torrent_id'];
if ($TorrentID) {
    $Data = $Cache->get_value("torrent_sub_title_$TorrentID");
    if (!$Data) {
        $DB->query("
        select 
            subtitles.id, 
            subtitles.languages, 
            subtitles.torrent_id, 
            subtitles.source, 
            subtitles.download_times, 
            subtitles.format, 
            subtitles.size, 
            subtitles.uploader, 
            subtitles.upload_time, 
            subtitles.name
        from subtitles 
            where torrent_id=$TorrentID");
        $AllSubtitles = $DB->to_array();
        $DB->query("
        select 
            t.ID,
            tg.Name,
            tg.SubName,
            tg.Year,
            t.Source,
            t.Codec,
            t.Resolution,
            t.Container,
            t.Processing,
            t.Size
        from torrents as t 
        left join torrents_group as tg on tg.ID = t.GroupID
            where t.ID=$TorrentID");
        $TorrentInfo = $DB->to_array()[0];
        $Cache->cache_value("torrent_sub_title_$TorrentID", [$AllSubtitles, $TorrentInfo]);
    } else {
        list($AllSubtitles, $TorrentInfo) = $Data;
    }
}
?>



<div class="thin">
    <div class="header">
        <h2><?= Lang::get('subtitles', 'h2_subtitles') ?></h2>
    </div>
    <div class="linkbox">
        <!-- <a href="subtitles.php?action=new" class="brackets"><?= Lang::get('subtitles', 'new_subtitle') ?></a> -->
        <!-- <a href="forums.php" class="brackets"><?= Lang::get('subtitles', 'request_a_subtitle') ?></a>
        <a href="subtitles.php?action=new" class="brackets"><?= Lang::get('subtitles', 'my_subtitles') ?></a>
        <a href="subtitles.php?action=new" class="brackets"><?= Lang::get('subtitles', 'bookmarked_subtitles') ?></a> -->
    </div>

    <div id="subtitle_rules_box" class="box pad">
        <?
        if (empty($TorrentID)) {
        ?>
            <div class="center"><strong class="important_text"><?= Lang::get('subtitles', 'top_warning') ?></strong></div>
        <?
        }
        ?>
        <?= Lang::get('subtitles', 'subtitle_rules') ?>
    </div>

    <!-- <div id="subtitle_search_box">
        <input id="subtitle_search_title" type="search" placeholder="<?= Lang::get('subtitles', 'title_or_imdb_link') ?>">
        <input id="subtitle_search_year"  type="search" placeholder="<?= Lang::get('subtitles', 'year_optional') ?>">
        <select id="subtitle_search_language" name="TargetLanguageId" class="form__input">
            请注意，下列 option 的 value 都与 GPW 不对应
            <option value="14">简中</option><option value="14">繁中</option><option value="3">English</option><option value="14">日语 japanese</option><option value="19">韩语 korean</option><option value="" selected="selected">---</option><option value="22">Arabic</option><option value="49">Brazilian Portuguese</option><option value="29">Bulgarian</option><option value="14">Chinese</option><option value="23">Croatian</option><option value="30">Czech</option><option value="10">Danish</option><option value="9">Dutch</option><option value="38">Estonian</option><option value="15">Finnish</option><option value="6">German</option><option value="26">Greek</option><option value="40">Hebrew</option><option value="41">Hindi</option><option value="24">Hungarian</option><option value="28">Icelandic</option><option value="47">Indonesian</option><option value="16">Italian</option></option><option value="37">Latvian</option><option value="39">Lithuanian</option><option value="12">Norwegian</option><option value="52">Persian</option><option value="17">Polish</option><option value="21">Portuguese</option><option value="13">Romanian</option><option value="7">Russian</option><option value="31">Serbian</option><option value="42">Slovak</option><option value="43">Slovenian</option><option value="11">Swedish</option><option value="20">Thai</option><option value="18">Turkish</option><option value="34">Ukrainian</option><option value="25">Vietnamese</option>
        </select>
        <select id="subtitle_search_format" name="" class="form__input">
            <option><?= Lang::get('global', 'format') ?></option>
            <option>SRT</option>
            <option>ASS</option>
            <option>SUP</option>
            <option>VTT</option>
            <option><?= Lang::get('global', 'others') ?></option>
        </select>
        <button><?= Lang::get('subtitles', 'search') ?></button>
    </div> -->
    <!-- 原来的那个表我挪到字幕收藏里边去了，基于单个字幕的展示更适合那里，这里我认为还是应该采用分组管理 -->

    <!-- <table id="subtitle_browse_table">
        <tr class="colhead">
            <td>这一列是伸缩展开按钮的位置</td>
            <td style="70px"><?= Lang::get('global', 'language') ?></td>
            <td><?= Lang::get('subtitles', 'movie_title') ?></td>
            <td><?= Lang::get('global', 'format') ?></td>
            <td><?= Lang::get('global', 'size') ?></td>
            <td><?= Lang::get('subtitles', 'times_of_download') ?></td>
            <td><?= Lang::get('global', 'time') ?></td>
        </tr>
        <tr class="group">
            <td>这一列是伸缩展开按钮的位置</td>
            <td colspan="6">x264 / WEB / 1080p / MKV</td>
        </tr>
        <tr class="torrent">
            <td>这一列是伸缩展开按钮的位置</td>
            <td colspan="6">[流浪地球] The Wandering Earth (2019) by 郭帆</td>
        </tr>
        <tr class="subtitle">
            <td><img class="national_flags" src="static/common/flags/China.png"></td>
            <td><a href="subtitles.php?action=detail">Liu.lang.di.qiu.2019.REPACK.1080p.BluRay.DDP7.1.x264-Geek.srt</a>
            <span class="float_right">[ <a>DL</a> | <a>RP</a> ]</span>
        </td>
            <td>SRT</td>
            <td>3.28 KB</td>
            <td>166</td>
            <td>1 分前</td>
        </tr>
    </table> -->
    <?
    if (isset($Err)) {
        echo "\t" . '<p style="text-align: center;" class="important_text">' . $Err . "</p>\n";
    }
    if ($TorrentID) {
        $Title = Torrents::torrent_group_name($TorrentInfo, false, true, true);
    ?>

        <h3 id="subtitle_for_torrent_title"><?= $Title ?></h3>
        <table id="subtitle_browse_table">
            <tr class="colhead">
                <!-- <td>这一列是伸缩展开按钮的位置</td> -->
                <td style="70px"><?= Lang::get('global', 'language') ?></td>
                <td><?= Lang::get('subtitles', 'subtitle_names') ?></td>
                <td><?= Lang::get('global', 'format') ?></td>
                <td><?= Lang::get('global', 'size') ?></td>
                <td><?= Lang::get('subtitles', 'times_of_download') ?></td>
                <td><?= Lang::get('subtitles', 'subtitle_uploader') ?></td>
                <td><?= Lang::get('global', 'time') ?></td>
            </tr>
            <?

            if ($AllSubtitles) {
                $Labels = ['chinese_simplified', 'chinese_traditional', 'english', 'japanese', 'korean', 'no_subtitles', 'arabic', 'brazilian_port', 'bulgarian', 'croatian', 'czech', 'danish', 'dutch', 'estonian', 'finnish', 'french', 'german', 'greek', 'hebrew', 'hindi', 'hungarian', 'icelandic', 'indonesian', 'italian', 'latvian', 'lithuanian', 'norwegian', 'persian', 'polish', 'portuguese', 'romanian', 'russian', 'serbian', 'slovak', 'slovenian', 'spanish', 'swedish', 'thai', 'turkish', 'ukrainian', 'vietnamese'];
                $Images = ['China.png', 'Hong Kong.png', 'United States of America.png', 'Japan.png', 'South Korea.png', 'none.png', 'Palestine.png', 'Brazil.png', 'Bulgaria.png', 'Croatia.png', 'Czech Republic.png', 'Denmark.png', 'Netherlands.png', 'Estonia.png', 'Finland.png', 'France.png', 'Germany.png', 'Greece.png', 'Israel.png', 'India.png', 'Hungary.png', 'Iceland.png', 'Indonesia.png', 'Italy.png', 'Latvia.png', 'Lithuania.png', 'Norway.png', 'Iran.png', 'Poland.png', 'Portugal.png', 'Romania.png', 'Russian Federation.png', 'Serbia.png', 'Slovakia.png', 'Slovenia.png', 'Spain.png', 'Sweden.png', 'Thailand.png', 'Turkey.png', 'Ukraine.png', 'Viet Nam.png'];
                foreach ($AllSubtitles as $Subtitle) {
                    $LanguageArray = explode(',', $Subtitle['languages']);
                    $IsNew = time_ago($Subtitle['upload_time']) < 60;
                    $CanRM = check_perms('users_mod');
            ?>
                    <tr class="subtitle">
                        <td>
                            <?
                            foreach ($LanguageArray as $Language) {
                            ?>
                                <img class="national_flags" src="static/common/flags/<?= $Images[array_search($Language, $Labels)] ?>">
                            <?
                            }
                            ?>
                        </td>
                        <td><?= $Subtitle['name'] . ' ' ?><span class="important_text"><?= ($IsNew ? '(' . Lang::get('subtitles', 'new') . '!)' : '') ?></span><span class="float_right">[ <a href="subtitles.php?action=download&id=<?= $Subtitle['id'] ?>" class="tooltip" title="<?= Lang::get('global', 'download') ?>">DL</a> | <a href="reportsv2.php?action=report&type=subtitle_track_bad&id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('global', 'report') ?>">RP</a> <?= $CanRM ? '| <a href="subtitles.php?action=delete&id=' . $Subtitle['id'] . '" class="tooltip" title="' . Lang::get('global', 'remove') . '">RM</a> ' : '' ?>] </span></td>
                        <td><?= $Subtitle['format'] ?></td>
                        <td><?= Format::get_size($Subtitle['size']) ?></td>
                        <td><?= $Subtitle['download_times'] ?></td>
                        <td><?= Users::format_username($Subtitle['uploader'], false, false, false) ?></td>
                        <td><?= time_diff($Subtitle['upload_time']) ?></td>
                    </tr>
            <?
                }
            }
            ?>

        </table>



        <div id="subtitle_upload_form_container" class="table_container">
            <form id="subtitle_upload_form" action="subtitles.php" method='post' enctype="multipart/form-data" accept-charset='UTF-8'>
                <?

                ?>
                <table id="subtitle_upload_table">
                    <tr>
                        <td class="right"><?= Lang::get('subtitles', 'upload_a_subtitle') ?>:</td>
                        <td class=""><input id="file" name="file_input" type="file" accept=".sub,.idx,.sup,.srt,.vtt,.ass,.zip,.rar,.7z,.smi,.ssa"></td>
                    </tr>
                    <tr>
                        <td class="right"><?= Lang::get('subtitles', 'torrent_pl') ?>:</td>
                        <td class=""><input readonly value="<?= site_url() . '/torrents.php?torrentid=' . $TorrentID ?>" name="torrent_pl_link" id="subtitle_torrent_pl" type="url" placeholder="https://greatposterwall.com/torrents.php?torrentid=12345"></td>
                    </tr>
                    <tr>
                        <td class="right"><?= Lang::get('global', 'language') ?>:</td>
                        <td class="">
                            <div id="subtitles_container" class="error-container">
                                <div id="common_subtitles" class="grid_subtitles">
                                    <?
                                    function genSubcheckboxes($Labels, $Images, $Subtitles) {
                                        for ($i = 0; $i < count($Labels); $i++) {
                                            echo '<div class="subtitle"><input id="' . $Labels[$i] . '" type="checkbox" name="languages[]" value="' . $Labels[$i] . '"' . (strpos($Subtitles, $Labels[$i]) === false ? "" : "checked=\"checked\"") . '><label for="' . $Labels[$i] . '"><img class="national_flags" src="static/common/flags/' . $Images[$i] . '"> ' . Lang::get('upload', $Labels[$i]) . '</label></div>';
                                        }
                                    }
                                    $Labels = ['chinese_simplified', 'chinese_traditional', 'english', 'japanese', 'korean'];
                                    $Images = ['China.png', 'Hong Kong.png', 'United States of America.png', 'Japan.png', 'South Korea.png'];
                                    genSubcheckboxes($Labels, $Images, "");
                                    ?>
                                    <a href="javascript:$('#other_subtitles').new_toggle()"><?= Lang::get('upload', 'show_more') ?></a>
                                </div>
                                <div id="other_subtitles" style="display: none;">
                                    <div class="grid_subtitles">
                                        <?
                                        $Labels = ['no_subtitles', 'arabic', 'brazilian_port', 'bulgarian', 'croatian', 'czech', 'danish', 'dutch', 'estonian', 'finnish', 'french', 'german', 'greek', 'hebrew', 'hindi', 'hungarian', 'icelandic', 'indonesian', 'italian', 'latvian', 'lithuanian', 'norwegian', 'persian', 'polish', 'portuguese', 'romanian', 'russian', 'serbian', 'slovak', 'slovenian', 'spanish', 'swedish', 'thai', 'turkish', 'ukrainian', 'vietnamese'];
                                        $Images = ['none.png', 'Palestine.png', 'Brazil.png', 'Bulgaria.png', 'Croatia.png', 'Czech Republic.png', 'Denmark.png', 'Netherlands.png', 'Estonia.png', 'Finland.png', 'France.png', 'Germany.png', 'Greece.png', 'Israel.png', 'India.png', 'Hungary.png', 'Iceland.png', 'Indonesia.png', 'Italy.png', 'Latvia.png', 'Lithuania.png', 'Norway.png', 'Iran.png', 'Poland.png', 'Portugal.png', 'Romania.png', 'Russian Federation.png', 'Serbia.png', 'Slovakia.png', 'Slovenia.png', 'Spain.png', 'Sweden.png', 'Thailand.png', 'Turkey.png', 'Ukraine.png', 'Viet Nam.png'];
                                        genSubcheckboxes($Labels, $Images, "");
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <!-- <tr>
                <td class="right"><?= Lang::get('subtitles', 'source') ?>:</td>
                <td class=""><input name="source" type="text" placeholder="<?= Lang::get('subtitles', 'a_subtitle_group') ?>"></td>
            </tr> -->
                    <!-- <tr>
                <td class="right"><?= Lang::get('subtitles', 'anonymous_upload') ?>:</td>
                <td class=""><label><input type="checkbox">
                <?= Lang::get('subtitles', 'do_not_show_my_username') ?></label></td>
            </tr> -->
                    <tr>
                        <td colspan="2" class="center"><?= Lang::get('subtitles', 'subtitle_upload_warning') ?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="center"><input id="post" name="submit" type="submit" value="<?= Lang::get('subtitles', 'button_upload') ?>"></td>
                    </tr>
                </table>
            </form>
        </div>
    <?
    }
    ?>
</div>



<?
View::show_footer();
