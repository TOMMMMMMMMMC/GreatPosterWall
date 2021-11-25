<?php
$SphQL = new SphinxqlQuery();
$SphQL->select('id, votes, bounty')->from('requests, requests_delta');

$SortOrders = array(
    'votes' => 'votes',
    'bounty' => 'bounty',
    'lastvote' => 'lastvote',
    'filled' => 'timefilled',
    'year' => 'year',
    'created' => 'timeadded',
    'random' => false
);

if (empty($_GET['order']) || !isset($SortOrders[$_GET['order']])) {
    $_GET['order'] = 'created';
}
$OrderBy = $_GET['order'];

if (!empty($_GET['sort']) && $_GET['sort'] === 'asc') {
    $OrderWay = 'asc';
} else {
    $_GET['sort'] = 'desc';
    $OrderWay = 'desc';
}
$NewSort = $_GET['sort'] === 'asc' ? 'desc' : 'asc';

if ($OrderBy === 'random') {
    $SphQL->order_by('RAND()', '');
    unset($_GET['page']);
} else {
    $SphQL->order_by($SortOrders[$OrderBy], $OrderWay);
}

$Submitted = !empty($_GET['submit']);

//Paranoia
if (!empty($_GET['userid'])) {
    if (!is_number($_GET['userid'])) {
        error('User ID must be an integer');
    }
    $UserInfo = Users::user_info($_GET['userid']);
    if (empty($UserInfo)) {
        error('That user does not exist');
    }
    $Perms = Permissions::get_permissions($UserInfo['PermissionID']);
    $UserClass = $Perms['Class'];
}
$BookmarkView = false;

if (empty($_GET['type'])) {
    $Title = Lang::get('requests', 'requests');
    if (empty($_GET['showall'])) {
        $SphQL->where('visible', 1);
    }
} else {
    switch ($_GET['type']) {
        case 'created':
            if (!empty($UserInfo)) {
                if (!check_paranoia('requestsvoted_list', $UserInfo['Paranoia'], $Perms['Class'], $UserInfo['ID'])) {
                    error(403);
                }
                $Title = Lang::get('requests', 'requests_created_by_before') . $UserInfo[Username] . Lang::get('requests', 'requests_created_by_after');
                $SphQL->where('userid', $UserInfo['ID']);
            } else {
                $Title = Lang::get('requests', 'my_requests');
                $SphQL->where('userid', $LoggedUser['ID']);
            }
            break;
        case 'voted':
            if (!empty($UserInfo)) {
                if (!check_paranoia('requestsvoted_list', $UserInfo['Paranoia'], $Perms['Class'], $UserInfo['ID'])) {
                    error(403);
                }
                $Title = Lang::get('requests', 'requests_voted_for_by_before') . $UserInfo[Username] . Lang::get('requests', 'requests_voted_for_by_after');
                $SphQL->where('voter', $UserInfo['ID']);
            } else {
                $Title = Lang::get('requests', 'requests_i_have_voted_on');
                $SphQL->where('voter', $LoggedUser['ID']);
            }
            break;
        case 'filled':
            if (!empty($UserInfo)) {
                if (!check_paranoia('requestsfilled_list', $UserInfo['Paranoia'], $Perms['Class'], $UserInfo['ID'])) {
                    error(403);
                }
                $Title = Lang::get('requests', 'requests_filled_by_before') . $UserInfo[Username] . Lang::get('requests', 'requests_filled_by_after');
                $SphQL->where('fillerid', $UserInfo['ID']);
            } else {
                $Title = Lang::get('requests', 'requests_i_have_filled');
                $SphQL->where('fillerid', $LoggedUser['ID']);
            }
            break;
        case 'bookmarks':
            $Title = Lang::get('requests', 'bookmarks');
            $BookmarkView = true;
            $SphQL->where('bookmarker', $LoggedUser['ID']);
            break;
        default:
            error(404);
    }
}

if (empty($_GET['show_filled'])) {
    $SphQL->where('torrentid', 0);
}

$EnableNegation = false; // Sphinx needs at least one positive search condition to support the NOT operator

