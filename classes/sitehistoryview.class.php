<?

class SiteHistoryView {

    public static function render_linkbox() {
        if (check_perms('users_mod')) {
?>
            <div class="linkbox">
                <a href="sitehistory.php?action=edit" class="brackets"><?= Lang::get('sitehistory', 'create_new_event') ?></a>
            </div>
        <?
        }
    }

    public static function render_events($Events) {
        $Categories = SiteHistory::get_categories();
        $SubCategories = SiteHistory::get_sub_categories();
        $CanEdit = check_perms('users_mod');
        foreach ($Events as $Event) {
        ?>
            <div class="box sitehistory_event_container">
                <div class="head colhead_dark">
                    <div class="title">
                        <? if ($CanEdit) { ?>
                            <a class="brackets" href="sitehistory.php?action=edit&amp;id=<?= $Event['ID'] ?>"><?= Lang::get('global', 'edit') ?></a>
                        <?          } ?>

                        <span><?= date('F d, Y', strtotime($Event['Date'])); ?>
                            -</span>
                        <a href="sitehistory.php?action=search&amp;category=<?= $Event['Category'] ?>" class="brackets"><?= $Categories[$Event['Category']] ?></a>
                        <a href="sitehistory.php?action=search&amp;subcategory=<?= $Event['SubCategory'] ?>" class="brackets"><?= $SubCategories[$Event['SubCategory']] ?></a>

                        <? if (!empty($Event['Url'])) { ?>
                            <span><a href="<?= $Event['Url'] ?>"><?= $Event['Title'] ?></a></span>
                        <?          } else { ?>
                            <span><?= $Event['Title'] ?></span>
                        <?          } ?>
                    </div>
                    <div class="tags">
                        <? self::render_tags($Event['Tags']) ?>
                    </div>
                </div>
                <? if (!empty($Event['Body'])) { ?>
                    <div class="body pad">
                        <?= Text::full_format($Event['Body']) ?>
                    </div>
                <?          } ?>
            </div>
        <?
        }
    }

    private static function render_tags($Tags) {
        $Tags = explode(',', $Tags);
        natcasesort($Tags);
        $FormattedTags = '';
        foreach ($Tags as $Tag) {
            $FormattedTags .= "<a href=\"sitehistory.php?action=search&amp;tags=$Tag\">$Tag" . "</a>, ";
        }
        echo rtrim($FormattedTags, ', ');
    }

    public static function render_months($Months) { ?>
        <div class="box">
            <div class="head"><?= Lang::get('sitehistory', 'calendar') ?></div>
            <div class="pad">
                <?
                $Year = "";
                foreach ($Months as $Month) {
                    if ($Month['Year'] != $Year) {
                        $Year = $Month['Year'];
                        echo "<h2>$Year</h2>";
                    }
                ?>
                    <a style="margin-left: 5px;" href="sitehistory.php?month=<?= $Month['Month'] ?>&amp;year=<?= $Month['Year'] ?>"><?= $Month['MonthName'] ?></a>
                <?      } ?>
            </div>
        </div>
    <?
    }

    public static function render_search() { ?>
        <div class="box">
            <div class="head"><?= Lang::get('sitehistory', 'search') ?></div>
            <div class="pad">
                <form class="search_form" action="sitehistory.php" method="get">
                    <input type="hidden" name="action" value="search" />
                    <input type="text" id="title" name="title" size="20" placeholder="<?= Lang::get('sitehistory', 'title') ?>" />
                    <br />
                    <input type="text" id="tags" name="tags" size="20" placeholder="<?= Lang::get('sitehistory', 'comma_separated_tags') ?>" />
                    <br />
                    <select name="category" id="category">
                        <option value="0"><?= Lang::get('sitehistory', 'choose_a_category') ?></option>
                        <?
                        $Categories = SiteHistory::get_categories();
                        foreach ($Categories as $Key => $Value) {
                        ?>
                            <option<?= $Key == $Event['Category'] ? ' selected="selected"' : '' ?> value="<?= $Key ?>"><?= $Value ?></option>
                            <?          } ?>
                    </select>
                    <br />
                    <select name="subcategory">
                        <option value="0"><?= Lang::get('sitehistory', 'choose_a_subcategory') ?></option>
                        <?
                        $SubCategories = SiteHistory::get_sub_categories();
                        foreach ($SubCategories as $Key => $Value) {
                        ?>
                            <option<?= $Key == $Event['SubCategory'] ? ' selected="selected"' : '' ?> value="<?= $Key ?>"><?= $Value ?></option>
                            <?          } ?>
                    </select>
                    <br />
                    <input value="Search" type="submit" />
                </form>
            </div>
        </div>
    <?  }

