<?

class Top10View {

    public static function render_linkbox($Selected) {
?>
        <div class="linkbox">
            <a href="top10.php?type=torrents" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'torrents'), $Selected == "torrents") ?></a>
            <!-- <a href="top10.php?type=lastfm" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'last_fm'), $Selected == "lastfm") ?></a> -->
            <a href="top10.php?type=users" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'users'), $Selected == "users") ?></a>
            <a href="top10.php?type=tags" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'tags'), $Selected == "tags") ?></a>
            <a href="top10.php?type=votes" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'favorites'), $Selected == "votes") ?></a>
            <a href="top10.php?type=donors" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'donors'), $Selected == "donors") ?></a>

        </div>
    <?
    }

    public static function render_artist_links($Selected, $View) {
    ?>
        <div class="center">
            <a href="top10.php?type=lastfm&amp;category=weekly&amp;view=<?= $View ?>" class="brackets tooltip" title="<?= Lang::get('top10', 'most_listeners_artists_week') ?>"><?= self::get_selected_link(Lang::get('top10', 'weekly_artists'), $Selected == "weekly") ?></a>
            <a href="top10.php?type=lastfm&amp;category=hyped&amp;view=<?= $View ?>" class="brackets tooltip" title="<?= Lang::get('top10', 'fastest_rising_artists_week') ?>"><?= self::get_selected_link(Lang::get('top10', 'hyped_artists'), $Selected == "hyped") ?></a>

        </div>
    <?
    }

    public static function render_artist_controls($Selected, $View) {
    ?>
        <div class="center">
            <a href="top10.php?type=lastfm&amp;category=<?= $Selected ?>&amp;view=tiles" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'tiles'), $View == "tiles") ?></a>
            <a href="top10.php?type=lastfm&amp;category=<?= $Selected ?>&amp;view=list" class="brackets"><?= self::get_selected_link(Lang::get('top10', 'list'), $View == "list") ?></a>
        </div>
        <?
    }

    private static function get_selected_link($String, $Selected) {
        if ($Selected) {
            return "<strong>$String</strong>";
        } else {
            return $String;
        }
    }

    public static function render_artist_tile($Artist, $Category) {
        if (self::is_valid_artist($Artist)) {
            switch ($Category) {
                case 'weekly':
                case 'hyped':
                    self::render_tile("artist.php?artistname=", $Artist['name'], $Artist['image'][3]['#text']);
                    break;
                default:
                    break;
            }
        }
    }

    private static function render_tile($Url, $Name, $Image) {
        if (!empty($Image)) {
            $Name = display_str($Name);
        ?>
            <li>
                <a href="<?= $Url ?><?= $Name ?>">
                    <img class="tooltip large_tile" alt="<?= $Name ?>" title="<?= $Name ?>" src="<?= ImageTools::process($Image) ?>" />
                </a>
            </li>
        <?
        }
    }


    public static function render_artist_list($Artist, $Category) {
        if (self::is_valid_artist($Artist)) {
            switch ($Category) {

                case 'weekly':
                case 'hyped':
                    self::render_list("artist.php?artistname=", $Artist['name'], $Artist['image'][3]['#text']);
                    break;
                default:
                    break;
            }
        }
    }

    private static function render_list($Url, $Name, $Image) {
        if (!empty($Image)) {
            $UseTooltipster = !isset(G::$LoggedUser['Tooltipster']) || G::$LoggedUser['Tooltipster'];
            $Image = ImageTools::process($Image);
            $Title = "title=\"&lt;img class=&quot;large_tile&quot; src=&quot;$Image&quot; alt=&quot;&quot; /&gt;\"";
            $Name = display_str($Name);
        ?>
            <li>
                <a class="tooltip_image" data-title-plain="<?= $Name ?>" <?= $Title ?> href="<?= $Url ?><?= $Name ?>"><?= $Name ?></a>
            </li>
<?
        }
    }

    private static function is_valid_artist($Artist) {
        return $Artist['name'] != '[unknown]';
    }
}