if (!empty($_GET['source'])) {
    $SourceArray = $_GET['source'];
    if (count($SourceArray) !== count($Sources)) {
        $SourceNameArray = array();
        foreach ($SourceArray as $Index => $MasterIndex) {
            if (isset($Sources[$MasterIndex])) {
                $SourceNameArray[$Index] = '"' . strtr(Sphinxql::sph_escape_string($Sources[$MasterIndex]), '-.', '  ') . '"';
            }
        }
        if (count($SourceNameArray) >= 1) {
            $EnableNegation = true;
            if (!empty($_GET['source_strict'])) {
                $SearchString = '(' . implode(' | ', $SourceNameArray) . ')';
            } else {
                $SearchString = '(any | ' . implode(' | ', $SourceNameArray) . ')';
            }
            $SphQL->where_match($SearchString, 'sourcelist', false);
        }
    }
}

if (!empty($_GET['codec'])) {
    $CodecArray = $_GET['codec'];
    if (count($CodecArray) !== count($Codecs)) {
        $CodecNameArray = array();
        foreach ($CodecArray as $Index => $MasterIndex) {
            if (isset($Codecs[$MasterIndex])) {
                $CodecNameArray[$Index] = '"' . strtr(Sphinxql::sph_escape_string($Codecs[$MasterIndex]), '-.', '  ') . '"';
            }
        }

        if (count($CodecNameArray) >= 1) {
            $EnableNegation = true;
            if (!empty($_GET['codec_strict'])) {
                $SearchString = '(' . implode(' | ', $CodecNameArray) . ')';
            } else {
                $SearchString = '(any | ' . implode(' | ', $CodecNameArray) . ')';
            }
            $SphQL->where_match($SearchString, 'codeclist', false);
        }
    }
}

if (!empty($_GET['container'])) {
    $ContainerArray = $_GET['container'];
    if (count($ContainerArray) !== count($Containers)) {
        $ContainerNameArray = array();
        foreach ($ContainerArray as $Index => $MasterIndex) {
            if (isset($Containers[$MasterIndex])) {
                $ContainerNameArray[$Index] = '"' . strtr(Sphinxql::sph_escape_string($Containers[$MasterIndex]), '-.', '  ') . '"';
            }
        }

        if (count($ContainerNameArray) >= 1) {
            $EnableNegation = true;
            if (!empty($_GET['container_strict'])) {
                $SearchString = '(' . implode(' | ', $ContainerNameArray) . ')';
            } else {
                $SearchString = '(any | ' . implode(' | ', $ContainerNameArray) . ')';
            }
            $SphQL->where_match($SearchString, 'containerlist', false);
        }
    }
}

if (!empty($_GET['resolution'])) {
    $ResolutionArray = $_GET['resolution'];
    if (count($ResolutionArray) !== count($Resolutions)) {
        $ResolutionNameArray = array();
        foreach ($ResolutionArray as $Index => $MasterIndex) {
            if (isset($Resolutions[$MasterIndex])) {
                $ResolutionNameArray[$Index] = '"' . strtr(Sphinxql::sph_escape_string($Resolutions[$MasterIndex]), '-.', '  ') . '"';
            }
        }

        if (count($ResolutionNameArray) >= 1) {
            $EnableNegation = true;
            if (!empty($_GET['container_strict'])) {
                $SearchString = '(' . implode(' | ', $ResolutionNameArray) . ')';
            } else {
                $SearchString = '(any | ' . implode(' | ', $ResolutionNameArray) . ')';
            }
            $SphQL->where_match($SearchString, 'resolutionlist', false);
        }
    }
}

if (!empty($_GET['search'])) {
    $SearchString = trim($_GET['search']);

    if ($SearchString !== '') {
        $SearchWords = array('include' => array(), 'exclude' => array());
        $Words = explode(' ', $SearchString);
        foreach ($Words as $Word) {
            $Word = trim($Word);
            // Skip isolated hyphens to enable "Artist - Title" searches
            if ($Word === '-') {
                continue;
            }
            if ($Word[0] === '!' && strlen($Word) >= 2) {
                if (strpos($Word, '!', 1) === false) {
                    $SearchWords['exclude'][] = $Word;
                } else {
                    $SearchWords['include'][] = $Word;
                    $EnableNegation = true;
                }
            } elseif ($Word !== '') {
                $SearchWords['include'][] = $Word;
                $EnableNegation = true;
            }
        }
    }
}

if (!isset($_GET['tags_type']) || $_GET['tags_type'] === '1') {
    $TagType = 1;
    $_GET['tags_type'] = '1';
} else {
    $TagType = 0;
    $_GET['tags_type'] = '0';
}

