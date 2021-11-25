<?

/********************************************************************************
 ************ Torrent form class *************** upload.php and torrents.php ****
 ********************************************************************************
 ** This class is used to create both the upload form, and the 'edit torrent'  **
 ** form. It is broken down into several functions - head(), foot(),           **
 ** music_form() [music], audiobook_form() [Audiobooks and comedy], and        **
 ** simple_form() [everything else].                                           **
 **                                                                         **
 ** When it is called from the edit page, the forms are shortened quite a bit. **
 **                                                                         **
 ********************************************************************************/
class TORRENT_FORM {
    var $UploadForm = '';
    var $Categories = array();
    var $Sources = array();
    var $Codecs = array();
    var $Containers = array();
    var $Resolutions = array();
    var $Makers = array();
    var $NewTorrent = false;
    var $Torrent = array();
    var $Error = false;
    var $TorrentID = false;
    var $Disabled = '';
    var $DisabledFlag = false;

    const TORRENT_INPUT_ACCEPT = ['application/x-bittorrent', '.torrent'];
    const JSON_INPUT_ACCEPT = ['application/json', '.json'];

    function __construct($Torrent = array(), $Error = false, $NewTorrent = true) {

        $this->NewTorrent = $NewTorrent;
        $this->Torrent = $Torrent;
        $this->Error = $Error;

        global $UploadForm, $Categories, $Sources, $Codecs, $Containers, $Resolutions, $Processings, $Makers, $TorrentID;

        $this->UploadForm = $UploadForm;
        $this->Categories = $Categories;
        $this->Sources = $Sources;
        $this->Codecs = $Codecs;
        $this->Containers = $Containers;
        $this->Resolutions = $Resolutions;
        $this->Processings = $Processings;
        $this->Makers = $Makers;
        $this->TorrentID = $TorrentID;

        if ($this->Torrent && isset($this->Torrent['GroupID'])) {
            $this->Disabled = ' readonly';
            $this->DisabledFlag = true;
        }
    }