    public static function render_edit_form($Event) { ?>
        <form id="event_form" method="post" action="">
            <? if ($Event) { ?>
                <input type="hidden" name="action" value="take_edit" />
                <input type="hidden" name="id" value="<?= $Event['ID'] ?>" />
            <?      } else { ?>
                <input type="hidden" name="action" value="take_create" />
            <?      } ?>
            <input type="hidden" name="auth" value="<?= G::$LoggedUser['AuthKey'] ?>" />
            <table id="sitehistory_event_create_table" cellpadding="6" cellspacing="1" border="0" class="layout border" width="100%">
                <tr>
                    <td class="label"><?= Lang::get('sitehistory', 'title') ?>:</td>
                    <td>
                        <input type="text" id="title" name="title" size="50" class="required" value="<?= $Event['Title'] ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('sitehistory', 'link') ?>:</td>
                    <td>
                        <input type="text" id="url" name="url" size="50" value="<?= $Event['Url'] ?>" />
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('sitehistory', 'date') ?>:</td>
                    <td>
                        <input type="date" id="date" name="date" class="required" <?= $Event ? ' value="' . date('Y-m-d', strtotime($Event['Date'])) . '"' : '' ?> />
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('sitehistory', 'category') ?>:</td>
                    <td>
                        <select id="category" name="category" class="required">
                            <option value="0"><?= Lang::get('sitehistory', 'choose_a_category') ?></option>
                            <?
                            $Categories = SiteHistory::get_categories();
                            foreach ($Categories as $Key => $Value) {
                            ?>
                                <option<?= $Key == $Event['Category'] ? ' selected="selected"' : '' ?> value="<?= $Key ?>"><?= $Value ?></option>
                                <?      } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('sitehistory', 'subcategory') ?>:</td>
                    <td>
                        <select id="category" name="sub_category" class="required">
                            <option value="0"><?= Lang::get('sitehistory', 'choose_a_subcategory') ?></option>
                            <? $SubCategories = SiteHistory::get_sub_categories();
                            foreach ($SubCategories as $Key => $Value) { ?>
                                <option<?= $Key == $Event['SubCategory'] ? ' selected="selected"' : '' ?> value="<?= $Key ?>"><?= $Value ?></option>
                                <?      } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('sitehistory', 'tags') ?>:</td>
                    <td>
                        <input type="text" id="tags" name="tags" placeholder="<?= Lang::get('sitehistory', 'tags_placeholder') ?>" size="50" value="<?= $Event['Tags'] ?>" />
                        <select id="tag_list">
                            <option><?= Lang::get('sitehistory', 'choose_tags') ?></option>
                            <?
                            $Tags = SiteHistory::get_tags();
                            foreach ($Tags as $Tag) {
                            ?>
                                <option><?= $Tag ?></option>
                            <?      } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?= Lang::get('sitehistory', 'body') ?>:</td>
                    <td>
                        <textarea id="body" name="body" cols="90" rows="8" tabindex="1" onkeyup="resize('body');"><?= $Event['Body'] ?></textarea>
                    </td>
                </tr>
            </table>
            <div style="text-align: center;">
                <input type="submit" name="submit" value="Submit" />
                <? if ($Event) { ?>
                    <input type="submit" name="delete" value="Delete" />
                <?      } ?>
            </div>
        </form>
    <?
    }

    public static function render_recent_sidebar($Events) { ?>
        <div class="caidan artbox">
            <div class="sec-title">
                <span><a href="sitehistory.php"><?= Lang::get('sitehistory', 'latest_site_history') ?></a></span>
            </div>
            <ul class="stats nobullet">
                <?
                $Categories = SiteHistory::get_categories();
                $i = 0;
                foreach ($Events as $Event) {
                ?>
                    <li>
                        <? if (!empty($Event['Url'])) { ?><?= ++$i ?>.
                        <a href="<?= $Event['Url'] ?>"><?= $Event['Title'] ?></a>
                    <?          } else { ?>
                        <?= ++$i . ". " . $Event['Title'] ?>
                    <?          } ?>
                    </li>
                <?      } ?>
            </ul>
        </div>
<?
    }
}