if (!empty($_GET['tags'])) {
    $SearchTags = array('include' => array(), 'exclude' => array());
    $Tags = explode(',', str_replace('.', '_', $_GET['tags']));
    foreach ($Tags as $Tag) {
        $Tag = trim($Tag);
        if ($Tag[0] === '!' && strlen($Tag) >= 2) {
            if (strpos($Tag, '!', 1) === false) {
                $SearchTags['exclude'][] = $Tag;
            } else {
                $SearchTags['include'][] = $Tag;
                $EnableNegation = true;
            }
        } elseif ($Tag !== '') {
            $SearchTags['include'][] = $Tag;
            $EnableNegation = true;
        }
    }

    $TagFilter = Tags::tag_filter_sph($SearchTags, $EnableNegation, $TagType);
    $TagNames = $TagFilter['input'];

    if (!empty($TagFilter['predicate'])) {
        $SphQL->where_match($TagFilter['predicate'], 'taglist', false);
    }
} elseif (!isset($_GET['tags_type']) || $_GET['tags_type'] !== '0') {
    $_GET['tags_type'] = 1;
} else {
    $_GET['tags_type'] = 0;
}

if (isset($SearchWords)) {
    $QueryParts = array();
    if (!$EnableNegation && !empty($SearchWords['exclude'])) {
        $SearchWords['include'] = array_merge($SearchWords['include'], $SearchWords['exclude']);
        unset($SearchWords['exclude']);
    }
    foreach ($SearchWords['include'] as $Word) {
        $QueryParts[] = Sphinxql::sph_escape_string($Word);
    }
    if (!empty($SearchWords['exclude'])) {
        foreach ($SearchWords['exclude'] as $Word) {
            $QueryParts[] = '!' . Sphinxql::sph_escape_string(substr($Word, 1));
        }
    }
    if (!empty($QueryParts)) {
        $SearchString = implode(' ', $QueryParts);
        $SphQL->where_match($SearchString, '*', false);
    }
}

if (!empty($_GET['filter_cat'])) {
    $CategoryArray = array_keys($_GET['filter_cat']);
    if (count($CategoryArray) !== count($Categories)) {
        foreach ($CategoryArray as $Key => $Index) {
            if (!isset($Categories[$Index - 1])) {
                unset($CategoryArray[$Key]);
            }
        }
        if (count($CategoryArray) >= 1) {
            $SphQL->where('categoryid', $CategoryArray);
        }
    }
}

if (!empty($_GET['releases'])) {
    $ReleaseArray = $_GET['releases'];
    if (count($ReleaseArray) !== count($ReleaseTypes)) {
        foreach ($ReleaseArray as $Index => $Value) {
            if (!isset($ReleaseTypes[$Value])) {
                unset($ReleaseArray[$Index]);
            }
        }
        if (count($ReleaseArray) >= 1) {
            $SphQL->where('releasetype', $ReleaseArray);
        }
    }
}

if (!empty($_GET['requestor'])) {
    if (is_number($_GET['requestor'])) {
        $SphQL->where('userid', $_GET['requestor']);
    } else {
        error(404);
    }
}

if (isset($_GET['year'])) {
    if (is_number($_GET['year']) || $_GET['year'] === '0') {
        $SphQL->where('year', $_GET['year']);
    } else {
        error(404);
    }
}

if (!empty($_GET['page']) && is_number($_GET['page']) && $_GET['page'] > 0) {
    $Page = $_GET['page'];
    $Offset = ($Page - 1) * REQUESTS_PER_PAGE;
    $SphQL->limit($Offset, REQUESTS_PER_PAGE, $Offset + REQUESTS_PER_PAGE);
} else {
    $Page = 1;
    $SphQL->limit(0, REQUESTS_PER_PAGE, REQUESTS_PER_PAGE);
}

$SphQLResult = $SphQL->query();
$NumResults = (int)$SphQLResult->get_meta('total_found');
if ($NumResults > 0) {
    $SphRequests = $SphQLResult->to_array('id');
    if ($OrderBy === 'random') {
        $NumResults = count($SphRequests);
    }
    if ($NumResults > REQUESTS_PER_PAGE) {
        if (($Page - 1) * REQUESTS_PER_PAGE > $NumResults) {
            $Page = 0;
        }
        $PageLinks = Format::get_pages($Page, $NumResults, REQUESTS_PER_PAGE);
    }
}

$CurrentURL = Format::get_url(array('order', 'sort', 'page'));
View::show_header($Title);