    function head() {
        $AnnounceURL = (G::$LoggedUser['HttpsTracker']) ? ANNOUNCE_HTTPS_URL : ANNOUNCE_HTTP_URL;
?>

        <div class="thin">
            <? if ($this->NewTorrent) { ?>
                <p style="text-align: center; margin-bottom: 1px !important;">
                    <?= Lang::get('upload', 'personal_announce') ?>:
                    <br />
                    <a onclick="return false" href="<?= $AnnounceURL . '/' . G::$LoggedUser['torrent_pass'] . '/announce' ?>"><?= Lang::get('upload', 'personal_announce_note') ?></a>
                </p>
            <?      }
            if ($this->Error) {
                echo "\t" . '<p style="text-align: center;" class="important_text">' . $this->Error . "</p>\n";
            }
            ?>
            <? if ($this->NewTorrent && $this->Torrent['GroupID']) { ?>
                <h2><?= Lang::get('torrents', 'add_format') ?> &gt; <a href="torrents.php?id=<?= $this->Torrent['GroupID'] ?>"><?= Torrents::torrent_group_name($this->Torrent, true) ?></a></h2>
            <? } ?>
            <form class="create_form form-validation <?= ($this->Error || ($this->Torrent && isset($this->Torrent['GroupID']))) ? "autofilled" : "" ?>" name="torrent" action="" enctype="multipart/form-data" method="post" id="upload_table">
                <div>
                    <input type="hidden" name="submit" value="true" />
                    <input type="hidden" name="auth" value="<?= G::$LoggedUser['AuthKey'] ?>" />
                    <? if (!$this->NewTorrent) { ?>
                        <input type="hidden" name="action" value="takeedit" />
                        <input type="hidden" name="torrentid" value="<?= display_str($this->TorrentID) ?>" />
                        <input type="hidden" name="type" value="<?= display_str($this->Torrent['CategoryID']) ?>" />
                        <?
                    } else {
                        if ($this->Torrent && $this->Torrent['GroupID']) {
                        ?>
                            <input type="hidden" name="groupid" value="<?= display_str($this->Torrent['GroupID']) ?>" />
                            <input type="hidden" name="type" value="<?= in_array($this->UploadForm, $this->Categories) ?>" />
                        <?
                        }
                        if ($this->Torrent && isset($this->Torrent['RequestID'])) {
                        ?>
                            <input type="hidden" name="requestid" value="<?= display_str($this->Torrent['RequestID']) ?>" />
                    <?
                        }
                    }
                    ?>
                </div>
                <? if ($this->NewTorrent) { ?>
                    <table cellpadding="3" cellspacing="1" border="0" class="layout border" width="100%">
                        <tr class="file_tr">
                            <td class="label"><?= Lang::get('upload', 'torrent_file') ?><span class="important_text">*</span>:</td>
                            <td class="error-container">
                                <input id="file" type="file" name="file_input" size="50" accept="<?= implode(',', self::TORRENT_INPUT_ACCEPT); ?>" />
                            </td>
                        </tr>

                        <tr class="hidden">
                            <td class="label"><?= Lang::get('upload', 'type') ?>:</td>
                            <td>
                                <select id="categories" name="type" onchange="Categories()" <?= $this->Disabled ?>>
                                    <?
                                    foreach (Misc::display_array($this->Categories) as $Index => $Cat) {
                                        if ($Cat == "Applications") {
                                            if (!check_perms('users_mod')) {
                                                continue;
                                            }
                                        }
                                        echo "\t\t\t\t\t\t<option value=\"$Index\"";
                                        if ($Cat == $this->Torrent['CategoryName']) {
                                            echo ' selected="selected"';
                                        }
                                        echo ">$Cat</option>\n";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                <?      }/*if*/ ?>
                <div id="dynamic_form">
                <?
            } // function head


            function foot() {
                $Torrent = $this->Torrent;
                ?>
                </div>
                <div class="collapse buttons">
                    <table cellpadding="3" cellspacing="1" border="0" class="layout border slice" width="100%">
                        <?
                        if (!$this->NewTorrent) {
                            if (check_perms('torrents_freeleech')) {
                        ?>
                                <tr id="freetorrent">
                                    <td class="label"><?= Lang::get('upload', 'freeleech') ?>:</td>
                                    <td>
                                        <select name="freeleech">
                                            <?
                                            $FL = array(0 => "Normal", 1 => "Free", 2 => "Neutral", 11 => "-25%", 12 => "-50%", 13 => "-75%");
                                            // $FL = array("Normal", "Free", "Neutral");
                                            foreach ($FL as $Key => $Name) {
                                            ?>
                                                <option value="<?= $Key ?>" <?= ($Key == $Torrent['FreeTorrent'] ? ' selected="selected"' : '') ?>>
                                                    <?= $Name ?></option>
                                            <?              } ?>
                                        </select>
                                        <script>
                                            $(document).ready(() => {
                                                $("#limit-time").click(() => {
                                                    if ($("#limit-time")[0].checked) {
                                                        $("#input-free-date,#input-free-time").show()
                                                        if (<?= $Torrent['FreeEndTime'] ? "false" : "true" ?>) {
                                                            const d = new Date()
                                                            $("#input-free-date")[0].value = d.getFullYear() + "-" + ("0" + (d.getMonth() +
                                                                1)).substr(-2) + "-" + ("0" + d.getDate()).substr(-2)
                                                            $("#input-free-time")[0].value = ("0" + d.getHours()).substr(-2) + ":" + ("0" + d
                                                                .getMinutes()).substr(-2)
                                                        }

                                                    } else {
                                                        $("#input-free-date,#input-free-time").hide()
                                                    }
                                                })
                                            })
                                        </script>
                                        <input type="checkbox" id="limit-time" name="limit-time" <?= $Torrent['FreeEndTime'] ? " checked=\"checked\"" : "" ?> />&nbsp;<label for="limit-time" style="display: inline;">定时</label>&nbsp;
                                        <input id="input-free-date" name="free-date" type="date" <?= $Torrent['FreeEndTime'] ? "value=\"" . substr($Torrent['FreeEndTime'], 0, 10) . "\"" : "style=\"display:none;\"" ?> /><input id="input-free-time" name="free-time" type="time" <?= $Torrent['FreeEndTime'] ? "value=\"" . substr($Torrent['FreeEndTime'], 11, 5) . "\"" : "style=\"display:none;\"" ?> />
                                        <?= Lang::get('upload', 'because') ?>
                                        <select name="freeleechtype">
                                            <?
                                            $FL = array("N/A", "Staff Pick", "Perma-FL", "Vanity House");
                                            foreach ($FL as $Key => $Name) {
                                            ?>
                                                <option value="<?= $Key ?>" <?= ($Key == $Torrent['FreeLeechType'] ? ' selected="selected"' : '') ?>><?= $Name ?></option>
                                            <?              } ?>
                                        </select>
                                    </td>
                                </tr>
                        <?
                            }
                        }
                        ?>
                        <tr class="section_tr">
                            <td colspan="2" style="text-align: center;">
                                <?
                                if ($this->NewTorrent) {
                                ?>
                                    <br />
                                    <p>
                                        <?= Lang::get('upload', 'assurance') ?>
                                    </p>
                                    <? if ($this->NewTorrent) { ?>
                                        <?= Lang::get('upload', 'assurance_note') ?>
                                <?      }
                                }
                                ?>
                                <button type="submit" id="post" class="btn loading" />
                                <span class="text">
                                    <? if ($this->NewTorrent) {
                                        echo 'Upload torrent';
                                    } else {
                                        echo 'Edit torrent';
                                    } ?>
                                </span>
                                <span class="icon"></span>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    <?
            } //function foot

            function movie_form($GenreTags) {
                $QueryID = G::$DB->get_query_id();
                $Torrent = $this->Torrent;
                $IsRemaster = true;
                $NoSub = isset($Torrent['NoSub']) ? $Torrent['NoSub'] : null;
                $HardSub = isset($Torrent['HardSub']) ? $Torrent['HardSub'] : null;
                $BadFolders = isset($Torrent['BadFolders']) ? $Torrent['BadFolders'] : null;
                $CustomTrumpable = isset($Torrent['CustomTrumpable']) ? $Torrent['CustomTrumpable'] : null;
                $RemasterTitle = isset($Torrent['RemasterTitle']) ? $Torrent['RemasterTitle'] : null;
                $RemasterYear = isset($Torrent['RemasterYear']) ? $Torrent['RemasterYear'] : null;
                $RemasterCustomTitle = isset($Torrent['RemasterCustomTitle']) ? $Torrent['RemasterCustomTitle'] : null;
                $Scene = isset($Torrent['Scene']) ? $Torrent['Scene'] : null;
                $TorrentDescription = isset($Torrent['TorrentDescription']) ? $Torrent['TorrentDescription'] : null;
                $TorrentCodec = isset($Torrent['Codec']) ? $Torrent['Codec'] : null;
                $TorrentSource = isset($Torrent['Source']) ? $Torrent['Source'] : null;
                $TorrentContainer = isset($Torrent['Container']) ? $Torrent['Container'] : null;
                $TorrentResolution = isset($Torrent['Resolution']) ? $Torrent['Resolution'] : null;
                $TorrentProcessing = isset($Torrent['Processing']) ? $Torrent['Processing'] : null;
                $Subtitles = isset($Torrent['Subtitles']) ? $Torrent['Subtitles'] : null;
                $Buy = isset($Torrent['Buy']) ? $Torrent['Buy'] : null;
                $Diy = isset($Torrent['Diy']) ? $Torrent['Diy'] : null;
                $Jinzhuan = isset($Torrent['Jinzhuan']) ? $Torrent['Jinzhuan'] : null;
                $IMDBID = isset($Torrent['IMDBID']) ? $Torrent['IMDBID'] : null;
                $SpecialSub = isset($Torrent['SpecialSub']) ? $Torrent['SpecialSub'] : null;
                $ChineseDubbed = isset($Torrent['ChineseDubbed']) ? $Torrent['ChineseDubbed'] : null;
                $MediaInfos = isset($Torrent['MediaInfo']) ? json_decode($Torrent['MediaInfo']) : null;
                $SubtitleType = isset($Torrent['SubtitleType']) ? $Torrent['SubtitleType'] : null;
                $Note = isset($Torrent['Note']) ? $Torrent['Note'] : null;
                global $MovieTypes;
    ?>
        <table cellpadding="3" cellspacing="1" border="0" class="layout border<? if ($this->NewTorrent) {
                                                                                    echo ' slice';
                                                                                } ?>" width="100%">
            <? if ($this->NewTorrent) { ?>
                <tr>
                    <td class="label"><?= Lang::get('upload', 'movie_imdb') ?><span class="important_text">*</span>:</td>
                    <td class="error-container">
                        <input type="text" id="imdb" name="imdb" size="45" <?= $this->Disabled ?> value=<?= $IMDBID ?>>
                        <button id='imdb_button' class='btn autofill loading' onclick="movieAutofill()" <?= $this->Disabled ? "disabled" : '' ?> type='button' /><span class="text"><?= Lang::get('upload', 'movie_fill') ?></span><span class="icon"></span></button>
                        <input type="checkbox" name="no_imdb_link" id="no_imdb_link" onchange="noImdbId()" <?= $this->Disabled ? "disabled" : '' ?>><label for="no_imdb_link"><?= Lang::get('upload', 'no_imdb_link') ?></label>
                        <div class="no-imdb-note"><?= Lang::get('upload', 'imdb_empty_warning') ?></div>
                        <div class="no-imdb-note hidden important-text"><?= Lang::get('upload', 'no_imdb_note') ?></div>
                        <div class="imdb error-message"></div>
                    </td>
                </tr>
        </table>
        <div class="collapse">
            <table cellpadding="3" cellspacing="1" border="0" class="layout border<? if ($this->NewTorrent) {
                                                                                        echo ' slice';
                                                                                    } ?>" width="100%">
                <tr id="releasetype_tr" class="section_tr">
                    <td class="label">
                        <span id="movie_type"><?= Lang::get('upload', 'movie_type') ?><span class="important_text">*</span>:</span>
                    </td>
                    <td class="error-container">
                        <select id="releasetype" name="releasetype" <?= $this->Disabled ?>>
                            <option value=''>---</option>
                            <?

                            foreach ($MovieTypes as $Key => $Val) {
                                echo "\t\t\t\t\t\t<option value=\"$Key\"";
                                if ($Key == $Torrent['ReleaseType']) {
                                    echo ' selected="selected"';
                                } else if ($this->DisabledFlag) {
                                    echo "disabled";
                                }
                                echo ">" . Lang::get('torrents', 'release_types')[$Key] . "</option>\n";
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><?= Lang::get('upload', 'movie_upload_note') ?></td>
                </tr>
                <tr id="title_tr">
                    <td class="label"><?= Lang::get('upload', 'movie_title') ?><span class="important_text">*</span>:</td>
                    <td class="error-container">
                        <input type="text" id="name" name="name" size="45" value="<?= display_str($Torrent['Name']) ?>" <?= $this->Disabled ?> />
                    </td>

                </tr>
                <tr id="aliases_tr">
                    <td class="label"><?= Lang::get('upload', 'movie_aliases') ?>:</td>
                    <td>
                        <input type="text" id="subname" name="subname" size="45" value="<?= display_str($Torrent['SubName']) ?>" <?= $this->Disabled ?> />
                        <p class="upload_form_note"><?= Lang::get('upload', 'movie_aliases_note') ?></p>
                    </td>
                </tr>
                <tr id="year_tr">
                    <td class="label">
                        <span id="year_label_not_remaster" <? if ($IsRemaster) {
                                                                echo ' class="hidden"';
                                                            }
                                                            ?>><?= Lang::get('upload', 'year') ?>:</span>
                        <span id="year_label_remaster" <? if (!$IsRemaster) {
                                                            echo ' class="hidden"';
                                                        }
                                                        ?>><?= Lang::get('upload', 'year_remaster') ?><span class="important_text">*</span>:</span>
                    </td>

                    <td class="error-container">
                        <p id="yearwarning" class="hidden"><?= Lang::get('upload', 'year_remaster_title') ?></p>
                        <input type="text" id="year" name="year" size="5" value="<?= display_str($Torrent['Year']) ?>" <?= $this->Disabled ?> />
                    </td>
                </tr>
                <!-- <tr>
        <td></td>
        <td><?= Lang::get('upload', 'album_note') ?></td>
    </tr> -->
                <tr id="artist_tr">
                    <td class="label"><?= Lang::get('global', 'artist') ?><span class="important_text">*</span>:</td>
                    <td id="artistfields" class="artistfields items">
                        <p id="vawarning" class="hidden"><?= Lang::get('upload', 'artist_note') ?></p>
                        <?
                        if (!empty($Torrent['Artists'])) {
                            $FirstArtist = true;
                            foreach ($Torrent['Artists'] as $Importance => $Artists) {
                                foreach ($Artists as $Artist) {
                        ?>
                                    <div class="artist item">
                                        <input type="hidden" id="artist_id" name="artist_ids[]" value="<?= display_str($Artist['imdbid']) ?>" size="45" />
                                        <input type="text" id="artist" name="artists[]" size="45" value="<?= display_str($Artist['name']) ?>" <? Users::has_autocomplete_enabled('other'); ?><?= $this->Disabled ?> /><input type="text" id="artist_chinese" title="<?= Lang::get('upload', 'chinese_name') ?>" name="artists_chinese[]" size="25" value="<?= display_str($Artist['name']) ?>" <? Users::has_autocomplete_enabled('other'); ?><?= $this->Disabled ?> />

                                        <select id="importance" name="importance[]" <?= $this->Disabled ?>>
                                            <option value="1" <?= ($Importance == '1' ? ' selected="selected"' : ($this->DisabledFlag ? 'disabled' : '')) ?>>
                                                <?= Lang::get('upload', 'director') ?></option>
                                            <option value="2" <?= ($Importance == '2' ? ' selected="selected"' : ($this->DisabledFlag ? 'disabled' : '')) ?>>
                                                <?= Lang::get('upload', 'writer') ?></option>
                                            <option value="4" <?= ($Importance == '3' ? ' selected="selected"' : ($this->DisabledFlag ? 'disabled' : '')) ?>>
                                                <?= Lang::get('upload', 'movie_producer') ?></option>
                                            <option value="5" <?= ($Importance == '4' ? ' selected="selected"' : ($this->DisabledFlag ? 'disabled' : '')) ?>>
                                                <?= Lang::get('upload', 'composer') ?></option>
                                            <option value="6" <?= ($Importance == '5' ? ' selected="selected"' : ($this->DisabledFlag ? 'disabled' : '')) ?>>
                                                <?= Lang::get('upload', 'cinematographer') ?></option>
                                            <option value="6" <?= ($Importance == '6' ? ' selected="selected"' : ($this->DisabledFlag ? 'disabled' : '')) ?>>
                                                <?= Lang::get('upload', 'actor') ?></option>
                                        </select>
                                        <?
                                        if ($FirstArtist) {
                                            if (!$this->DisabledFlag) {
                                        ?>
                                                <a href="javascript:AddArtistField(true)" class="brackets">+</a> <a href="javascript:RemoveArtistField()" class="brackets">&minus;</a>
                                        <?
                                            }
                                            $FirstArtist = false;
                                        }
                                        ?>
                                        <br />
                                    </div>
                            <?
                                }
                            }
                        } else {
                            ?>
                            <div class="artist item">
                                <input type="hidden" id="artist_id" name="artist_ids[]" size="45" />
                                <input type="text" id="artist" name="artists[]" size="45" <?
                                                                                            Users::has_autocomplete_enabled('other'); ?><?= $this->Disabled ?> />
                                <input type="text" id="artist_chinese" name="artists_chinese[]" size="25" placeholder="<?= Lang::get('upload', 'chinese_name') ?>" <?
                                                                                                                                                                    Users::has_autocomplete_enabled('other'); ?><?= $this->Disabled ?> />
                                <select id="importance" name="importance[]" <?= $this->Disabled ?>>
                                    <option value="1"><?= Lang::get('upload', 'director') ?></option>
                                    <option value="2"><?= Lang::get('upload', 'writer') ?></option>
                                    <option value="3"><?= Lang::get('upload', 'movie_producer') ?></option>
                                    <option value="4"><?= Lang::get('upload', 'composer') ?></option>
                                    <option value="5"><?= Lang::get('upload', 'cinematographer') ?></option>
                                    <option value="6"><?= Lang::get('upload', 'actor') ?></option>
                                </select>
                                <a href="#" onclick="AddArtistField(true); return false;" class="brackets add-artist">+</a>
                                <a href="#" onclick="RemoveArtistField(); return false;" class="brackets remove-artist">&minus;</a>
                            </div>
                            <?= Lang::get('upload', 'torrent_rule') ?>
                        <?          } ?>
                        <div class="show-more" style="display: none;">
                            <a href='#' onclick="artistsShowMore(); return false"><?= Lang::get('upload', 'show_more') ?></a>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td class="label"><?= Lang::get('upload', 'movie_cover') ?><span class="important_text">*</span>:</td>
                    <script>
                        function imgUpload(file = false) {
                            UploadImage(file, url => {
                                $('#image').val(url)
                            })
                        }
                    </script>
                    <td class="error-container"><input ondrop="drop(event)" ondragover="allowDrop(event)" type="text" id="image" name="image" size="60" value="<?= display_str($Torrent['Image']) ?>" <?= $this->Disabled ?> /> <input type="button" onclick="imgUpload()" <?= $this->Disabled ? "disabled" : '' ?> value="<?= Lang::get('upload', 'upload_img') ?>"> <span id="imgUploadPer"></span>
                        <!-- <br /><?= Lang::get('upload', 'image_note') ?> -->
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('upload', 'trailer_link') ?>:</td>
                    <td><input type="text" id="trailer_link" name="trailer_link" size="60" <?= $this->Disabled ?> />
                </tr>

                <?
                    if ($this->NewTorrent) {
                ?>
                    <tr>
                        <td class="label"><?= Lang::get('upload', 'tags') ?><span class="important_text">*</span>:</td>
                        <td class="error-container">
                            <? if ($GenreTags) { ?>
                                <select id="genre_tags" name="genre_tags" onchange="add_tag(); return false;" <?= $this->Disabled ?>>
                                    <? foreach (Misc::display_array($GenreTags) as $Genre) { ?>
                                        <option value="<?= $Genre ?>"><?= $Genre ?></option>
                                    <?              } ?>
                                </select>
                            <?          } ?>
                            <input type="text" id="tags" name="tags" size="40" value="<?= display_str($Torrent['TagList']) ?>" <?
                                                                                                                                Users::has_autocomplete_enabled('other'); ?><?= $this->Disabled ?> />
                            <!-- <br />
<? Rules::display_site_tag_rules(true); ?> -->
                        </td>
                    </tr>
                    <tr>
                        <td class="label"><?= Lang::get('upload', 'chinese_movie_synopsis') ?><span class="important_text">*</span>:</td>
                        <td class="error-container">
                            <?php new TEXTAREA_PREVIEW('album_desc', 'album_desc', display_str($Torrent['GroupDescription']), 60, 8, false, false, false, array($this->Disabled)); ?>
                        </td>
                    </tr>
            <?
                    }
                }
            ?>
            <tr class="text_tr section_tr">
                <td class="label"><?= Lang::get('upload', 'movie_scene') ?>:</td>
                <td>
                    <input type="checkbox" id="scene" name="scene" <? if ($Scene) {
                                                                        echo 'checked="checked" ';
                                                                    } ?> />
                    <label for="scene"><?= Lang::get('upload', 'movie_scene_label') ?></label>
                    <p class="upload_form_note"><?= Lang::get('upload', 'movie_scene_note') ?></p>
                </td>
            </tr>
            <tr class="text_tr">
                <td class="label"><?= Lang::get('upload', 'not_main_movie') ?>:</td>
                <td>
                    <input type="checkbox" id="not_main_movie" name="not_main_movie" <?= $Torrent['NotMainMovie'] ? "checked" : "" ?> <?= $this->Disabled ?> />
                    <label for="not_main_movie"><?= Lang::get('upload', 'not_main_movie_label') ?></label>
                    <p class="upload_form_note"><?= Lang::get('upload', 'not_main_movie_note') ?></p>
                </td>
            </tr>

            <tr id="movie_edition_information_tr" class="text_tr">
                <td class="label"><?= Lang::get('upload', 'movie_edition_information') ?>:</td>
                <td class="error-container">

                    <div><input onclick="$('#movie_edition_information_container').new_toggle()" type="checkbox" id="movie_edition_information" name="movie_edition_information" <?= $RemasterTitle || $RemasterCustomTitle || $RemasterYear ? "checked " : "" ?>>
                        <label for="movie_edition_information"><?= Lang::get('upload', 'movie_edition_information_label') ?></label>
                    </div>
                    <?= Lang::get('upload', 'movie_edition_information_examples') ?>
                    <input type="hidden" id="remaster_title_hide" name="remaster_title" value="<?= display_str($RemasterTitle) ?>" />
                    <div id="movie_edition_information_container" style="display: none">
                        <div><?= Lang::get('upload', 'movie_information') ?>: <input readonly type="text" name="remaster_title_show" id="remaster_title_show" size="80" value="<?= $RemasterTitle ? display_str(Torrents::display_edition_info($RemasterTitle)) : '' ?>" />
                        </div>
                        <div id="movie_remaster_tags">
                            <div><?= Lang::get('upload', 'collections') ?>:
                                <?
                                function genRemasterTags($RemasterTags, $SelectedTitle) {
                                    for ($i = 0; $i < count($RemasterTags); $i++) {
                                        if ($i) echo ', ';
                                        $remasterStyle = '';
                                        if ($SelectedTitle && strstr($SelectedTitle, $RemasterTags[$i])) {
                                            $remasterStyle = ' style="color:#ffbb33" ';
                                        }
                                        echo '<a ' . $remasterStyle . 'onclick="remasterTags(this, \'' . $RemasterTags[$i] . '\')" href="javascript:void(0)">' . Lang::get('upload', $RemasterTags[$i]) . '</a>';
                                    }
                                }

                                $RemasterTags = ['masters_of_cinema', 'the_criterion_collection', 'warner_archive_collection'];
                                genRemasterTags($RemasterTags, $RemasterTitle);
                                ?>
                            </div>
                            <div><?= Lang::get('upload', 'editions') ?>:
                                <?
                                $RemasterTags = ['director_s_cut', 'extended_edition', 'rifftrax', 'theatrical_cut', 'uncut', 'unrated'];
                                genRemasterTags($RemasterTags, $RemasterTitle);
                                ?>
                            </div>
                            <div><?= Lang::get('upload', 'features') ?>:
                                <?
                                $RemasterTags = ['2_disc_set', '2_in_1', '2d_3d_edition', '3d_anaglyph', '3d_full_sbs', '3d_half_ou', '3d_half_sbs', '4k_restoration', '4k_remaster', 'remaster', '10_bit', 'dts_x', 'dolby_atmos', 'dolby_vision', 'dual_audio', 'english_dub', 'extras', 'hdr10', 'hdr10plus', 'with_commentary'];
                                genRemasterTags($RemasterTags, $RemasterTitle);
                                ?>
                            </div>
                        </div>
                        <div class="items">
                            <div class="item">
                                <input id="other-button" onclick="$('#other-input').new_toggle()" type="button" value="<?= Lang::get('upload', 'other') ?>">
                                <input id="year-button" onclick="$('#year-input').new_toggle()" type="button" value="<?= Lang::get('upload', 'year') ?>">
                            </div>
                            <div class="item" id="other-input" style="display: none;"><?= Lang::get('upload', 'other') ?>: <input type="text" value="<?= $RemasterCustomTitle ?>" name="remaster_custom_title"> </div>
                            <div class="item" id="year-input" style="display: none;"><?= Lang::get('upload', 'year') ?>: <input type="number" value="<?= $RemasterYear ?>" name="remaster_year"></div>
                        </div>
                    </div>
                    <?
                    if ($RemasterTitle || $RemasterCustomTitle || $RemasterYear) {
                    ?>
                        <script>
                            $('#movie_edition_information_container').new_toggle();
                        </script>
                    <?
                    }
                    if ($RemasterCustomTitle) {
                    ?>
                        <script>
                            $('#other-input').new_toggle();
                        </script>
                    <?
                    }
                    if ($RemasterYear) {
                    ?>
                        <script>
                            $('#year-input').new_toggle();
                        </script>
                    <?
                    }
                    ?>
                </td>
            </tr>
        </div>
        </td>
        </tr>

        <tr class="specifications section_tr">
            <td class="label"><?= Lang::get('upload', 'movie_source') ?><span class="important_text">*</span>:</td>
            <td class="error-container select-input">
                <select id="source" name="source">
                    <option value=""><?= Lang::get('upload', 'auto_detect') ?></option>
                    <?
                    $SourceOther = null;
                    if (!in_array($TorrentSource, $this->Sources)) {
                        $SourceOther = $TorrentSource;
                    }
                    foreach (Misc::display_array($this->Sources) as $Source) {
                        echo "\t\t\t\t\t\t<option value=\"$Source\"";
                        if ($Source == $TorrentSource) {
                            echo ' selected="selected"';
                        } else if ($Source == 'Other' && $SourceOther) {
                            echo ' selected="selected"';
                        }
                        echo ">$Source</option>\n";
                        // <option value="$Source" selected="selected">$Source</option>
                    }
                    ?>
                </select>
                <input type="text" class="hidden" name="source_other" value="<?= !in_array($TorrentSource, $this->Sources) ? $TorrentSource : '' ?>" />
                <span id="source_warning" class="important_text"></span>
            </td>
        </tr>
        <tr id="processing-container" class="<?= $this->NewTorrent || in_array($TorrentSource, ['HDTV', 'WEB', "TV"]) ? 'hidden' : '' ?> specifications">
            <td class="label"><?= Lang::get('upload', 'movie_processing') ?><span class="important_text">*</span>:</td>
            <td class="error-container">
                <select id="processing" name="processing">
                    <?
                    $SelectedProcessing = $TorrentProcessing;
                    if ($TorrentProcessing && !in_array($TorrentProcessing, $this->Processings)) {
                        $SelectedProcessing = 'Untouched';
                    }

                    foreach (Misc::display_array($this->Processings) as $Processing) {
                        echo "\t\t\t\t\t\t<option value=\"$Processing\"";
                        if ($Processing == $SelectedProcessing) {
                            echo ' selected="selected"';
                        }
                        echo ">$Processing</option>\n";
                    }
                    ?>
                </select>
                <select class="<?= in_array($TorrentSource, ['Blu-ray', 'DVD']) && $SelectedProcessing == 'Untouched' ? '' : 'hidden' ?>" name="processing_other">
                    <option value=''><?= Lang::get('upload', 'auto_detect') ?></option>
                    <option class="bd <?= $TorrentSource == 'Blu-ray' ? '' : 'hidden' ?>" value='BD25' <?= $TorrentProcessing == 'BD25' ? 'selected="selected"' : '' ?>>BD25</option>
                    <option class="bd <?= $TorrentSource == 'Blu-ray' ? '' : 'hidden' ?>" value='BD50' <?= $TorrentProcessing == 'BD50' ? 'selected="selected"' : '' ?>>BD50</option>
                    <option class="bd <?= $TorrentSource == 'Blu-ray' ? '' : 'hidden' ?>" value='BD66' <?= $TorrentProcessing == 'BD66' ? 'selected="selected"' : '' ?>>BD66</option>
                    <option class="bd <?= $TorrentSource == 'Blu-ray' ? '' : 'hidden' ?>" value='BD100' <?= $TorrentProcessing == 'BD100' ? 'selected="selected"' : '' ?>>BD100</option>
                    <option class="dvd <?= $TorrentSource == 'DVD' ? '' : 'hidden' ?>" value='DVD5' <?= $TorrentProcessing == 'DVD5' ? 'selected="selected"' : '' ?>>DVD5</option>
                    <option class="dvd <?= $TorrentSource == 'DVD' ? '' : 'hidden' ?>" value='DVD9' <?= $TorrentProcessing == 'DVD9' ? 'selected="selected"' : '' ?>>DVD9</option>
                </select>
            </td>
        </tr>

        <tr class="specifications">
            <td class="label"><?= Lang::get('upload', 'movie_codec') ?><span class="important_text">*</span>:</td>
            <td class="error-container select-input">
                <select id="codec" name="codec">
                    <option value=''><?= Lang::get('upload', 'auto_detect') ?></option>
                    <?
                    $CodecOther = null;
                    if (!in_array($TorrentCodec, $this->Codecs)) {
                        $CodecOther = $TorrentCodec;
                    }
                    foreach (Misc::display_array($this->Codecs) as $Codec) {
                        echo "\t\t\t\t\t\t<option value=\"$Codec\"";
                        if ($Codec == $TorrentCodec) {
                            echo ' selected="selected"';
                        } else if ($Codec == 'Other' && $CodecOther) {
                            echo ' selected="selected"';
                        }
                        echo ">$Codec</option>\n";
                        // <option value="$Codec" selected="selected">$Codec</option>
                    }
                    ?>
                </select>
                <input type="text" class="hidden" name="codec_other" value="<?= !in_array($TorrentCodec, $this->Codecs) ? $TorrentCodec : '' ?>" />
                <span id="codex_warning" class="important_text"></span></ </td>
        </tr>
        <tr class="specifications">
            <td class="label"><?= Lang::get('upload', 'movie_resolution') ?><span class="important_text">*</span>:</td>
            <td class="error-container select-input">
                <select id="resolution" name="resolution">
                    <option value=""><?= Lang::get('upload', 'auto_detect') ?></option>
                    <?
                    $resolution = $TorrentResolution;
                    $resolution_width = '';
                    $resolution_height = '';
                    if ($resolution && !in_array($resolution, $this->Resolutions)) {
                        $resolution = "Other";
                        list($resolution_width, $resolution_height) = explode('&times;', $Torrent['Resolution']);
                    }
                    foreach (Misc::display_array($this->Resolutions) as $Resolution) {
                        echo "\t\t\t\t\t\t<option value=\"$Resolution\"";
                        if ($Resolution == $resolution) {
                            echo ' selected="selected"';
                        }
                        echo ">$Resolution</option>\n";
                        // <option value="$Resolution" selected="selected">$Resolution</option>
                    }
                    ?>
                </select>
                <span class="hidden">
                    <input type="number" id="resolution_width" name="resolution_width" value="<?= $resolution_width ?>">
                    <span>×</span>
                    <input type="number" id="resolution_height" name="resolution_height" , value="<?= $resolution_height ?>">
                </span>
                <span id="resolution_warning" class="important_text"></span>
            </td>
        </tr>
        <tr class="specifications">
            <td class="label"><?= Lang::get('upload', 'movie_container') ?><span class="important_text">*</span>:</td>
            <td class="error-container select-input">
                <select id="container" name="container">
                    <option value=""><?= Lang::get('upload', 'auto_detect') ?></option>
                    <?
                    $ContainerOther = null;
                    if (!in_array($TorrentContainer, $this->Containers)) {
                        $ContainerOther = $TorrentContainer;
                    }
                    foreach (Misc::display_array($this->Containers) as $Container) {
                        echo "\t\t\t\t\t\t<option value=\"$Container\"";
                        if ($Container == $TorrentContainer) {
                            echo ' selected="selected"';
                        } else if ($Container == 'Other' && $ContainerOther) {
                            echo ' selected="selected"';
                        }
                        echo ">$Container</option>\n";
                        // <option value="$Container" selected="selected">$Container</option>
                    }
                    ?>
                </select>
                <input type="text" class="hidden" name="container_other" value="<?= !in_array($TorrentContainer, $this->Containers) ? $TorrentContainer : '' ?>" />
                <span id="container_warning" class="important_text"></span>
            </td>
        </tr>
        <?
                if (check_perms("users_mod") && !$this->NewTorrent) {
        ?>
            <tr>
                <td class="label"><?= Lang::get('upload', 'staff_note') ?>:</td>
                <td>
                    <textarea name="staff_note" id="staff_note"><?= $Note ?></textarea>
                </td>
            </tr>
        <?
                }
        ?>
        <tr class="toolbar">
            <td></td>
            <td>
                <div class="actions">
                    <a id="add-mediainfo" href="#" class="brackets">+</a>
                    <a id="remove-mediainfo" href="#" class="brackets">&minus;</a>
                </div>
            </td>
        </tr>
        <tr id="mediainfo">
            <td class="label">MediaInfo/BDInfo<span class="important_text">*</span>:</td>
            <td class="items">
                <? if ($this->NewTorrent) {
                    $GroupClass = "group1";
                } else {
                    $GroupClass = "group0";
                } ?>
                <?
                if ($MediaInfos) {
                    foreach ($MediaInfos as $MediaInfo) {
                ?>
                        <div class="item error-container">
                            <div class="hidden">
                                <div class="bbcode-preview-html <?= $GroupClass ?>"></div>
                            </div>
                            <div>
                                <textarea class="bbcode-preview-text <?= $GroupClass ?>" name="mediainfo[]" data-type="mediainfo" placeholder="<?= Lang::get('upload', 'mediainfo_bdinfo_placeholder') ?>"><?= $MediaInfo ?></textarea>
                            </div>
                        </div>
                        <p class="upload_form_note"><?= Lang::get('upload', 'mediainfo_bdinfo_note') ?></p>
                    <?
                    }
                } else {
                    ?>
                    <div class="item error-container">
                        <div class="hidden">
                            <div class="bbcode-preview-html <?= $GroupClass ?>"></div>
                        </div>
                        <div>
                            <textarea class="bbcode-preview-text <?= $GroupClass ?>" name="mediainfo[]" data-type="mediainfo" placeholder="<?= Lang::get('upload', 'mediainfo_bdinfo_placeholder') ?>"></textarea>
                        </div>
                    </div>
                <?
                }
                ?>
            </td>
        </tr>

        <tr class="toolbar bbcode-editor">
            <td></td>
            <td>
                <div>
                    <div class="bbcode-toolbar">
                        <!--<span data-cmd="mediainfo"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" width="16" height="16"><path d="M18 3v2h-2V3H8v2H6V3H4v18h2v-2h2v2h8v-2h2v2h2V3h-2zM8 17H6v-2h2v2zm0-4H6v-2h2v2zm0-4H6V7h2v2zm10 8h-2v-2h2v2zm0-4h-2v-2h2v2zm0-4h-2V7h2v2z"></path></svg></span>-->
                        <span data-cmd="bold"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M13.5,15.5H10V12.5H13.5A1.5,1.5 0 0,1 15,14A1.5,1.5 0 0,1 13.5,15.5M10,6.5H13A1.5,1.5 0 0,1 14.5,8A1.5,1.5 0 0,1 13,9.5H10M15.6,10.79C16.57,10.11 17.25,9 17.25,8C17.25,5.74 15.5,4 13.25,4H7V18H14.04C16.14,18 17.75,16.3 17.75,14.21C17.75,12.69 16.89,11.39 15.6,10.79Z"></path>
                            </svg></span>
                        <span data-cmd="italic"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M10,4V7H12.21L8.79,15H6V18H14V15H11.79L15.21,7H18V4H10Z"></path>
                            </svg></span>
                        <span data-cmd="underline"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M5,21H19V19H5V21M12,17A6,6 0 0,0 18,11V3H15.5V11A3.5,3.5 0 0,1 12,14.5A3.5,3.5 0 0,1 8.5,11V3H6V11A6,6 0 0,0 12,17Z"></path>
                            </svg></span>
                        <span data-cmd="strikethrough"><svg t="1621932438542" class="icon" viewBox="0 0 1024 1024" version="1.1" xmlns="http://www.w3.org/2000/svg" p-id="2298" width="200" height="200">
                                <path d="M160.788521 761.54345" p-id="2299"></path>
                                <path d="M863.765087 510.780219 678.771793 510.780219c-45.856397-32.428573-104.777345-50.226938-166.771281-50.226938-81.672114 0-150.684908-46.006823-150.684908-100.453876s69.012793-100.458993 150.684908-100.458993c81.670068 0 150.681838 46.01194 150.681838 100.458993l100.457969 0c0-55.626925-28.782533-109.123326-78.932724-146.742109-46.587038-34.93465-107.739816-54.17076-172.207084-54.17076-64.466244 0-125.623116 19.23611-172.207084 54.17076-50.15326 37.619806-78.932724 91.116207-78.932724 146.742109 0 55.626925 28.751834 109.119233 78.932724 146.739039 1.786693 1.339508 3.601015 2.648316 5.429663 3.940752L160.788521 510.779196l0 50.232055 351.21199 0c81.670068 0 150.681838 46.007847 150.681838 100.4549 0 54.44603-69.01177 100.45183-150.681838 100.45183-81.672114 0-150.684908-46.0058-150.684908-100.45183l-100.4549 0c0 55.625902 28.751834 109.117187 78.932724 146.742109 46.583968 34.930557 107.74084 54.17076 172.207084 54.17076 64.467267 0 125.620046-19.240203 172.207084-54.17076 50.15019-37.625946 78.932724-91.116207 78.932724-146.742109 0-35.554774-11.776208-70.230528-33.357735-100.4549l133.981481 0L863.764064 510.780219z" p-id="2300" data-spm-anchor-id="a313x.7781069.0.i0" class="selected"></path>
                            </svg></span>
                        <span data-cmd="image"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M8.5,13.5L11,16.5L14.5,12L19,18H5M21,19V5C21,3.89 20.1,3 19,3H5A2,2 0 0,0 3,5V19A2,2 0 0,0 5,21H19A2,2 0 0,0 21,19Z"></path>
                            </svg></span>
                        <!--<span data-cmd="video"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M10,16.5V7.5L16,12M20,4.4C19.4,4.2 15.7,4 12,4C8.3,4 4.6,4.19 4,4.38C2.44,4.9 2,8.4 2,12C2,15.59 2.44,19.1 4,19.61C4.6,19.81 8.3,20 12,20C15.7,20 19.4,19.81 20,19.61C21.56,19.1 22,15.59 22,12C22,8.4 21.56,4.91 20,4.4Z"></path></svg></span>-->
                        <!--<span data-cmd="link"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M16,6H13V7.9H16C18.26,7.9 20.1,9.73 20.1,12A4.1,4.1 0 0,1 16,16.1H13V18H16A6,6 0 0,0 22,12C22,8.68 19.31,6 16,6M3.9,12C3.9,9.73 5.74,7.9 8,7.9H11V6H8A6,6 0 0,0 2,12A6,6 0 0,0 8,18H11V16.1H8C5.74,16.1 3.9,14.26 3.9,12M8,13H16V11H8V13Z"></path></svg></span>-->
                        <!--<span data-cmd="unorderedList"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M7,5H21V7H7V5M7,13V11H21V13H7M4,4.5A1.5,1.5 0 0,1 5.5,6A1.5,1.5 0 0,1 4,7.5A1.5,1.5 0 0,1 2.5,6A1.5,1.5 0 0,1 4,4.5M4,10.5A1.5,1.5 0 0,1 5.5,12A1.5,1.5 0 0,1 4,13.5A1.5,1.5 0 0,1 2.5,12A1.5,1.5 0 0,1 4,10.5M7,19V17H21V19H7M4,16.5A1.5,1.5 0 0,1 5.5,18A1.5,1.5 0 0,1 4,19.5A1.5,1.5 0 0,1 2.5,18A1.5,1.5 0 0,1 4,16.5Z"></path></svg></span>-->
                        <!--<span data-cmd="orderedList"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M7,13H21V11H7M7,19H21V17H7M7,7H21V5H7M2,11H3.8L2,13.1V14H5V13H3.2L5,10.9V10H2M3,8H4V4H2V5H3M2,17H4V17.5H3V18.5H4V19H2V20H5V16H2V17Z"></path></svg></span>-->
                        <span data-cmd="alignLeft"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M3,3H21V5H3V3M3,7H15V9H3V7M3,11H21V13H3V11M3,15H15V17H3V15M3,19H21V21H3V19Z"></path>
                            </svg></span>
                        <span data-cmd="alignCenter"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M3,3H21V5H3V3M7,7H17V9H7V7M3,11H21V13H3V11M7,15H17V17H7V15M3,19H21V21H3V19Z"></path>
                            </svg></span>
                        <span data-cmd="alignRight"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M3,3H21V5H3V3M9,7H21V9H9V7M3,11H21V13H3V11M9,15H21V17H9V15M3,19H21V21H3V19Z"></path>
                            </svg></span>
                        <span data-cmd="quote"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M14,17H17L19,13V7H13V13H16M6,17H9L11,13V7H5V13H8L6,17Z"></path>
                            </svg></span>
                        <span data-cmd="code"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on">
                                <path d="M8,3A2,2 0 0,0 6,5V9A2,2 0 0,1 4,11H3V13H4A2,2 0 0,1 6,15V19A2,2 0 0,0 8,21H10V19H8V14A2,2 0 0,0 6,12A2,2 0 0,0 8,10V5H10V3M16,3A2,2 0 0,1 18,5V9A2,2 0 0,0 20,11H21V13H20A2,2 0 0,0 18,15V19A2,2 0 0,1 16,21H14V19H16V14A2,2 0 0,1 18,12A2,2 0 0,1 16,10V5H14V3H16Z"></path>
                            </svg></span>
                        <!--<span data-cmd="emoji"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M12,17.5C14.33,17.5 16.3,16.04 17.11,14H6.89C7.69,16.04 9.67,17.5 12,17.5M8.5,11A1.5,1.5 0 0,0 10,9.5A1.5,1.5 0 0,0 8.5,8A1.5,1.5 0 0,0 7,9.5A1.5,1.5 0 0,0 8.5,11M15.5,11A1.5,1.5 0 0,0 17,9.5A1.5,1.5 0 0,0 15.5,8A1.5,1.5 0 0,0 14,9.5A1.5,1.5 0 0,0 15.5,11M12,20A8,8 0 0,1 4,12A8,8 0 0,1 12,4A8,8 0 0,1 20,12A8,8 0 0,1 12,20M12,2C6.47,2 2,6.5 2,12A10,10 0 0,0 12,22A10,10 0 0,0 22,12A10,10 0 0,0 12,2Z"></path></svg></span>-->
                        <!--<span data-cmd="color"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M9.62,12L12,5.67L14.37,12M11,3L5.5,17H7.75L8.87,14H15.12L16.25,17H18.5L13,3H11Z"></path><path class="sce-color" d="M0,24H24V20H0V24Z" style="fill: inherit;"></path></svg></span>-->
                        <!--<span data-cmd="size"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M3,12H6V19H9V12H12V9H3M9,4V7H14V19H17V7H22V4H9Z"></path></svg></span>-->
                        <!--<span data-cmd="font"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M17,8H20V20H21V21H17V20H18V17H14L12.5,20H14V21H10V20H11L17,8M18,9L14.5,16H18V9M5,3H10C11.11,3 12,3.89 12,5V16H9V11H6V16H3V5C3,3.89 3.89,3 5,3M6,5V9H9V5H6Z"></path></svg></span>-->
                        <!--<span data-cmd="source"><svg xmlns="http://www.w3.org/2000/svg" viewBox="1 1 22 22" unselectable="on"><path d="M14.6,16.6L19.2,12L14.6,7.4L16,6L22,12L16,18L14.6,16.6M9.4,16.6L4.8,12L9.4,7.4L8,6L2,12L8,18L9.4,16.6Z"></path></svg></span>-->
                        <span data-cmd="comparison">
                            <svg width="992px" height="897px" viewBox="0 0 992 897" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                <path d="M50.1384061,896.855556 C76.6384061,896.855556 103.138406,896.855556 129.538406,896.855556 C162.738406,896.855556 195.938406,896.855556 229.138406,896.855556 C254.038406,896.855556 278.938406,896.855556 303.838406,896.855556 C331.638406,896.855556 359.538406,896.855556 387.338406,896.855556 C422.538406,896.855556 457.738406,896.855556 492.938406,896.855556 C516.338406,896.855556 539.838406,896.855556 563.238406,896.855556 C592.638406,896.855556 622.138406,896.855556 651.538406,896.855556 C684.738406,896.855556 717.838406,896.855556 751.038406,896.855556 C775.538406,896.855556 800.038406,896.855556 824.438406,896.855556 C860.138406,896.855556 895.938406,896.855556 931.638406,896.855556 C934.938406,896.855556 938.238406,896.855556 941.638406,896.855556 C954.438406,896.855556 967.938406,891.255556 977.038406,882.255556 C985.738406,873.555556 992.238406,859.355556 991.638406,846.855556 C991.038406,833.955556 986.838406,820.555556 977.038406,811.455556 C967.238406,802.455556 955.238406,796.855556 941.638406,796.855556 C908.938406,796.855556 876.238406,796.855556 843.638406,796.855556 C822.738406,796.855556 801.838406,796.855556 780.838406,796.855556 C741.938406,796.855556 702.938406,796.855556 664.038406,796.855556 C638.938406,796.855556 613.838406,796.855556 588.638406,796.855556 C566.238406,796.855556 543.838406,796.855556 521.538406,796.855556 C481.938406,796.855556 442.438406,796.855556 402.838406,796.855556 C377.738406,796.855556 352.738406,796.855556 327.638406,796.855556 C304.738406,796.855556 281.738406,796.855556 258.838406,796.855556 C219.938406,796.855556 180.938406,796.855556 142.038406,796.855556 C116.038406,796.855556 90.0384061,796.855556 64.1384061,796.855556 C59.4384061,796.855556 54.7384061,796.855556 50.0384061,796.855556 C37.2384061,796.855556 23.7384061,802.455556 14.6384061,811.455556 C5.93840614,820.155556 -0.561593858,834.355556 0.0384061424,846.855556 C0.638406142,859.755556 4.83840614,873.155556 14.6384061,882.255556 C24.6384061,891.155556 36.6384061,896.855556 50.1384061,896.855556 L50.1384061,896.855556 Z" id="Path"></path>
                                <path d="M369.438406,471.055556 C369.438406,483.455556 369.438406,495.855556 369.438406,508.355556 C369.438406,538.155556 369.438406,568.055556 369.438406,597.855556 C369.438406,633.955556 369.438406,670.155556 369.438406,706.255556 C369.438406,737.555556 369.438406,768.755556 369.438406,800.055556 C369.438406,815.355556 369.138406,830.655556 369.438406,845.855556 C369.438406,846.055556 369.438406,846.255556 369.438406,846.455556 C386.138406,829.755556 402.738406,813.155556 419.438406,796.455556 C384.238406,796.455556 349.138406,796.455556 313.938406,796.455556 C257.938406,796.455556 201.838406,796.455556 145.838406,796.455556 C132.938406,796.455556 120.138406,796.455556 107.238406,796.455556 C123.938406,813.155556 140.538406,829.755556 157.238406,846.455556 C157.238406,833.955556 157.238406,821.355556 157.238406,808.855556 C157.238406,778.755556 157.238406,748.755556 157.238406,718.655556 C157.238406,682.355556 157.238406,646.055556 157.238406,609.755556 C157.238406,578.555556 157.238406,547.255556 157.238406,516.055556 C157.238406,501.155556 156.838406,486.255556 157.238406,471.455556 C157.238406,470.355556 157.338406,469.255556 157.438406,468.155556 C156.838406,472.555556 156.238406,477.055556 155.638406,481.455556 C155.938406,480.055556 156.238406,478.855556 156.638406,477.555556 C154.938406,481.555556 153.238406,485.555556 151.638406,489.455556 C152.338406,487.755556 153.138406,486.355556 154.138406,484.855556 C151.538406,488.255556 148.938406,491.555556 146.338406,494.955556 C147.438406,493.555556 148.638406,492.355556 150.038406,491.255556 C146.638406,493.855556 143.338406,496.455556 139.938406,499.055556 C141.438406,498.055556 142.838406,497.255556 144.538406,496.555556 C140.538406,498.255556 136.538406,499.955556 132.638406,501.555556 C133.938406,501.155556 135.138406,500.755556 136.538406,500.555556 C132.138406,501.155556 127.638406,501.755556 123.238406,502.355556 C132.938406,501.255556 143.138406,502.155556 152.938406,502.155556 C174.838406,502.155556 196.738406,502.155556 218.738406,502.155556 C268.338406,502.155556 318.038406,502.155556 367.638406,502.155556 C379.438406,502.155556 391.738406,501.055556 403.538406,502.355556 C399.138406,501.755556 394.638406,501.155556 390.238406,500.555556 C391.638406,500.855556 392.838406,501.155556 394.138406,501.555556 C390.138406,499.855556 386.138406,498.155556 382.238406,496.555556 C383.938406,497.255556 385.338406,498.055556 386.838406,499.055556 C383.438406,496.455556 380.138406,493.855556 376.738406,491.255556 C378.138406,492.355556 379.338406,493.555556 380.438406,494.955556 C377.838406,491.555556 375.238406,488.255556 372.638406,484.855556 C373.638406,486.355556 374.438406,487.755556 375.138406,489.455556 C373.438406,485.455556 371.738406,481.455556 370.138406,477.555556 C370.538406,478.855556 370.938406,480.055556 371.138406,481.455556 C370.538406,477.055556 369.938406,472.555556 369.338406,468.155556 C369.438406,469.255556 369.438406,470.155556 369.438406,471.055556 C369.938406,484.155556 374.638406,497.055556 384.038406,506.455556 C392.738406,515.155556 406.938406,521.655556 419.438406,521.055556 C432.338406,520.455556 445.738406,516.255556 454.838406,506.455556 C463.638406,496.855556 469.938406,484.455556 469.438406,471.055556 C468.738406,452.955556 462.638406,437.355556 450.638406,423.955556 C446.438406,419.255556 441.038406,414.955556 435.638406,411.855556 C429.838406,408.555556 423.338406,405.655556 416.838406,404.355556 C410.738406,403.155556 404.638406,402.355556 398.438406,402.355556 C391.638406,402.355556 384.838406,402.355556 378.038406,402.355556 C350.738406,402.355556 323.338406,402.355556 296.038406,402.355556 C263.338406,402.355556 230.638406,402.355556 198.038406,402.355556 C176.138406,402.355556 154.338406,402.355556 132.438406,402.355556 C130.538406,402.355556 128.638406,402.355556 126.838406,402.355556 C108.738406,402.555556 92.3384061,409.055556 78.9384061,421.155556 C63.6384061,434.955556 57.3384061,455.155556 57.3384061,475.255556 C57.3384061,492.155556 57.3384061,509.155556 57.3384061,526.055556 C57.3384061,555.455556 57.3384061,584.855556 57.3384061,614.255556 C57.3384061,647.355556 57.3384061,680.455556 57.3384061,713.555556 C57.3384061,742.055556 57.3384061,770.555556 57.3384061,799.055556 C57.3384061,814.155556 57.3384061,829.355556 57.3384061,844.455556 C57.3384061,845.155556 57.3384061,845.855556 57.3384061,846.555556 C57.3384061,873.555556 80.2384061,896.555556 107.338406,896.555556 C142.538406,896.555556 177.638406,896.555556 212.838406,896.555556 C268.838406,896.555556 324.938406,896.555556 380.938406,896.555556 C393.838406,896.555556 406.638406,896.555556 419.538406,896.555556 C446.538406,896.555556 469.538406,873.655556 469.538406,846.555556 C469.538406,834.155556 469.538406,821.755556 469.538406,809.255556 C469.538406,779.455556 469.538406,749.555556 469.538406,719.755556 C469.538406,683.655556 469.538406,647.455556 469.538406,611.355556 C469.538406,580.055556 469.538406,548.855556 469.538406,517.555556 C469.538406,502.255556 469.738406,486.955556 469.538406,471.755556 C469.538406,471.555556 469.538406,471.355556 469.538406,471.155556 C469.538406,458.355556 463.938406,444.855556 454.938406,435.755556 C446.238406,427.055556 432.038406,420.555556 419.538406,421.155556 C406.638406,421.755556 393.238406,425.955556 384.138406,435.755556 C375.138406,445.455556 369.438406,457.555556 369.438406,471.055556 Z" id="Path"></path>
                                <path d="M601.538406,270.055556 C601.538406,289.155556 601.538406,308.355556 601.538406,327.455556 C601.538406,373.455556 601.538406,419.355556 601.538406,465.355556 C601.538406,520.955556 601.538406,576.455556 601.538406,632.055556 C601.538406,679.955556 601.538406,727.755556 601.538406,775.655556 C601.538406,798.955556 601.238406,822.255556 601.538406,845.555556 C601.538406,845.855556 601.538406,846.255556 601.538406,846.555556 C618.238406,829.855556 634.838406,813.255556 651.538406,796.555556 C625.438406,796.555556 599.338406,796.555556 573.338406,796.555556 C531.738406,796.555556 490.038406,796.555556 448.438406,796.555556 C438.838406,796.555556 429.138406,796.555556 419.538406,796.555556 C436.238406,813.255556 452.838406,829.855556 469.538406,846.555556 C469.538406,828.755556 469.538406,810.855556 469.538406,793.055556 C469.538406,753.155556 469.538406,713.255556 469.538406,673.355556 C469.538406,631.255556 469.538406,589.155556 469.538406,547.155556 C469.538406,522.755556 469.538406,498.455556 469.538406,474.055556 C469.538406,467.255556 469.038406,460.455556 467.338406,453.755556 C459.538406,422.655556 430.338406,402.555556 399.038406,402.455556 C380.838406,402.355556 362.538406,402.455556 344.338406,402.455556 C342.638406,402.455556 340.938406,402.455556 339.238406,402.455556 C355.938406,419.155556 372.538406,435.755556 389.238406,452.455556 C389.238406,431.755556 389.238406,411.055556 389.238406,390.255556 C389.238406,357.455556 389.238406,324.655556 389.238406,291.855556 C389.238406,283.755556 388.538406,275.355556 389.438406,267.355556 C388.838406,271.755556 388.238406,276.255556 387.638406,280.655556 C387.938406,279.255556 388.238406,278.055556 388.638406,276.755556 C386.938406,280.755556 385.238406,284.755556 383.638406,288.655556 C384.338406,286.955556 385.138406,285.555556 386.138406,284.055556 C383.538406,287.455556 380.938406,290.755556 378.338406,294.155556 C379.438406,292.755556 380.638406,291.555556 382.038406,290.455556 C378.638406,293.055556 375.338406,295.655556 371.938406,298.255556 C373.438406,297.255556 374.838406,296.455556 376.538406,295.755556 C372.538406,297.455556 368.538406,299.155556 364.638406,300.755556 C365.938406,300.355556 367.138406,299.955556 368.538406,299.755556 C364.138406,300.355556 359.638406,300.955556 355.238406,301.555556 C364.938406,300.455556 375.238406,301.355556 384.938406,301.355556 C406.838406,301.355556 428.838406,301.355556 450.738406,301.355556 C500.438406,301.355556 550.138406,301.355556 599.738406,301.355556 C611.538406,301.355556 623.938406,300.255556 635.638406,301.555556 C631.238406,300.955556 626.738406,300.355556 622.338406,299.755556 C623.738406,300.055556 624.938406,300.355556 626.238406,300.755556 C622.238406,299.055556 618.238406,297.355556 614.338406,295.755556 C616.038406,296.455556 617.438406,297.255556 618.938406,298.255556 C615.538406,295.655556 612.238406,293.055556 608.838406,290.455556 C610.238406,291.555556 611.438406,292.755556 612.538406,294.155556 C609.938406,290.755556 607.338406,287.455556 604.738406,284.055556 C605.738406,285.555556 606.538406,286.955556 607.238406,288.655556 C605.538406,284.655556 603.838406,280.655556 602.238406,276.755556 C602.638406,278.055556 603.038406,279.255556 603.238406,280.655556 C602.638406,276.255556 602.038406,271.755556 601.438406,267.355556 C601.438406,268.155556 601.538406,269.155556 601.538406,270.055556 C602.038406,283.155556 606.738406,296.055556 616.138406,305.455556 C624.838406,314.155556 639.038406,320.655556 651.538406,320.055556 C664.438406,319.455556 677.838406,315.255556 686.938406,305.455556 C695.738406,295.855556 702.038406,283.455556 701.538406,270.055556 C700.638406,246.055556 689.138406,223.755556 668.238406,211.155556 C657.338406,204.555556 645.138406,201.655556 632.438406,201.355556 C629.538406,201.255556 626.638406,201.355556 623.738406,201.355556 C608.838406,201.355556 594.038406,201.355556 579.138406,201.355556 C533.138406,201.355556 487.238406,201.355556 441.238406,201.355556 C413.538406,201.355556 385.738406,200.555556 358.038406,201.355556 C339.938406,201.855556 324.938406,208.255556 311.238406,219.755556 C305.438406,224.655556 300.338406,231.355556 297.138406,238.155556 C291.838406,249.455556 289.538406,257.255556 289.038406,270.155556 C288.938406,273.455556 289.038406,276.855556 289.038406,280.155556 C289.038406,298.655556 289.038406,317.155556 289.038406,335.655556 C289.038406,373.855556 289.038406,411.955556 289.038406,450.155556 C289.038406,450.955556 289.038406,451.655556 289.038406,452.455556 C289.038406,479.455556 311.938406,502.455556 339.038406,502.455556 C349.638406,502.455556 360.238406,502.455556 370.838406,502.455556 C381.538406,502.455556 392.738406,501.455556 403.438406,502.655556 C399.038406,502.055556 394.538406,501.455556 390.138406,500.855556 C391.538406,501.155556 392.738406,501.455556 394.038406,501.855556 C390.038406,500.155556 386.038406,498.455556 382.138406,496.855556 C383.838406,497.555556 385.238406,498.355556 386.738406,499.355556 C383.338406,496.755556 380.038406,494.155556 376.638406,491.555556 C378.038406,492.655556 379.238406,493.855556 380.338406,495.255556 C377.738406,491.855556 375.138406,488.555556 372.538406,485.155556 C373.538406,486.655556 374.338406,488.055556 375.038406,489.755556 C373.338406,485.755556 371.638406,481.755556 370.038406,477.855556 C370.438406,479.155556 370.838406,480.355556 371.038406,481.755556 C370.438406,477.355556 369.838406,472.855556 369.238406,468.455556 C369.638406,472.455556 369.438406,476.555556 369.438406,480.555556 C369.438406,489.555556 369.438406,498.555556 369.438406,507.555556 C369.438406,537.355556 369.438406,567.255556 369.438406,597.055556 C369.438406,633.355556 369.438406,669.655556 369.438406,705.855556 C369.438406,737.255556 369.438406,768.755556 369.438406,800.155556 C369.438406,815.455556 369.238406,830.655556 369.438406,845.955556 C369.438406,846.155556 369.438406,846.455556 369.438406,846.655556 C369.438406,873.655556 392.338406,896.655556 419.438406,896.655556 C445.538406,896.655556 471.638406,896.655556 497.638406,896.655556 C539.238406,896.655556 580.938406,896.655556 622.538406,896.655556 C632.138406,896.655556 641.838406,896.655556 651.438406,896.655556 C678.438406,896.655556 701.438406,873.755556 701.438406,846.655556 C701.438406,827.555556 701.438406,808.355556 701.438406,789.255556 C701.438406,743.255556 701.438406,697.355556 701.438406,651.355556 C701.438406,595.755556 701.438406,540.255556 701.438406,484.655556 C701.438406,436.755556 701.438406,388.955556 701.438406,341.055556 C701.438406,317.755556 701.738406,294.455556 701.438406,271.155556 C701.438406,270.855556 701.438406,270.455556 701.438406,270.155556 C701.438406,257.355556 695.838406,243.855556 686.838406,234.755556 C678.138406,226.055556 663.938406,219.555556 651.438406,220.155556 C638.538406,220.755556 625.138406,224.955556 616.038406,234.755556 C607.138406,244.455556 601.538406,256.455556 601.538406,270.055556 Z" id="Path"></path>
                                <path d="M833.438406,69.0555556 C833.438406,75.9555556 833.438406,82.9555556 833.438406,89.8555556 C833.438406,108.755556 833.438406,127.655556 833.438406,146.455556 C833.438406,174.355556 833.438406,202.255556 833.438406,230.155556 C833.438406,264.055556 833.438406,297.955556 833.438406,331.855556 C833.438406,369.155556 833.438406,406.455556 833.438406,443.755556 C833.438406,481.355556 833.438406,519.055556 833.438406,556.655556 C833.438406,591.755556 833.438406,626.855556 833.438406,661.955556 C833.438406,691.655556 833.438406,721.255556 833.438406,750.955556 C833.438406,772.255556 833.438406,793.455556 833.438406,814.755556 C833.438406,824.855556 833.338406,835.055556 833.438406,845.155556 C833.438406,845.555556 833.438406,846.055556 833.438406,846.455556 C850.138406,829.755556 866.738406,813.155556 883.438406,796.455556 C857.238406,796.455556 831.038406,796.455556 804.838406,796.455556 C763.338406,796.455556 721.738406,796.455556 680.238406,796.455556 C670.638406,796.455556 661.138406,796.455556 651.538406,796.455556 C668.238406,813.155556 684.838406,829.755556 701.538406,846.455556 C701.538406,822.055556 701.538406,797.555556 701.538406,773.155556 C701.538406,717.255556 701.538406,661.355556 701.538406,605.455556 C701.538406,543.055556 701.538406,480.655556 701.538406,418.155556 C701.538406,375.155556 701.538406,332.155556 701.538406,289.255556 C701.538406,282.355556 701.838406,275.455556 701.438406,268.555556 C700.038406,238.155556 679.438406,209.755556 648.838406,203.155556 C640.338406,201.355556 633.038406,201.155556 624.738406,201.155556 C610.238406,201.155556 595.738406,201.155556 581.238406,201.155556 C577.838406,201.155556 574.438406,201.155556 571.038406,201.155556 C587.738406,217.855556 604.338406,234.455556 621.038406,251.155556 C621.038406,230.455556 621.038406,209.755556 621.038406,188.955556 C621.038406,156.155556 621.038406,123.355556 621.038406,90.5555556 C621.038406,82.4555556 620.338406,74.0555556 621.238406,66.0555556 C620.638406,70.4555556 620.038406,74.9555556 619.438406,79.3555556 C619.738406,77.9555556 620.038406,76.7555556 620.438406,75.4555556 C618.738406,79.4555556 617.038406,83.4555556 615.438406,87.3555556 C616.138406,85.6555556 616.938406,84.2555556 617.938406,82.7555556 C615.338406,86.1555556 612.738406,89.4555556 610.138406,92.8555556 C611.238406,91.4555556 612.438406,90.2555556 613.838406,89.1555556 C610.438406,91.7555556 607.138406,94.3555556 603.738406,96.9555556 C605.238406,95.9555556 606.638406,95.1555556 608.338406,94.4555556 C604.338406,96.1555556 600.338406,97.8555556 596.438406,99.4555556 C597.738406,99.0555556 598.938406,98.6555556 600.338406,98.4555556 C595.938406,99.0555556 591.438406,99.6555556 587.038406,100.255556 C596.738406,99.1555556 606.938406,100.055556 616.738406,100.055556 C638.638406,100.055556 660.638406,100.055556 682.538406,100.055556 C732.238406,100.055556 781.838406,100.055556 831.538406,100.055556 C843.338406,100.055556 855.738406,98.9555556 867.438406,100.255556 C863.038406,99.6555556 858.538406,99.0555556 854.138406,98.4555556 C855.538406,98.7555556 856.738406,99.0555556 858.038406,99.4555556 C854.038406,97.7555556 850.038406,96.0555556 846.138406,94.4555556 C847.838406,95.1555556 849.238406,95.9555556 850.738406,96.9555556 C847.338406,94.3555556 844.038406,91.7555556 840.638406,89.1555556 C842.038406,90.2555556 843.238406,91.4555556 844.338406,92.8555556 C841.738406,89.4555556 839.138406,86.1555556 836.538406,82.7555556 C837.538406,84.2555556 838.338406,85.6555556 839.038406,87.3555556 C837.338406,83.3555556 835.638406,79.3555556 834.038406,75.4555556 C834.438406,76.7555556 834.838406,77.9555556 835.038406,79.3555556 C834.438406,74.9555556 833.838406,70.4555556 833.238406,66.0555556 C833.338406,67.1555556 833.438406,68.0555556 833.438406,69.0555556 C833.938406,82.1555556 838.638406,95.0555556 848.038406,104.455556 C856.738406,113.155556 870.938406,119.655556 883.438406,119.055556 C896.338406,118.455556 909.738406,114.255556 918.838406,104.455556 C927.638406,94.8555556 933.938406,82.4555556 933.438406,69.0555556 C932.538406,45.0555556 921.038406,22.7555556 900.138406,10.1555556 C889.238406,3.55555556 877.038406,0.655555556 864.338406,0.355555556 C861.438406,0.255555556 858.538406,0.355555556 855.638406,0.355555556 C840.738406,0.355555556 825.938406,0.355555556 811.038406,0.355555556 C765.138406,0.355555556 719.138406,0.355555556 673.238406,0.355555556 C645.538406,0.355555556 617.738406,-0.444444444 590.038406,0.355555556 C571.938406,0.855555556 556.938406,7.25555556 543.238406,18.7555556 C537.438406,23.6555556 532.338406,30.3555556 529.138406,37.1555556 C523.838406,48.4555556 521.538406,56.2555556 521.038406,69.1555556 C520.938406,72.4555556 521.038406,75.8555556 521.038406,79.1555556 C521.038406,97.6555556 521.038406,116.155556 521.038406,134.655556 C521.038406,172.855556 521.038406,210.955556 521.038406,249.155556 C521.038406,249.955556 521.038406,250.655556 521.038406,251.455556 C521.038406,278.455556 543.938406,301.455556 571.038406,301.455556 C581.638406,301.455556 592.238406,301.455556 602.838406,301.455556 C613.538406,301.455556 624.738406,300.455556 635.438406,301.655556 C631.038406,301.055556 626.538406,300.455556 622.138406,299.855556 C623.538406,300.155556 624.738406,300.455556 626.038406,300.855556 C622.038406,299.155556 618.038406,297.455556 614.138406,295.855556 C615.838406,296.555556 617.238406,297.355556 618.738406,298.355556 C615.338406,295.755556 612.038406,293.155556 608.638406,290.555556 C610.038406,291.655556 611.238406,292.855556 612.338406,294.255556 C609.738406,290.855556 607.138406,287.555556 604.538406,284.155556 C605.538406,285.655556 606.338406,287.055556 607.038406,288.755556 C605.338406,284.755556 603.638406,280.755556 602.038406,276.855556 C602.438406,278.155556 602.838406,279.355556 603.038406,280.755556 C602.438406,276.355556 601.838406,271.855556 601.238406,267.455556 C601.838406,273.255556 601.438406,279.255556 601.438406,285.155556 C601.438406,299.055556 601.438406,312.855556 601.438406,326.755556 C601.438406,372.655556 601.438406,418.655556 601.438406,464.555556 C601.438406,520.155556 601.438406,575.655556 601.438406,631.255556 C601.438406,679.355556 601.438406,727.455556 601.438406,775.455556 C601.438406,798.855556 601.138406,822.255556 601.438406,845.655556 C601.438406,845.955556 601.438406,846.355556 601.438406,846.655556 C601.438406,873.655556 624.338406,896.655556 651.438406,896.655556 C677.638406,896.655556 703.838406,896.655556 730.038406,896.655556 C771.538406,896.655556 813.138406,896.655556 854.638406,896.655556 C864.238406,896.655556 873.738406,896.655556 883.338406,896.655556 C910.338406,896.655556 933.338406,873.755556 933.338406,846.655556 C933.338406,839.755556 933.338406,832.755556 933.338406,825.855556 C933.338406,806.955556 933.338406,788.055556 933.338406,769.255556 C933.338406,741.355556 933.338406,713.455556 933.338406,685.555556 C933.338406,651.655556 933.338406,617.755556 933.338406,583.855556 C933.338406,546.555556 933.338406,509.255556 933.338406,471.955556 C933.338406,434.355556 933.338406,396.655556 933.338406,359.055556 C933.338406,323.955556 933.338406,288.855556 933.338406,253.755556 C933.338406,224.055556 933.338406,194.455556 933.338406,164.755556 C933.338406,143.455556 933.338406,122.255556 933.338406,100.955556 C933.338406,90.8555556 933.438406,80.6555556 933.338406,70.5555556 C933.338406,70.1555556 933.338406,69.6555556 933.338406,69.2555556 C933.338406,56.4555556 927.738406,42.9555556 918.738406,33.8555556 C910.038406,25.1555556 895.838406,18.6555556 883.338406,19.2555556 C870.438406,19.8555556 857.038406,24.0555556 847.938406,33.8555556 C839.038406,43.4555556 833.438406,55.4555556 833.438406,69.0555556 Z" id="Path"></path>
                            </svg>
                        </span>
                    </div>
            </td>
        </tr>
        <tr id="description-container">
            <td class="label"><?= Lang::get('upload', 'movie_torrent_description') ?><span class="important_text">*</span>:</td>
            <td>
                <div class="error-container">
                    <?php new TEXTAREA_PREVIEW('release_desc', 'release_desc', display_str($TorrentDescription), 60, 8, true, true, false); ?>
                </div>
                <p class="upload_form_note"><?= Lang::get('upload', 'movie_torrent_description_note') ?></p>
            </td>
        </tr>





        <tr class="text_tr">
            <td class="label">
                <?= Lang::get('upload', 'movie_subtitles') ?><span class="important_text">*</span>:
            </td>
            <td>
                <div id="subtitles_container" class="error-container">
                    <div id="type_of_subtitles" class="grid_subtitles radio-group">
                        <div class="subtitle">
                            <input id="mixed_subtitles" type="radio" name="subtitle_type" data-value="mixed-sub" value="1" <?= $SubtitleType == 1 ? 'checked' : '' ?>>
                            <label for="mixed_subtitles"><?= Lang::get('upload', 'mixed_subtitles') ?></label>
                        </div>
                        <div class="subtitle">
                            <input id="hardcoded_subtitles" type="radio" name="subtitle_type" value="2" data-value="hardcoded-sub" <?= $SubtitleType == 2 ? 'checked' : '' ?>>
                            <label for="hardcoded_subtitles"><?= Lang::get('upload', 'hardcode_sub') ?></label>
                        </div>
                        <div class="subtitle">
                            <input id="no_subtitles" type="radio" name="subtitle_type" value="3" data-value="no-sub" <?= $SubtitleType == 3 ? 'checked' : '' ?>>
                            <label for="no_subtitles"><?= Lang::get('upload', 'no_subtitles') ?></label>
                        </div>
                    </div>
                    <div id="other_subtitles" class="<?= in_array($SubtitleType, [1, 2]) ? '' : 'hidden' ?>">
                        <div class="grid_subtitles">
                            <?
                            function genSubcheckboxes($Labels, $Images, $Subtitles) {
                                for ($i = 0; $i < count($Labels); $i++) {
                                    echo '<div class="subtitle"><input id="' . $Labels[$i] . '" type="checkbox" name="subtitles[]" value="' . $Labels[$i] . '"' . (strpos($Subtitles, $Labels[$i]) === false ? "" : "checked=\"checked\"") . '><label for="' . $Labels[$i] . '"><img class="national_flags" src="static/common/flags/' . $Images[$i] . '"> ' . Lang::get('upload', $Labels[$i]) . '</label></div>';
                                }
                            }
                            $Labels = ['chinese_simplified', 'chinese_traditional', 'english', 'japanese', 'korean'];
                            $Images = ['China.png', 'Hong Kong.png', 'United States of America.png', 'Japan.png', 'South Korea.png'];
                            genSubcheckboxes($Labels, $Images, $Subtitles);
                            ?>

                            <?
                            $Labels = ['arabic', 'brazilian_port', 'bulgarian', 'croatian', 'czech', 'danish', 'dutch', 'estonian', 'finnish', 'french', 'german', 'greek', 'hebrew', 'hindi', 'hungarian', 'icelandic', 'indonesian', 'italian', 'latvian', 'lithuanian', 'norwegian', 'persian', 'polish', 'portuguese', 'romanian', 'russian', 'serbian', 'slovak', 'slovenian', 'spanish', 'swedish', 'thai', 'turkish', 'ukrainian', 'vietnamese'];
                            $Images = ['Palestine.png', 'Brazil.png', 'Bulgaria.png', 'Croatia.png', 'Czech Republic.png', 'Denmark.png', 'Netherlands.png', 'Estonia.png', 'Finland.png', 'France.png', 'Germany.png', 'Greece.png', 'Israel.png', 'India.png', 'Hungary.png', 'Iceland.png', 'Indonesia.png', 'Italy.png', 'Latvia.png', 'Lithuania.png', 'Norway.png', 'Iran.png', 'Poland.png', 'Portugal.png', 'Romania.png', 'Russian Federation.png', 'Serbia.png', 'Slovakia.png', 'Slovenia.png', 'Spain.png', 'Sweden.png', 'Thailand.png', 'Turkey.png', 'Ukraine.png', 'Viet Nam.png'];
                            genSubcheckboxes($Labels, $Images, $Subtitles);
                            ?>
                        </div>
                    </div>
                </div>
                <p class="upload_form_note"><?= Lang::get('upload', 'movie_subtitles_note') ?></p>
            </td>
        </tr>
        <?
                if (!$this->NewTorrent && check_perms('users_mod')) {
        ?>
            </td>
            </tr>



            <tr>
                <td class="label"><?= Lang::get('upload', 'custom_trumpable') ?>:</td>
                <td>
                    <textarea style="min-height: auto;" name="custom_trumpable" id="custom_trumpable" cols="60" rows="1"><?= $CustomTrumpable ?></textarea>
                </td>
            </tr>
        <?
                }
                if ($this->NewTorrent) {
        ?>
        <?      } /* if new torrent */ ?>
        <tr id="movie_feature_tr" class="text_tr">
            <td class="label"><?= Lang::get('upload', 'movie_feature') ?>:</td>
            <td><input type="checkbox" id="chinese_dubbed" name="chinese_dubbed" <?= $ChineseDubbed ? "checked" : "" ?> />
                <label for="chinese_dubbed"><?= Lang::get('upload', 'chinese_dubbed_label') ?></label>
                <input type="checkbox" id="special_effects_subtitles" name="special_effects_subtitles" <?= $SpecialSub ? "checked" : "" ?> />
                <label for="special_effects_subtitles"><?= Lang::get('upload', 'special_effects_subtitles_label') ?></label>
                <p class="upload_form_note"><span class="important_text"><?= Lang::get('upload', 'movie_feature_note') ?></span></p>
            </td>
        </tr>
        <?
                if (check_perms("torrents_trumpable")) {
        ?>
            <tr id="trumpable_tr" class="text_tr">
                <td class="label"><?= Lang::get('upload', 'movie_trumpable') ?>:</td>
                <td><input type="checkbox" id="no_sub" name="no_sub" <?= $this->Disabled ?> <?= $NoSub ? "checked" : "" ?> />
                    <label for="no_sub"><?= Lang::get('upload', 'no_sub') ?></label>
                    <input type="checkbox" id="hardcode_sub" name="hardcode_sub" <?= $this->Disabled ?> <?= $HardSub ? "checked" : "" ?> />
                    <label for="hardcode_sub"><?= Lang::get('upload', 'hardcode_sub') ?></label>
                    <input type="checkbox" id="bad_folders" name="bad_folders" <?= $this->Disabled ?> <?= $BadFolders ? "checked" : "" ?> />
                    <label for="bad_folders"><?= Lang::get('upload', 'bad_folders') ?></label>

                </td>
            </tr>
        <?
                }
                if (check_perms('users_mod') || $this->NewTorrent) {
        ?>
            <tr id="marks_tr" class="text_tr">
                <td class="label"><?= Lang::get('upload', 'marks') ?>:</td>
                <td>
                    <input type="checkbox" onchange="AlterOriginal()" id="self_purchase" name="buy" <? if ($Buy) {
                                                                                                        echo 'checked="checked" ';
                                                                                                    } ?> />
                    <label for="self_purchase"><?= Lang::get('upload', 'self_purchase') ?></label>
                    <input type="checkbox" onchange="AlterOriginal()" id="self_rip" name="diy" <? if ($Diy) {
                                                                                                    echo 'checked="checked" ';
                                                                                                } ?> />
                    <label for="self_rip"><?= Lang::get('upload', 'self_rip') ?></label>
                    <input type="checkbox" id="jinzhuan" name="jinzhuan" <? if ($Jinzhuan) {
                                                                                echo 'checked="checked" ';
                                                                            } ?><?= !$Buy && !$Diy && !check_perms("users_mod") ? "disabled" : "" ?> />
                    <label for="jinzhuan"><?= Lang::get('upload', 'jinzhuan') ?></label>
                </td>
            </tr>
        <?
                }
        ?>

        </table>
        </div>
<?
                //  For AJAX requests (e.g. when changing the type from Music to Applications),
                //  we don't need to include all scripts, but we do need to include the code
                //  that generates previews. It will have to be eval'd after an AJAX request.
                if ($_SERVER['SCRIPT_NAME'] === '/ajax.php')
                    TEXTAREA_PREVIEW::JavaScript(false);

                G::$DB->set_query_id($QueryID);
            } //function movie_form
        } //class
?>