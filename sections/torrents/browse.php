<?

use Gazelle\API\Torrent;

include(SERVER_ROOT . '/sections/torrents/functions.php');
include(SERVER_ROOT . '/classes/torrenttable.class.php');
// The "order by x" links on columns headers
function header_link($SortKey, $DefaultWay = 'desc') {
    global $OrderBy, $OrderWay;
    if ($SortKey == $OrderBy) {
        if ($OrderWay == 'desc') {
            $NewWay = 'asc';
        } else {
            $NewWay = 'desc';
        }
    } else {
        $NewWay = $DefaultWay;
    }
    return "torrents.php?order_way=$NewWay&amp;order_by=$SortKey&amp;" . Format::get_url(array('order_way', 'order_by'));
}

if (!empty($_GET['searchstr']) || !empty($_GET['groupname'])) {
    if (!empty($_GET['searchstr'])) {
        $InfoHash = $_GET['searchstr'];
    } else {
        $InfoHash = $_GET['groupname'];
    }

    // Search by infohash
    if ($InfoHash = is_valid_torrenthash($InfoHash)) {
        $InfoHash = db_string(pack('H*', $InfoHash));
        $DB->query("
			SELECT ID, GroupID
			FROM torrents
			WHERE info_hash = '$InfoHash'");
        if ($DB->has_results()) {
            list($ID, $GroupID) = $DB->next_record();
            header("Location: torrents.php?id=$GroupID&torrentid=$ID");
            die();
        }
    }
}

// Setting default search options
if (!empty($_GET['setdefault'])) {
    $UnsetList = array('page', 'setdefault');
    $UnsetRegexp = '/(&|^)(' . implode('|', $UnsetList) . ')=.*?(&|$)/i';

    $DB->query("
		SELECT SiteOptions
		FROM users_info
		WHERE UserID = '" . db_string($LoggedUser['ID']) . "'");
    list($SiteOptions) = $DB->next_record(MYSQLI_NUM, false);
    $SiteOptions = unserialize_array($SiteOptions);
    $SiteOptions = array_merge(Users::default_site_options(), $SiteOptions);

    $SiteOptions['DefaultSearch'] = preg_replace($UnsetRegexp, '', $_SERVER['QUERY_STRING']);
    $DB->query("
		UPDATE users_info
		SET SiteOptions = '" . db_string(serialize($SiteOptions)) . "'
		WHERE UserID = '" . db_string($LoggedUser['ID']) . "'");
    $Cache->begin_transaction("user_info_heavy_$UserID");
    $Cache->update_row(false, array('DefaultSearch' => $SiteOptions['DefaultSearch']));
    $Cache->commit_transaction(0);

    // Clearing default search options
} elseif (!empty($_GET['cleardefault'])) {
    $DB->query("
		SELECT SiteOptions
		FROM users_info
		WHERE UserID = '" . db_string($LoggedUser['ID']) . "'");
    list($SiteOptions) = $DB->next_record(MYSQLI_NUM, false);
    $SiteOptions = unserialize_array($SiteOptions);
    $SiteOptions['DefaultSearch'] = '';
    $DB->query("
		UPDATE users_info
		SET SiteOptions = '" . db_string(serialize($SiteOptions)) . "'
		WHERE UserID = '" . db_string($LoggedUser['ID']) . "'");
    $Cache->begin_transaction("user_info_heavy_$UserID");
    $Cache->update_row(false, array('DefaultSearch' => ''));
    $Cache->commit_transaction(0);

    // Use default search options
} elseif (empty($_SERVER['QUERY_STRING']) || (count($_GET) === 1 && isset($_GET['page']))) {
    if (!empty($LoggedUser['DefaultSearch'])) {
        if (!empty($_GET['page'])) {
            $Page = $_GET['page'];
            parse_str($LoggedUser['DefaultSearch'], $_GET);
            $_GET['page'] = $Page;
        } else {
            parse_str($LoggedUser['DefaultSearch'], $_GET);
        }
    }
}
// Terms were not submitted via the search form
if (isset($_GET['searchsubmit'])) {
    $GroupResults = !empty($_GET['group_results']);
} else {
    $GroupResults = !$LoggedUser['DisableGrouping2'];
}

if (!empty($_GET['order_way']) && $_GET['order_way'] == 'asc') {
    $OrderWay = 'asc';
} else {
    $OrderWay = 'desc';
}

if (empty($_GET['order_by']) || !isset(TorrentSearch::$SortOrders[$_GET['order_by']])) {
    $OrderBy = 'time'; // For header links
} else {
    $OrderBy = $_GET['order_by'];
}

$Page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;
$Search = new TorrentSearch($GroupResults, $OrderBy, $OrderWay, $Page, TORRENTS_PER_PAGE);
$Results = $Search->query($_GET);
$Groups = $Search->get_groups();
$RealNumResults = $NumResults = $Search->record_count();

if (check_perms('site_search_many')) {
    $LastPage = ceil($NumResults / TORRENTS_PER_PAGE);
    $FixSearch = new TorrentSearch($GroupResults, $OrderBy, $OrderWay, $LastPage, TORRENTS_PER_PAGE);
    $FixSearch->query($_GET);
    $RealNumResults = $NumResults = $FixSearch->record_count();
} else {
    $NumResults = min($NumResults, SPHINX_MAX_MATCHES);
}

$HideFilter = isset($LoggedUser['ShowTorFilter']) && $LoggedUser['ShowTorFilter'] == 0;
// This is kinda ugly, but the enormous if paragraph was really hard to read
$AdvancedSearch = !empty($_GET['action']) && $_GET['action'] == 'advanced';
$AdvancedSearch |= !empty($LoggedUser['SearchType']) && (empty($_GET['action']) || $_GET['action'] == 'advanced');
$AdvancedSearch &= check_perms('site_advanced_search');
if ($AdvancedSearch) {
    $Action = 'action=advanced';
    $HideBasic = ' hidden';
    $HideAdvanced = '';
} else {
    $Action = 'action=basic';
    $HideBasic = '';
    $HideAdvanced = ' hidden';
}

View::show_header(Lang::get('torrents', 'header'), 'browse');
//$TimeNow = new DateTime();
//$TimeUntil = new DateTime('2016-12-16 03:50:00');
//$Interval = $TimeUntil->diff($TimeNow);
//$Left = $Interval->format("%i MINS, %s SECONDS");
?>
<script>
    function toggleTorrentSearch(mode) {
        if (mode == 0) {
            var link = $('#ft_toggle').raw();
            $('#ft_container').gtoggle();
            //link.innerHTML = link.textContent == 'Hide' ? 'Show' : 'Hide';
            link.innerHTML = link.textContent == <?= "'" . Lang::get('torrents', 'hide') . "'" ?> ? <?= "'" . Lang::get('torrents', 'show') . "'" ?> : <?= "'" . Lang::get('torrents', 'hide') . "'" ?>;
        }
        if (mode == 'basic') {
            $('.fti_advanced').disable();
            $('.fti_basic').enable();
            $('.ftr_advanced').ghide(true);
            $('.ftr_basic').gshow();
            $('#ft_advanced_link').gshow();
            $('#ft_advanced_text').ghide();
            $('#ft_basic_link').ghide();
            $('#ft_basic_text').gshow();
            $('#ft_type').raw().value = 'basic';
        } else if (mode == 'advanced') {
            $('.fti_advanced').enable();
            $('.fti_basic').disable();
            $('.ftr_advanced').gshow();
            $('.ftr_basic').ghide();
            $('#ft_advanced_link').ghide();
            $('#ft_advanced_text').gshow();
            $('#ft_basic_link').gshow();
            $('#ft_basic_text').ghide();
            $('#ft_type').raw().value = 'advanced';
        }
        return false;
    }
</script>
<div class="thin widethin">
    <!--<div class="alertbar blend" style="font-size: 14pt;">GLOBAL FREELEECH ENDS IN <?= 0 //$Left 
                                                                                        ?></div>-->
    <div class="header">
        <h2><?= Lang::get('global', 'torrents') ?></h2>
    </div>
    <form class="search_form headbody" name="torrents" method="get" action="" onsubmit="$(this).disableUnset();">
        <div class="box filter_torrents">
            <div class="head">
                <strong>
                    <span id="ft_basic_text" class="<?= $HideBasic ?>"><?= Lang::get('torrents', 'base') ?> /</span>
                    <span id="ft_basic_link" class="<?= $HideAdvanced ?>"><a href="#" onclick="return toggleTorrentSearch('basic');"><?= Lang::get('torrents', 'base') ?></a> /</span>
                    <span id="ft_advanced_text" class="<?= $HideAdvanced ?>"><?= Lang::get('torrents', 'advanced') ?></span>
                    <span id="ft_advanced_link" class="<?= $HideBasic ?>"><a href="#" onclick="return toggleTorrentSearch('advanced');"><?= Lang::get('torrents', 'advanced') ?></a></span>
                    <?= Lang::get('torrents', 'search') ?>
                    <a class="tooltip" href="wiki.php?action=article&name=%E9%AB%98%E7%BA%A7%E6%90%9C%E7%B4%A2%E6%8C%87%E5%8D%97" target="_blank" title="<?= Lang::get('torrents', 'guide_of_advanced_search') ?>">[?]</a>
                </strong>
                <span style="float: right;">
                    <!--<a href="#" onclick="return toggleTorrentSearch(0);" id="ft_toggle" class="brackets"><?= $HideFilter ? 'Show' : 'Hide' ?></a>-->

                    <a href="#" onclick="return toggleTorrentSearch(0);" id="ft_toggle" class="brackets"><?= $HideFilter ? Lang::get('global', 'show') : Lang::get('global', 'hide') ?></a>
                </span>
            </div>
            <div id="ft_container" class="pad<?= $HideFilter ? ' hidden' : '' ?>">
                <table class="layout">
                    <tr id="movie_name" class="ftr_advanced<?= $HideAdvanced ?>">
                        <td class="label"><?= Lang::get('global', 'movie_name') ?>:</td>
                        <td colspan="3" class="ft_moviename">
                            <input type="search" spellcheck="false" size="40" name="groupname" placeholder="<?= Lang::get('global', 'movie_name_title') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('moviename') ?>" />
                            <!--<label><input type="checkbox"/><?= Lang::get('global', 'include_all_aliases') ?></label>-->
                        </td>
                    </tr>
                    <tr id="director_year" class="ftr_advanced<?= $HideAdvanced ?>">
                        <td class="label"><?= Lang::get('global', 'artist') ?>:</td>
                        <td class="ft_director">
                            <input type="search" spellcheck="false" size="40" name="artistname" class="inputtext smaller fti_advanced" value="<? Format::form('artistname') ?>" />
                        </td>
                        <td class="label"><?= Lang::get('global', 'year') ?>:</td>
                        <td class="ft_year">
                            <input type="search" spellcheck="false" size="40" name="year" class="inputtext smaller fti_advanced" value="<? Format::form('year') ?>" />
                        </td>
                    </tr>
                    <tr id="language_region" class="ftr_advanced<?= $HideAdvanced ?>">
                        <td class="label"><?= Lang::get('global', 'language_region') ?>:</td>
                        <td class="ft_language">
                            <input type="search" spellcheck="false" size="40" name="language" placeholder="<?= Lang::get('global', 'language') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('language') ?>" />
                        </td>
                        <td class="ft_rigion">
                            <input type="search" spellcheck="false" size="40" name="region" placeholder="<?= Lang::get('global', 'countries_and_regions') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('rigion') ?>" />
                        </td>
                        <td class="ft_subtitle">
                            <input type="search" spellcheck="false" size="40" name="subtitles" placeholder="<?= Lang::get('global', 'subtitle') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('subtitle') ?>" />
                        </td>
                    </tr>

                    <tr id="rating" class="ftr_advanced<?= $HideAdvanced ?>">
                        <td class="label"><?= Lang::get('global', 'rating') ?>:</td>
                        <td class="ft_imdbrating">
                            <input type="search" spellcheck="false" size="40" name="imdbrating" placeholder="<?= Lang::get('global', 'imdb_rating') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('imdbrating') ?>" />
                        </td>
                        <td class="ft_doubanrating">
                            <input type="search" spellcheck="false" size="40" name="doubanrating" placeholder="<?= Lang::get('global', 'douban_rating') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('doubanrating') ?>" />
                        </td>
                        <!-- <td class="ft_gpwrating">
                    <input type="search" spellcheck="false" size="40" name="gpwrating" placeholder="<?= Lang::get('global', 'gpw_rating') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('gpwrating') ?>" />
                </td> -->
                        <td class="ft_rtrating">
                            <input type="search" spellcheck="false" size="40" name="rtrating" placeholder="<?= Lang::get('global', 'rt_rating') ?>" class="inputtext smaller fti_advanced" value="<? Format::form('rtrating') ?>" />
                        </td>
                    </tr>
                    <tr id="catalogue_number_year" class="ftr_advanced<?= $HideAdvanced ?> hidden">
                        <td class="label"><?= Lang::get('torrents', 'catalogue_number') ?>:</td>
                        <td class="ft_cataloguenumber">
                            <input type="search" size="40" name="cataloguenumber" class="inputtext smallest fti_advanced" value="<? Format::form('cataloguenumber') ?>" />
                        </td>
                        <td class="label"><?= Lang::get('torrents', 'ft_year') ?>:</td>
                        <td class="ft_year">
                            <input type="search" name="year" class="inputtext smallest fti_advanced" value="<? Format::form('year') ?>" size="4" />
                        </td>
                    </tr>
                    <tr id="edition_expand" class="ftr_advanced<?= $HideAdvanced ?>  hidden">
                        <td colspan="4" class="center ft_edition_expand"><a href="#" class="brackets" onclick="ToggleEditionRows(); return false;"><?= Lang::get('torrents', 'edition_expand') ?></a></td>
                    </tr>
                    <?
                    if (
                        Format::form('remastertitle', true) == ''
                        && Format::form('remasteryear', true) == ''
                        && Format::form('remasterrecordlabel', true) == ''
                        && Format::form('remastercataloguenumber', true) == ''
                    ) {
                        //$Hidden = ' hidden';
                        $Hidden = '';
                    } else {
                        $Hidden = '';
                    }
                    ?>
                    <tr id="edition_info" class="ftr_advanced<?= $HideAdvanced . $Hidden ?>">
                        <td class="label"><?= Lang::get('global', 'edition_info') ?>:</td>
                        <td colspan="3" class="ft_editioninfo">
                            <input type="search" spellcheck="false" size="40" name="remtitle" class="inputtext smaller fti_advanced" value="<? Format::form('editioninfo') ?>" placeholder="<?= Lang::get('global', 'comma_separated_edition') ?>" />
                        </td>
                    </tr>
                    <tr id="file_list" class="ftr_advanced<?= $HideAdvanced ?>">
                        <td class="label"><?= Lang::get('torrents', 'ft_filelist') ?>:</td>
                        <td colspan="3" class="ft_filelist">
                            <input type="search" spellcheck="false" size="40" name="filelist" class="inputtext fti_advanced" value="<? Format::form('filelist') ?>" />
                        </td>
                    </tr>
                    <tr id="rip_specifics" class="ftr_advanced<?= $HideAdvanced ?>">
                        <td class="label"><?= Lang::get('torrents', 'ft_ripspecifics') ?>:</td>
                        <td class="ft_ripspecifics" colspan="3">
                            <select id="source" name="source" class="ft_source fti_advanced">
                                <option value=""><?= Lang::get('torrents', 'source') ?></option>
                                <? foreach ($Sources as $SourceName) { ?>
                                    <option value="<?= display_str($SourceName); ?>" <? Format::selected('source', $SourceName) ?>><?= display_str($SourceName); ?></option>
                                <?  } ?>
                            </select>

                            <select name="codec" class="ft_codec fti_advanced">
                                <option value=""><?= Lang::get('torrents', 'codec') ?></option>
                                <? foreach ($Codecs as $CodecName) { ?>
                                    <option value="<?= display_str($CodecName); ?>" <? Format::selected('codec', $CodecName) ?>><?= display_str($CodecName); ?></option>
                                <?  } ?>
                            </select>
                            <select name="container" class="ft_container fti_advanced">
                                <option value=""><?= Lang::get('torrents', 'container') ?></option>
                                <? foreach ($Containers as $ContainerName) { ?>
                                    <option value="<?= display_str($ContainerName); ?>" <? Format::selected('container', $ContainerName) ?>><?= display_str($ContainerName); ?></option>
                                <?  } ?>
                            </select>
                            <select name="resolution" class="ft_resolution fti_advanced">
                                <option value=""><?= Lang::get('torrents', 'resolution') ?></option>
                                <? foreach ($Resolutions as $ResolutionName) { ?>
                                    <option value="<?= display_str($ResolutionName); ?>" <? Format::selected('resolution', $ResolutionName) ?>><?= display_str($ResolutionName); ?></option>
                                <?  } ?>
                            </select>
                            <select name="processing" class="ft_resolution fti_advanced">
                                <option value=""><?= Lang::get('torrents', 'processing') ?></option>
                                <? foreach ($Processings as $ProcessingName) { ?>
                                    <option value="<?= display_str($ProcessingName); ?>" <? Format::selected('processing', $ProcessingName) ?>><?= display_str($ProcessingName); ?></option>
                                <?  } ?>
                            </select>
                            <select name="releasetype" class="ft_releasetype fti_advanced">
                                <option value=""><?= Lang::get('torrents', 'ft_releasetype') ?></option>
                                <? foreach ($ReleaseTypes as $ID => $Type) { ?>
                                    <option value="<?= display_str($ID); ?>" <? Format::selected('releasetype', $ID) ?>><?= display_str(Lang::get('torrents', 'release_types')[$ID]); ?></option>
                                <?  } ?>
                            </select>
                            <select name="freetorrent">
                                <option value=""><?= Lang::get('tools', 'sales_promotion_plan') ?></option>
                                <option value='1'><?= Lang::get('tools', 'free_leech') ?></option>
                                <option value='11'><?= Lang::get('tools', '25_percent_off') ?></option>
                                <option value='12'><?= Lang::get('tools', '50_percent_off') ?></option>
                                <option value='13'><?= Lang::get('tools', '75_percent_off') ?></option>
                                <option value='2'><?= Lang::get('tools', 'neutral_leech') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="search_terms" class="ftr_basic<?= $HideBasic ?>">
                        <td class="label"><?= Lang::get('torrents', 'ftb_searchstr') ?>:</td>
                        <td colspan="3" class="ftb_searchstr">
                            <input type="search" spellcheck="false" size="40" name="searchstr" class="inputtext fti_basic" value="<? Format::form('searchstr') ?>" />
                        </td>
                    </tr>
                    <tr id="tagfilter">
                        <td class="label"><span title="<?= Lang::get('global', 'tags') ?>" class="tooltip"><?= Lang::get('global', 'tags') ?>:</span></td>
                        <td colspan="3" class="ft_taglist">
                            <input type="search" placeholder="<?= Lang::get('global', 'comma_separated') ?>" size="40" id="tags" name="taglist" class="inputtext smaller" value="<?= display_str($Search->get_terms('taglist')) ?>" <? Users::has_autocomplete_enabled('other'); ?> />&nbsp;
                            <div style="display: inline-block"><input type="radio" name="tags_type" id="tags_type0" value="0" <? Format::selected('tags_type', 0, 'checked') ?> /><label for="tags_type0"> <?= Lang::get('torrents', 'any') ?></label>&nbsp;&nbsp;
                                <input type="radio" name="tags_type" id="tags_type1" value="1" <? Format::selected('tags_type', 1, 'checked') ?> /><label for="tags_type1"> <?= Lang::get('torrents', 'all') ?></label>
                            </div>
                        </td>
                    </tr>

                    <tr id="order">
                        <td class="label"><?= Lang::get('torrents', 'ft_order') ?>:</td>
                        <td colspan="3" class="ft_order">
                            <select name="order_by" style="width: auto;" class="ft_order_by">
                                <option value="time" <? Format::selected('order_by', 'time') ?>><?= Lang::get('torrents', 'add_time') ?></option>
                                <option value="year" <? Format::selected('order_by', 'year') ?>><?= Lang::get('torrents', 'year') ?></option>
                                <option value="size" <? Format::selected('order_by', 'size') ?>><?= Lang::get('global', 'size') ?></option>
                                <option value="snatched" <? Format::selected('order_by', 'snatched') ?>><?= Lang::get('global', 'snatched') ?></option>
                                <option value="seeders" <? Format::selected('order_by', 'seeders') ?>><?= Lang::get('global', 'seeders') ?></option>
                                <option value="leechers" <? Format::selected('order_by', 'leechers') ?>><?= Lang::get('global', 'leechers') ?></option>
                                <option value="random" <? Format::selected('order_by', 'random') ?>><?= Lang::get('torrents', 'random') ?></option>
                            </select>
                            <select name="order_way" class="ft_order_way">
                                <option value="desc" <? Format::selected('order_way', 'desc') ?>><?= Lang::get('torrents', 'desc') ?></option>
                                <option value="asc" <? Format::selected('order_way', 'asc') ?>><?= Lang::get('torrents', 'asc') ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr id="search_group_results">
                        <td class="label">
                            <label for="group_results"><?= Lang::get('torrents', 'group_results') ?>:</label>
                        </td>
                        <td colspan="3" class="ft_group_results">
                            <input type="checkbox" value="1" name="group_results" id="group_results" <?= $GroupResults ? ' checked="checked"' : '' ?> />
                        </td>
                    </tr>
                </table>
                <table class="layout cat_list ft_cat_list">
                    <?
                    $x = 0;
                    reset($Categories);
                    foreach ($Categories as $CatKey => $CatName) {
                        if ($x % 7 == 0) {
                            if ($x > 0) {
                    ?>
                                </tr>
                            <?      } ?>
                            <tr>
                            <?
                        }
                        $x++;
                            ?>
                        <?
                    }
                        ?>
                            </tr>
                </table>
                <table class="layout cat_list<? if (empty($LoggedUser['ShowTags'])) { ?> hidden<? } ?>" id="taglist">
                    <tr>
                        <?
                        $GenreTags = $Cache->get_value('other_tags');
                        if (!$GenreTags) {
                            $DB->query('
		SELECT Name
		FROM tags
		WHERE TagType = \'other\'
		ORDER BY Name');
                            $GenreTags = $DB->collect('Name');
                            $Cache->cache_value('other_tags', $GenreTags, 3600 * 6);
                        }

                        $x = 0;
                        foreach ($GenreTags as $Tag) {
                        ?>
                            <td width="12.5%"><a href="#" onclick="add_tag('<?= $Tag ?>'); return false;"><?= $Tag ?></a></td>
                            <?
                            $x++;
                            if ($x % 7 == 0) {
                            ?>
                    </tr>
                    <tr>
                    <?
                            }
                        }
                        if ($x % 7 != 0) { // Padding
                    ?>
                    <td colspan="<?= (7 - ($x % 7)) ?>"> </td>
                <? } ?>
                    </tr>
                </table>
                <table class="layout cat_list" style="width: 100% !important;">
                    <tr>
                        <!-- <td>
                    <a class="brackets" href="random.php?action=torrent"><?= Lang::get('torrents', 'sj_torrents') ?></a>
                    <a class="brackets" href="random.php?action=artist"><?= Lang::get('torrents', 'sj_artists') ?></a>
                </td> -->
                        <td class="label" style="width: 100%;">
                            <!--<a class="brackets" href="#" onclick="$('#taglist').gtoggle(); if (this.innerHTML == '查看标签') { this.innerHTML = '隐藏标签'; } else { this.innerHTML = '查看标签'; }; return false;"><?= (empty($LoggedUser['ShowTags']) ? Lang::get('torrents', 'view_tags') : Lang::get('torrents', 'hide_tags')) ?></a>-->
                            <a class="brackets" href="#" onclick="$('#taglist').gtoggle(); if (this.innerHTML == <?= "'" . Lang::get('torrents', 'view_tags') . "'" ?>) { this.innerHTML = <?= "'" . Lang::get('torrents', 'hide_tags') . "'" ?>; } else { this.innerHTML = <?= "'" . Lang::get('torrents', 'view_tags') . "'" ?>; }; return false;"><?= (empty($LoggedUser['ShowTags']) ? Lang::get('torrents', 'view_tags') : Lang::get('torrents', 'hide_tags')) ?></a>
                        </td>
                    </tr>
                </table>
                <div class="submit ft_submit">
                    <span style="float: left;">
                        <!--
                --><?= number_format($RealNumResults) ?> <?= Lang::get('torrents', 'space_results') ?>
                        <?= !check_perms('site_search_many') ? "(" . Lang::get('torrents', 'showing_first_n_matches_before') . $NumResults . Lang::get('torrents', 'showing_first_n_matches_after') . ")" : "" ?>
                    </span>
                    <input type="submit" value="<?= Lang::get('torrents', 'search_torrents') ?>" />
                    <input type="hidden" name="action" id="ft_type" value="<?= ($AdvancedSearch ? 'advanced' : 'basic') ?>" />
                    <input type="hidden" name="searchsubmit" value="1" />
                    <input type="button" value="<?= Lang::get('torrents', 'reset') ?>" onclick="location.href = 'torrents.php<? if (isset($_GET['action']) && $_GET['action'] === 'advanced') { ?>?action=advanced<? } ?>'" />
                    <? if ($Search->has_filters()) { ?>
                        <input type="submit" name="setdefault" value="<?= Lang::get('torrents', 'setdefault') ?>" />
                    <?
                    }

                    if (!empty($LoggedUser['DefaultSearch'])) {
                    ?>
                        <input type="submit" name="cleardefault" value="<?= Lang::get('torrents', 'cleardefault') ?>" />
                    <?  } ?>
                </div>
            </div>
        </div>
    </form>
    <?
    if ($NumResults == 0) {
        $DB->query("
		SELECT
			tags.Name,
			((COUNT(tags.Name) - 2) * (SUM(tt.PositiveVotes) - SUM(tt.NegativeVotes))) / (tags.Uses * 0.8) AS Score
		FROM xbt_snatched AS s
			INNER JOIN torrents AS t ON t.ID = s.fid
			INNER JOIN torrents_group AS g ON t.GroupID = g.ID
			INNER JOIN torrents_tags AS tt ON tt.GroupID = g.ID
			INNER JOIN tags ON tags.ID = tt.TagID
		WHERE s.uid = '$LoggedUser[ID]'
			AND tt.TagID != '13679'
			AND tt.TagID != '4820'
			AND tt.TagID != '2838'
			AND g.CategoryID = '1'
			AND tags.Uses > '10'
		GROUP BY tt.TagID
		ORDER BY Score DESC
		LIMIT 8");
        $TagText = array();
        while (list($Tag) = $DB->next_record()) {
            $TagText[] = "<a href='torrents.php?taglist={$Tag}'>{$Tag}</a>";
        }
        $TagText = implode(", ", $TagText);
        print <<<HTML
<div class="box pad" align="center">
	<h2>你的搜索与任何内容都不匹配</h2>
	<p>确保所有名称拼写正确，或尝试模糊搜索</p>
	<p>你可能喜欢 (beta): {$TagText}</p>
</div>
</div>
HTML;
        View::show_footer();
        die();
    }

    if ($NumResults < ($Page - 1) * TORRENTS_PER_PAGE + 1) {
        $LastPage = ceil($NumResults / TORRENTS_PER_PAGE);
        $Pages = Format::get_pages(0, $NumResults, TORRENTS_PER_PAGE);
    ?>
        <div class="box pad" align="center">
            <h2>The requested page contains no matches.</h2>
            <p>You are requesting page <?= $Page ?>, but the search returned only <?= number_format($LastPage) ?> pages.</p>
        </div>
        <div class="linkbox">Go to page <?= $Pages ?></div>
</div>
<?
        View::show_footer();
        die();
    }

    // List of pages
    $Pages = Format::get_pages($Page, $NumResults, TORRENTS_PER_PAGE);


    if (check_perms('torrents_check')) {
        $CheckAllTorrents = !$LoggedUser['DisableCheckAll'];
    } else {
        $CheckAllTorrents = false;
    }
    if (check_perms('self_torrents_check')) {
        $CheckSelfTorrents = !$LoggedUser['DisableCheckSelf'];
    } else {
        $CheckSelfTorrents = false;
    }
?>

<div class="linkbox"><?= $Pages ?></div>
<?
if ($CheckAllTorrents || $CheckSelfTorrents) {
?>
    <script>
        function torrent_check(event) {
            var id = event.data.id,
                checked = event.data.checked
            $.get("torrents.php", {
                    action: "torrent_check",
                    torrentid: id,
                    checked: checked
                },
                function(data) {
                    var obj = eval("(" + data + ")");
                    if (obj.ret == "success") {
                        if (checked == 1) {
                            $('#torrent' + id + '_check1').show()
                            $('#torrent' + id + '_check0').hide()
                        } else {
                            $('#torrent' + id + '_check0').show()
                            $('#torrent' + id + '_check1').hide()
                        }
                    } else {
                        alert('失败');
                    }
                });
        }

        function torrents_unchecked() {
            $('#torrents_unchecked').hide()
            $('#torrents_checked').show()
            $('.torrent_all_checked').show()
            $('.torrent_checked').show()
            $('.torrent_all_unchecked').hide()
            $('.torrent_unchecked').hide()
        }

        function torrents_checked() {
            $('#torrents_checked').hide()
            $('#torrents_all').show()
            $('.torrent_all_unchecked').show()
            $('.torrent_unchecked').show()
        }

        function torrents_all() {
            $('#torrents_all').hide()
            $('#torrents_unchecked').show()
            $('.torrent_all_checked').hide()
            $('.torrent_checked').hide()
        }
        $(document).ready(function() {
            $('#torrents_all').click(torrents_all)
            $('#torrents_unchecked').click(torrents_unchecked)
            $('#torrents_checked').click(torrents_checked)
        })
    </script>
<? } ?>
<table class="cmp-torrent-table torrent_table list cats <?= $GroupResults ? 'grouping' : 'no_grouping' ?> m_table" id="torrent_table">
    <?
    if ($CheckAllTorrents) {
        $DB->query("select count(*) from torrents where Checked=0");
        list($AllUncheckedCnt) = $DB->next_record();
        $PageUncheckedCnt = 0;
        foreach ($Results as $Key => $GroupID) {
            $GroupInfo = $Groups[$GroupID];
            if ($GroupResults) {
                $Torrents = $GroupInfo['Torrents'];
            } else {
                $TorrentID = $Key;
                $Torrents = array($TorrentID => $GroupInfo['Torrents'][$TorrentID]);
            }
            foreach ($Torrents as $TorrentID => $Data) {
                $TorrentChecked = $Cache->get_value("torrent_checked_$TorrentID");
                if ($TorrentChecked === false) {
                    $DB->query("select Checked from torrents where ID=$TorrentID");
                    list($TorrentChecked) = $DB->next_record();
                    $Cache->cache_value("torrent_checked_$TorrentID", $TorrentChecked);
                }
                if (!$TorrentChecked) {
                    $PageUncheckedCnt++;
                }
            }
        }
        if ($AllUncheckedCnt < 50) {
            $CntColor = "#009900";
        } else if ($AllUncheckedCnt < 100) {
            $CntColor = "#99CC33";
        } else if ($AllUncheckedCnt < 200) {
            $CntColor = "#F2C300";
        } else {
            $CntColor = "#CF3434";
        }
    }
    print_torrent_table_header(TorrentTableScene::Main, ['GroupResults' => $GroupResults, 'CheckAllTorrents' => $CheckAllTorrents, 'PageUncheckedCnt' => $PageUncheckedCnt, 'AllUncheckedCnt' => $AllUncheckedCnt, 'CntColor' => $CntColor]);


    // Start printing torrent list
    foreach ($Results as $Key => $GroupID) {
        $GroupInfo = $Groups[$GroupID];
        if (empty($GroupInfo['Torrents'])) {
            continue;
        }
        $CategoryID = $GroupInfo['CategoryID'];
        $ExtendedArtists = $GroupInfo['ExtendedArtists'];
        $ReleaseType = $GroupInfo['ReleaseType'];
        if ($GroupResults) {
            $Torrents = $GroupInfo['Torrents'];
            $GroupTime = $MaxSize = $TotalLeechers = $TotalSeeders = $TotalSnatched = 0;
            foreach ($Torrents as $T) {
                $GroupTime = max($GroupTime, strtotime($T['Time']));
                $MaxSize = max($MaxSize, $T['Size']);
                $TotalLeechers += $T['Leechers'];
                $TotalSeeders += $T['Seeders'];
                $TotalSnatched += $T['Snatched'];
            }
        } else {
            $TorrentID = $Key;
            $Torrents = array($TorrentID => $GroupInfo['Torrents'][$TorrentID]);
        }

        $TorrentTags = new Tags($GroupInfo['TagList']);
        $Director = Artists::get_first_directors($ExtendedArtists);

        $SnatchedGroupClass = $GroupInfo['Flags']['IsSnatched'] ? ' snatched_group' : '';

        if ($GroupResults && (count($Torrents) > 1 || isset($GroupedCategories[$CategoryID - 1]))) {
            // These torrents are in a group
            $GroupAllChecked = true;
            $GroupAllUnchecked = true;
            foreach ($Torrents as $TorrentID => $Data) {
                $TorrentChecked = $Cache->get_value("torrent_checked_$TorrentID");
                if ($TorrentChecked === false) {
                    $DB->query("select Checked from torrents where ID=$TorrentID");
                    list($TorrentChecked) = $DB->next_record();
                    $Cache->cache_value("torrent_checked_$TorrentID", $TorrentChecked);
                }
                if ($TorrentChecked) {
                    $GroupAllUnchecked = false;
                } else {
                    $GroupAllChecked = false;
                }
            }
    ?>
            <tr class="<?= $GroupAllChecked ? "torrent_all_checked " : ($GroupAllUnchecked ? "torrent_all_unchecked " : "") ?>group<?= $SnatchedGroupClass ?>">
                <?
                print_group_info($GroupInfo, $TorrentTags, $Action, false);
                ?>

            </tr>
            <?
            $LastRemasterTitle = '';
            $LastRemasterCustomTitle = '';
            $LastResolution = '';
            $LastNotMain = '';

            $EditionID = 0;
            $FirstUnknown = null;
            uasort($Torrents, 'Torrents::sort_torrent');
            foreach ($Torrents as $TorrentID => $Data) {
                // All of the individual torrents in the group

                //Get report info for each torrent, use the cache if available, if not, add to it.
                $Reported = false;
                $Reports = Torrents::get_reports($TorrentID);
                if (count($Reports) > 0) {
                    $Reported = true;
                }

                if ($Data['Remastered'] && !$Data['RemasterYear']) {
                    $FirstUnknown = !isset($FirstUnknown);
                }
                $SnatchedTorrentClass = $Data['IsSnatched'] ? ' snatched_torrent' : '';

                if (isset($GroupedCategories[$CategoryID - 1])) {
                    $NewEdition = Torrents::get_new_edition_title($LastResolution, $LastRemasterTitle, $LastRemasterCustomTitle, $LastNotMain, $Data['Resolution'], $Data['RemasterTitle'], $Data['RemasterCustomTitle'], $Data['NotMainMovie']);
                    if ($NewEdition) {
                        $EditionID++;
            ?>
                        <tr class="<?= $TorrentChecked ? "torrent_checked " : "torrent_unchecked" ?> group_torrent groupid_<?= $GroupID ?> edition<?= (!empty($LoggedUser['TorrentGrouping']) && $LoggedUser['TorrentGrouping'] === 1 ? ' hidden' : '') ?>">
                            <td colspan="9" class="edition_info"><strong>
                                    <a href="#" onclick="torrentTable.toggleEdition(event, <?= $GroupID ?>, <?= $EditionID ?>)" class="tooltip" title="<?= Lang::get('global', 'collapse_this_edition_title') ?>">&minus;</a>
                                    <?= $NewEdition ?>
                                </strong></td>
                        </tr>
                <?
                    }
                }
                $LastRemasterTitle = $Data['RemasterTitle'];
                $LastRemasterCustomTitle = $Data['RemasterCustomTitle'];
                $LastResolution = $Data['Resolution'];
                $LastNotMain = $Data['NotMainMovie'];
                $TorrentChecked = $Cache->get_value("torrent_checked_$TorrentID");
                $TorrentCheckedBy = 'unknown';
                if ($TorrentChecked && $TorrentChecked != 1) {
                    $DB->query("select Username from users_main where ID=$TorrentChecked");
                    list($TorrentCheckedBy) = $DB->next_record();
                }
                ?>
                <tr class="<?= $TorrentChecked ? "torrent_checked " : "torrent_unchecked" ?> group_torrent groupid_<?= $GroupID ?> edition_<?= $EditionID ?><?= $SnatchedTorrentClass . $SnatchedGroupClass . (!empty($LoggedUser['TorrentGrouping']) && $LoggedUser['TorrentGrouping'] == 1 ? ' hidden' : '') ?>">
                    <td class="td_info" colspan="3">
                        <span>
                            [ <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>" class="tooltip" title="<?= Lang::get('global', 'download') ?>"><?= $Data['HasFile'] ? 'DL' : 'Missing' ?></a>
                            <? if (Torrents::can_use_token($Data)) { ?>
                                | <a href="torrents.php?action=download&amp;id=<?= $TorrentID ?>&amp;authkey=<?= $LoggedUser['AuthKey'] ?>&amp;torrent_pass=<?= $LoggedUser['torrent_pass'] ?>&amp;usetoken=1" class="tooltip" title="<?= Lang::get('global', 'use_fl_tokens') ?>" onclick="return confirm('<?= FL_confirmation_msg($Data['Seeders'], $Data['Size']) ?>');">FL</a>
                            <?          } ?>
                            | <a href="reportsv2.php?action=report&amp;id=<?= $TorrentID ?>" class="tooltip" title="<?= Lang::get('torrents', 'report') ?>"> RP </a> ]
                        </span>
                        <?
                        if (canCheckTorrent($TorrentID)) {
                            if (!$CheckAllTorrents && $CheckSelfTorrents) {
                                if ($TorrentCheckedBy != $LoggedUser['Username']) {
                                    $TorrentCheckedBy = "someone";
                                }
                            }
                        ?>
                            <script>
                                $(document).ready(function() {
                                    $('#torrent<?= $TorrentID ?>_check0').bind('click', {
                                        id: <?= $TorrentID ?>,
                                        checked: 1,
                                    }, torrent_check)
                                    $('#torrent<?= $TorrentID ?>_check1').bind('click', {
                                        id: <?= $TorrentID ?>,
                                        checked: 0,
                                    }, torrent_check)
                                })
                            </script>
                            <a href="javascript:void(0)" class="far fa-check-circle" id="torrent<?= $TorrentID ?>_check1" style="display:<?= $TorrentChecked ? "inline-block" : "none" ?>;color:#74B274;" title="Checked by <?= $TorrentChecked ? $TorrentCheckedBy : $LoggedUser['Username'] ?>"></a>
                            <a href="javascript:void(0)" class="far fa-circle" id="torrent<?= $TorrentID ?>_check0" style="display:<?= $TorrentChecked ? "none" : "inline-block" ?>;color:#555555;" title="<?= Lang::get('torrents', 'turn_me_green') ?>"></a>
                        <?
                        } else {
                        ?>
                            <i class="far fa-<?= $TorrentChecked ? "check-" : "" ?>circle" style="color: <?= $TorrentChecked ? "#74B274" : "#A6A6A6" ?>;" title="<?= $TorrentChecked ? Lang::get('torrents', 'has_been_checked') : Lang::get('torrents', 'has_not_been_checked') ?><?= Lang::get('torrents', 'checked_explanation') ?>"></i>
                        <?
                        }
                        ?>
                        &nbsp; <a class="torrent_specs" href="torrents.php?id=<?= $GroupID ?>&amp;torrentid=<?= $TorrentID ?>"><?= Torrents::torrent_info($Data, true, true) ?></a>
                    </td>
                    <td class="td_file_count"><?= $Data['FileCount'] ?></td>
                    <td class="td_time nobr"><?= time_diff($Data['Time'], 1) ?></td>
                    <td class="td_size number_column nobr"><?= Format::get_size($Data['Size']) ?></td>
                    <td class="td_snatched number_column m_td_right"><?= number_format($Data['Snatched']) ?></td>
                    <td class="td_seeders number_column<?= ($Data['Seeders'] == 0) ? ' r00' : '' ?> m_td_right"><?= number_format($Data['Seeders']) ?></td>
                    <td class="td_leechers number_column m_td_right"><?= number_format($Data['Leechers']) ?></td>
                </tr>
    <?
            }
        } else {
            // Viewing a type that does not require grouping
            list(, $Data) = each($Torrents);
            print_ungroup_info($Data, $GroupInfo, $CategoryID, $TorrentTags, $Director, $Action);
        }
    }
    ?>
</table>
<div class="linkbox"><?= $Pages ?></div>
</div>
<?
View::show_footer();