?>
<div class="thin">
    <div class="header">
        <h2><?= $Title ?></h2>
    </div>
    <div class="linkbox">
        <? if (!$BookmarkView) {
            if (check_perms('site_submit_requests')) { ?>
                <a href="requests.php?action=new" class="brackets"><?= Lang::get('requests', 'new_request') ?></a>
                <a href="requests.php?type=created" class="brackets"><?= Lang::get('requests', 'my_requests') ?></a>
            <?      }
            if (check_perms('site_vote')) { ?>
                <a href="requests.php?type=voted" class="brackets"><?= Lang::get('requests', 'vote_requests') ?></a>
            <?      } ?>
            <a href="bookmarks.php?type=requests" class="brackets"><?= Lang::get('requests', 'bookmarked_requests') ?></a>
        <?  } else { ?>
            <a href="bookmarks.php?type=torrents" class="brackets"><?= Lang::get('global', 'torrents') ?></a>
            <a href="bookmarks.php?type=artists" class="brackets"><?= Lang::get('global', 'artists') ?></a>
            <?
            if (ENABLE_COLLAGES) {
            ?>
                <a href="bookmarks.php?type=collages" class="brackets"><?= Lang::get('requests', 'collages') ?></a>
            <?
            }
            ?>
            <a href="bookmarks.php?type=requests" class="brackets"><?= Lang::get('global', 'requests') ?></a>
        <?  } ?>
    </div>
    <? if ($BookmarkView && $NumResults === 0) { ?>
        <div class="box pad" align="center">
            <h2><?= Lang::get('requests', 'you_have_not_bookmarked_any_request') ?></h2>
        </div>
    <?  } else { ?>
        <form class="search_form" name="requests" action="" method="get">
            <? if ($BookmarkView) { ?>
                <input type="hidden" name="action" value="view" />
                <input type="hidden" name="type" value="requests" />
            <?      } elseif (isset($_GET['type'])) { ?>
                <input type="hidden" name="type" value="<?= $_GET['type'] ?>" />
            <?      } ?>
            <input type="hidden" name="submit" value="true" />
            <? if (!empty($_GET['userid']) && is_number($_GET['userid'])) { ?>
                <input type="hidden" name="userid" value="<?= $_GET['userid'] ?>" />
            <?      } ?>
            <table class="layout border" id="requests_search_box">
                <tr id="search_terms">
                    <td class="label"><?= Lang::get('requests', 'search_terms') ?>:</td>
                    <td>
                        <input type="search" name="search" size="75" value="<? if (isset($_GET['search'])) {
                                                                                echo display_str($_GET['search']);
                                                                            } ?>" />
                    </td>
                </tr>
                <tr id="tagfilter">
                    <td class="label"><?= Lang::get('requests', 'tags_comma') ?>:</td>
                    <td>
                        <input type="search" name="tags" id="tags" size="60" value="<?= !empty($TagNames) ? display_str($TagNames) : '' ?>" <? Users::has_autocomplete_enabled('other'); ?> />&nbsp;
                        <label for="tags_type0"><input type="radio" name="tags_type" id="tags_type0" value="0" <? Format::selected('tags_type', 0, 'checked') ?> /><?= Lang::get('requests', 'any') ?></label>&nbsp;&nbsp;
                        <label for="tags_type1"><input type="radio" name="tags_type" id="tags_type1" value="1" <? Format::selected('tags_type', 1, 'checked') ?> /><?= Lang::get('requests', 'all') ?></label>
                    </td>
                </tr>
                <tr id="include_filled">
                    <td class="label"><label for="include_filled_box"><?= Lang::get('requests', 'include_filled') ?>:</label></td>
                    <td>
                        <input type="checkbox" id="include_filled_box" name="show_filled" <? if (!empty($_GET['show_filled']) || (!empty($_GET['type']) && $_GET['type'] === 'filled')) { ?> checked="checked" <? } ?> />
                    </td>
                </tr>
                <tr id="include_old">
                    <td class="label"><label for="include_old_box"><?= Lang::get('requests', 'include_old') ?>:</label></td>
                    <td>
                        <input type="checkbox" id="include_old_box" name="showall" <? if (!empty($_GET['showall'])) { ?> checked="checked" <? } ?> />
                    </td>
                </tr>
                <?      /* ?>
            <tr>
                <td class="label">Requested by:</td>
                <td>
                    <input type="search" name="requester" size="75" value="<?=display_str($_GET['requester'])?>" />
                </td>
            </tr>
<?      */ ?>
            </table>
            <!-- 下边这玩意儿暂时还用不上 -->
            <!-- <table class="layout cat_list" id="requests_search_switcher">
