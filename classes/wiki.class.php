<?
class Wiki {
    /**
     * Normalize an alias
     * @param string $str
     * @return string
     */
    public static function normalize_alias($str) {
        return trim(substr(preg_replace('/^(\xE2\x80\x8E)+/', '', strtolower(htmlentities($str))), 0, 50));
    }

    /**
     * Get all aliases in an associative array of Alias => ArticleID
     * @return array
     */
    public static function get_aliases() {
        $Aliases = G::$Cache->get_value('wiki_aliases');
        if (!$Aliases) {
            $QueryID = G::$DB->get_query_id();
            G::$DB->query("
				SELECT Alias, ArticleID
				FROM wiki_aliases");
            $Aliases = G::$DB->to_pair('Alias', 'ArticleID');
            G::$DB->set_query_id($QueryID);
            G::$Cache->cache_value('wiki_aliases', $Aliases, 3600 * 24 * 14); // 2 weeks
        }
        return $Aliases;
    }

    public static function unicode_decode($unistr) {
        $arr = preg_split("/(&#[0-9]*;)/", $unistr, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        $restr = '';
        foreach ($arr as $key => $value) {
            if (strstr($value, '&#')) {
                $unistr = '';
                $arruni = explode('&#', $value);
                $arruni = substr($arruni[1], 0, strlen($arruni[1]) - 1);
                $temp = intval($arruni);
                $unistr .= ($temp < 256) ? chr(0) . chr($temp) : chr($temp / 256) . chr($temp % 256);
                $restr .= iconv('UCS-2BE', 'UTF-8', $unistr);
            } else {
                $restr .= $value;
            }
        }
        return $restr;
    }

    /**
     * Flush the alias cache. Call this whenever you touch the wiki_aliases table.
     */
    public static function flush_aliases() {
        G::$Cache->delete_value('wiki_aliases');
    }

    /**
     * Get the ArticleID corresponding to an alias
     * @param string $Alias
     * @return int
     */
    public static function alias_to_id($Alias) {
        $Aliases = self::get_aliases();
        $Alias = self::normalize_alias($Alias);
        $Aliases2 = array();
        foreach ($Aliases as $key => $value) {
            $Aliases2[self::unicode_decode($key)] = $value;
        }
        if (!isset($Aliases2[$Alias])) {
            return false;
        } else {
            return (int)$Aliases2[$Alias];
        }
    }

    /**
     * Get an article; returns false on error if $Error = false
     * @param int $ArticleID
     * @param bool $Error
     * @return array|bool
     */
    public static function get_article($ArticleID, $Error = true) {
        $Contents = G::$Cache->get_value('wiki_article_' . $ArticleID);
        if (!$Contents) {
            $QueryID = G::$DB->get_query_id();
            G::$DB->query("
				SELECT
					w.Revision,
					w.Title,
					w.Body,
					w.MinClassRead,
					w.MinClassEdit,
					w.Date,
					w.Author,
					u.Username,
					GROUP_CONCAT(a.Alias),
					GROUP_CONCAT(a.UserID),
					w.Lang,
					w.Father
				FROM wiki_articles AS w
					LEFT JOIN wiki_aliases AS a ON w.ID=a.ArticleID
					LEFT JOIN users_main AS u ON u.ID=w.Author
				WHERE w.ID='$ArticleID'
				GROUP BY w.ID");
            if (!G::$DB->has_results()) {
                if ($Error) {
                    error(404);
                } else {
                    return false;
                }
            }
            $Contents = G::$DB->to_array();
            G::$DB->set_query_id($QueryID);
            G::$Cache->cache_value('wiki_article_' . $ArticleID, $Contents, 3600 * 24 * 14); // 2 weeks
        }
        return $Contents;
    }

    /**
     * Flush an article's cache. Call this whenever you edited a wiki article or its aliases.
     * @param int $ArticleID
     */
    public static function flush_article($ArticleID) {
        G::$Cache->delete_value('wiki_article_' . $ArticleID);
    }
}