<?
        $x = 1;
        reset($Categories);
        foreach ($Categories as $CatKey => $CatName) {
            if ($x % 8 === 0 || $x === 1) {
?>
                <tr>
<?          } ?>
                    <td>
                        <input type="checkbox" name="filter_cat[<?= ($CatKey + 1) ?>]" id="cat_<?= ($CatKey + 1) ?>" value="1"<? if (isset($_GET['filter_cat'][$CatKey + 1])) { ?> checked="checked"<? } ?> />
                        <label for="cat_<?= ($CatKey + 1) ?>"><?= $CatName ?></label>
                    </td>
<? if ($x % 7 === 0) { ?>
                </tr>
<?
            }
            $x++;
        }
?>
        </table> -->
            <table class="layout" id="requests_search_filter">
                <tr id="release_list">
                    <td class="label"><?= Lang::get('requests', 'release_list') ?></td>
                    <td>
                        <input type="checkbox" id="toggle_releases" onchange="Toggle('releases', 0);" <?= (!$Submitted || !empty($ReleaseArray) && count($ReleaseArray) === count($ReleaseTypes) ? ' checked="checked"' : '') ?> /> <label for="toggle_releases">All</label>
                        <?
                        $i = 0;
                        foreach ($ReleaseTypes as $Key => $Val) {
                            if ($i % 8 === 0) {
                                echo '<br />';
                            }
                        ?>
                            <input type="checkbox" name="releases[]" value="<?= $Key ?>" id="release_<?= $Key ?>" <?= (!$Submitted || (!empty($ReleaseArray) && in_array($Key, $ReleaseArray)) ? ' checked="checked" ' : '') ?> /> <label for="release_<?= $Key ?>"><?= Lang::get('torrents', 'release_types')[$Key] ?></label>
                        <?
                            $i++;
                        }
                        ?>
                    </td>
                </tr>
                <tr id="source_list">
                    <td class="label"><?= Lang::get('requests', 'source_list') ?>:</td>
                    <td>
                        <input type="checkbox" id="toggle_source" onchange="Toggle('sources', 0);" <?= (!$Submitted || !empty($SourceArray) && count($SourceArray) === count($Sources) ? ' checked="checked"' : '') ?> />
                        <label for="toggle_source">All</label>
                        <input type="checkbox" id="source_strict" name="source_strict" <?= (!empty($_GET['source_strict']) ? ' checked="checked"' : '') ?> />
                        <label for="source_strict">Only specified</label>
                        <?
                        foreach ($Sources as $Key => $Val) {
                            if ($Key % 8 === 0) {
                                echo '<br />';
                            }
                        ?>
                            <input type="checkbox" name="source[]" value="<?= $Key ?>" id="source_<?= $Key ?>" <?= (!$Submitted || (!empty($SourceArray) && in_array($Key, $SourceArray)) ? ' checked="checked" ' : '') ?> /> <label for="source_<?= $Key ?>"><?= $Val ?></label>
                        <?      } ?>
                    </td>
                </tr>

                <tr id="codec_list">
                    <td class="label"><?= Lang::get('requests', 'codec_list') ?>:</td>
                    <td>
                        <input type="checkbox" id="toggle_codec" onchange="Toggle('codec', 0);" <?= (!$Submitted || !empty($CodecArray) && count($CodecArray) === count($Codecs) ? ' checked="checked"' : '') ?> />
                        <label for="toggle_codec">All</label>
                        <input type="checkbox" id="codec_strict" name="codec_strict" <?= (!empty($_GET['codec_strict']) ? ' checked="checked"' : '') ?> />
                        <label for="codec_strict">Only specified</label>
                        <?
                        foreach ($Codecs as $Key => $Val) {
                            if ($Key % 8 === 0) {
                                echo '<br />';
                            }
                        ?>
                            <input type="checkbox" name="codec[]" value="<?= $Key ?>" id="codec_<?= $Key ?>" <?= (!$Submitted || (!empty($CodecArray) && in_array($Key, $CodecArray)) ? ' checked="checked" ' : '') ?> /> <label for="codec_<?= $Key ?>"><?= $Val ?></label>
                        <?      } ?>
                    </td>
                </tr>
                <tr id="container_list">
                    <td class="label"><?= Lang::get('requests', 'container_list') ?>:</td>
                    <td>
                        <input type="checkbox" id="toggle_container" onchange="Toggle('container', 0);" <?= (!$Submitted || !empty($ContainerArray) && count($ContainerArray) === count($Containers) ? ' checked="checked"' : '') ?> />
                        <label for="toggle_container">All</label>
                        <input type="checkbox" id="container_strict" name="container_strict" <?= (!empty($_GET['container_strict']) ? ' checked="checked"' : '') ?> />
                        <label for="container_strict">Only specified</label>
                        <?
                        foreach ($Containers as $Key => $Val) {
                            if ($Key % 8 === 0) {
                                echo '<br />';
                            }
                        ?>
                            <input type="checkbox" name="container[]" value="<?= $Key ?>" id="container_<?= $Key ?>" <?= (!$Submitted || (!empty($ContainerArray) && in_array($Key, $ContainerArray)) ? ' checked="checked" ' : '') ?> /> <label for="container_<?= $Key ?>"><?= $Val ?></label>
                        <?
                        }
                        ?>

                <tr id="resolution_list">
                    <td class="label"><?= Lang::get('requests', 'resolution_list') ?>:</td>
                    <td>
                        <input type="checkbox" id="toggle_resolution" onchange="Toggle('resolution', 0);" <?= (!$Submitted || !empty($ResolutionArray) && count($ResolutionArray) === count($Resolutions) ? ' checked="checked"' : '') ?> />
                        <label for="toggle_resolution">All</label>
                        <input type="checkbox" id="resolution_strict" name="resolution_strict" <?= (!empty($_GET['resolution_strict']) ? ' checked="checked"' : '') ?> />
                        <label for="resolution_strict">Only specified</label>
                        <?
                        foreach ($Resolutions as $Key => $Val) {
                            if ($Key % 8 === 0) {
                                echo '<br />';
                            }
                        ?>
                            <input type="checkbox" name="resolution[]" value="<?= $Key ?>" id="resolution_<?= $Key ?>" <?= (!$Submitted || (!empty($ResolutionArray) && in_array($Key, $ResolutionArray)) ? ' checked="checked" ' : '') ?> /> <label for="resolution_<?= $Key ?>"><?= $Val ?></label>
                        <?
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="center">
                        <input type="submit" value="<?= Lang::get('torrents', 'search_requests') ?>" />
                    </td>
                </tr>
            </table>
        </form>
        <? if (isset($PageLinks)) { ?>
            <div class="linkbox">
                <?= $PageLinks ?>
            </div>
        <?      } ?>
        <div class="table_container border">
            <table id="request_table" class="request_table border m_table" cellpadding="6" cellspacing="1" border="0" width="100%">
                <tr class="colhead_dark">
                    <td style="width: 38%;" class="m_th_left nobr">
                        <strong><?= Lang::get('requests', 'name') ?></strong> / <a href="?order=year&amp;sort=<?= ($OrderBy === 'year' ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><strong><?= Lang::get('requests', 'year') ?></strong></a>
                    </td>
                    <td class="m_th_right nobr">
                        <a href="?order=votes&amp;sort=<?= ($OrderBy === 'votes' ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><strong><?= Lang::get('requests', 'votes') ?></strong></a>
                    </td>
                    <td class="m_th_right nobr">
                        <a href="?order=bounty&amp;sort=<?= ($OrderBy === 'bounty' ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><strong><?= Lang::get('requests', 'bounty') ?></strong></a>
                    </td>
                    <td class="nobr">
                        <a href="?order=filled&amp;sort=<?= ($OrderBy === 'filled' ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><strong><?= Lang::get('requests', 'filled') ?></strong></a>
                    </td>
                    <td class="nobr">
                        <strong><?= Lang::get('requests', 'filled_by') ?></strong>
                    </td>
                    <td class="nobr">
                        <strong><?= Lang::get('requests', 'add_by') ?></strong>
                    </td>
                    <td class="nobr">
                        <a href="?order=created&amp;sort=<?= ($OrderBy === 'created' ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><strong><?= Lang::get('requests', 'created') ?></strong></a>
                    </td>
                    <td class="nobr">
                        <a href="?order=lastvote&amp;sort=<?= ($OrderBy === 'lastvote' ? $NewSort : 'desc') ?>&amp;<?= $CurrentURL ?>"><strong><?= Lang::get('requests', 'lastvote') ?></strong></a>
                    </td>
                </tr>
                <?
                if ($NumResults === 0) {
                    // not viewing bookmarks but no requests found
                ?>
                    <tr class="rowb">
                        <td colspan="8">
                            Nothing found!
                        </td>
                    </tr>
                <?      } elseif ($Page === 0) { ?>
                    <tr class="rowb">
                        <td colspan="8">
                            The requested page contains no matches!
                        </td>
                    </tr>
                    <?
                } else {

                    $TimeCompare = 1267643718; // Requests v2 was implemented 2010-03-03 20:15:18
                    $Requests = Requests::get_requests(array_keys($SphRequests));
                    foreach ($Requests as $RequestID => $Request) {
                        $SphRequest = $SphRequests[$RequestID];
                        $Bounty = $SphRequest['bounty'] * 1024; // Sphinx stores bounty in kB
                        $VoteCount = $SphRequest['votes'];

                        if ($Request['CategoryID'] == 0) {
                            $CategoryName = 'Unknown';
                        } else {
                            $CategoryName = $Categories[$Request['CategoryID'] - 1];
                        }

                        if ($Request['TorrentID'] != 0) {
                            $IsFilled = true;
                            $FillerInfo = Users::user_info($Request['FillerID']);
                        } else {
                            $IsFilled = false;
                        }

                        $ArtistForm = Requests::get_artists($RequestID);
                        $ArtistLink = Artists::display_artists($ArtistForm, true, false);
                        $RequestName = Torrents::torrent_group_name($Request, true);
                        $FullName = "<a href=\"requests.php?action=view&amp;id=$RequestID\">$RequestName</a>";
                        $Tags = $Request['Tags'];
                    ?>
                        <tr class="row<?= ($i % 2 ? 'b' : 'a') ?>">
                            <td>
                                <?= $FullName ?>
                                <div class="torrent_info">
                                    <?
                                    ?>
                                    <?= str_replace('|', ', ', $Request['CodecList']) . ' / ' . str_replace('|', ', ', $Request['SourceList']) . ' / ' . str_replace('|', ', ', $Request['ResolutionList']) . ' / ' . str_replace('|', ', ', $Request['ContainerList']) ?>
                                </div>
                            </td>
                            <td class="m_td_right nobr">
                                <span id="vote_count_<?= $RequestID ?>"><?= number_format($VoteCount) ?></span>
                                <? if (!$IsFilled && check_perms('site_vote')) { ?>
                                    &nbsp;&nbsp; <a href="javascript:Vote(0, <?= $RequestID ?>)" class="brackets"><strong>+</strong></a>
                                <?      } ?>
                            </td>
                            <td class="m_td_right number_column nobr">
                                <?= Format::get_size($Bounty) ?>
                            </td>
                            <td class="m_hidden nobr">
                                <? if ($IsFilled) { ?>
                                    <a href="torrents.php?<?= (strtotime($Request['TimeFilled']) < $TimeCompare ? 'id=' : 'torrentid=') . $Request['TorrentID'] ?>"><strong><?= time_diff($Request['TimeFilled'], 1) ?></strong></a>
                                <?      } else { ?>
                                    <strong>No</strong>
                                <?      } ?>
                            </td>
                            <td>
                                <? if ($IsFilled) { ?>
                                    <a href="user.php?id=<?= $FillerInfo['ID'] ?>"><?= $FillerInfo['Username'] ?></a>
                                <?      } else { ?>
                                    &mdash;
                                <?      } ?>
                            </td>
                            <td>
                                <a href="user.php?id=<?= $Request['UserID'] ?>"><?= Users::format_username($Request['UserID'], false, false, false) ?></a>
                            </td>
                            <td class="nobr">
                                <?= time_diff($Request['TimeAdded'], 1) ?>
                            </td>
                            <td class="nobr">
                                <?= time_diff($Request['LastVote'], 1) ?>
                            </td>
                        </tr>
            <?
                    } // foreach
                } // else
            } // if ($BookmarkView && $NumResults < 1)
            ?>
            </table>
        </div>
        <? if (isset($PageLinks)) { ?>
            <div class="linkbox">
                <?= $PageLinks ?>
            </div>
        <? } ?>
</div>
<? View::show_footer(); ?>