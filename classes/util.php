<?
// This is a file of miscellaneous functions that are called so damn often
// that it'd just be annoying to stick them in namespaces.

use Gazelle\Util\{Type, Time};

/**
 * Return true if the given string is an integer. The original Gazelle developers
 * must have thought the only numbers out there were integers when naming this function.
 *
 * @param mixed $Str
 * @return bool
 */
if (PHP_INT_SIZE === 4) {
    function is_number($Str) {
        if ($Str === null || $Str === '') {
            return false;
        }
        if (is_int($Str)) {
            return true;
        }
        if ($Str[0] == '-' || $Str[0] == '+') { // Leading plus/minus signs are ok
            $Str[0] = 0;
        }
        return ltrim($Str, "0..9") === '';
    }
} else {
    function is_number($Str) {
        return Type::isInteger($Str);
    }
}

function is_date($Date) {
    return Time::isValidDate($Date);
}

/**
 * Check that some given variables (usually in _GET or _POST) are numbers
 *
 * @param array $Base array that's supposed to contain all keys to check
 * @param array $Keys list of keys to check
 * @param mixed $Error error code or string to pass to the error() function if a key isn't numeric
 */
function assert_numbers(&$Base, $Keys, $Error = 0) {
    // make sure both arguments are arrays
    if (!is_array($Base) || !is_array($Keys)) {
        return;
    }
    foreach ($Keys as $Key) {
        if (!isset($Base[$Key]) || !is_number($Base[$Key])) {
            error($Error);
        }
    }
}

/**
 * Return true, false or null, depending on the input value's "truthiness" or "non-truthiness"
 *
 * @param $Value the input value to check for truthiness
 * @return true if $Value is "truthy", false if it is "non-truthy" or null if $Value was not
 *         a bool-like value
 */
function is_bool_value($Value) {
    return Type::isBoolValue($Value);
}

/**
 * HTML-escape a string for output.
 * This is preferable to htmlspecialchars because it doesn't screw up upon a double escape.
 *
 * @param string $Str
 * @return string escaped string.
 */
function display_str($Str) {
    if ($Str === null || $Str === false || is_array($Str)) {
        return '';
    }
    if ($Str != '' && !is_number($Str)) {
        $Str = Format::make_utf8($Str);
        $Str = mb_convert_encoding($Str, 'HTML-ENTITIES', 'UTF-8');
        $Str = preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,6};)/m", '&amp;', $Str);

        $Replace = array(
            "'", '"', "<", ">",
            '&#128;', '&#130;', '&#131;', '&#132;', '&#133;', '&#134;', '&#135;', '&#136;',
            '&#137;', '&#138;', '&#139;', '&#140;', '&#142;', '&#145;', '&#146;', '&#147;',
            '&#148;', '&#149;', '&#150;', '&#151;', '&#152;', '&#153;', '&#154;', '&#155;',
            '&#156;', '&#158;', '&#159;'
        );

        $With = array(
            '&#39;', '&quot;', '&lt;', '&gt;',
            '&#8364;', '&#8218;', '&#402;', '&#8222;', '&#8230;', '&#8224;', '&#8225;', '&#710;',
            '&#8240;', '&#352;', '&#8249;', '&#338;', '&#381;', '&#8216;', '&#8217;', '&#8220;',
            '&#8221;', '&#8226;', '&#8211;', '&#8212;', '&#732;', '&#8482;', '&#353;', '&#8250;',
            '&#339;', '&#382;', '&#376;'
        );

        $Str = str_replace($Replace, $With, $Str);
    }
    return $Str;
}


/**
 * Send a message to an IRC bot listening on SOCKET_LISTEN_PORT
 *
 * @param string $Raw An IRC protocol snippet to send.
 */
function send_irc($Raw) {
    if (defined('DISABLE_IRC') && DISABLE_IRC === true) {
        return;
    }
    $IRCSocket = fsockopen(SOCKET_LISTEN_ADDRESS, SOCKET_LISTEN_PORT);
    $Raw = str_replace(array("\n", "\r"), '', $Raw);
    fwrite($IRCSocket, $Raw);
    fclose($IRCSocket);
}


/**
 * Display a critical error and kills the page.
 *
 * @param string $Error Error type. Automatically supported:
 *  403, 404, 0 (invalid input), -1 (invalid request)
 *  If you use your own string for Error, it becomes the error description.
 * @param boolean $NoHTML If true, the header/footer won't be shown, just the description.
 * @param string $Log If true, the user is given a link to search $Log in the site log.
 */
function error($Error, $NoHTML = false, $Log = false, $Title = false) {
    global $Debug;
    require(SERVER_ROOT . '/sections/error/index.php');
    $Debug->profile();
    die();
}


/**
 * Convenience function for check_perms within Permissions class.
 *
 * @see Permissions::check_perms()
 *
 * @param string $PermissionName
 * @param int $MinClass
 * @return bool
 */
function check_perms($PermissionName, $MinClass = 0) {
    return Permissions::check_perms($PermissionName, $MinClass);
}


function is_dev() {
    if (getenv('ENV') == 'development') {
        return true;
    }
    return false;
}

function static_prefix() {
    if (is_dev()) {
        return "/src";
    }
    return "/public";
}

function ajax_json_success($Message, $Flags = 0) {
    print json_encode(array('status' => "success", 'response' => $Message), $Flags);
    die();
}

function ajax_json_error($Message = "") {
    print json_encode(array('status' => "failure", 'error' => $Message));
    die();
}

function ajax_json_error_with_code($Code, $Message = "") {
    print json_encode(array('status' => "failure", 'code' => $Code, 'error' => $Message));
    die();
}

function json_error($Code) {
    echo json_encode(add_json_info(['status' => 'failure', 'error' => $Code, 'response' => []]));
    die();
}

function add_json_info($Json) {
    if (!isset($Json['info'])) {
        $Json = array_merge($Json, [
            'info' => [
                'source' => SITE_NAME,
                'version' => 1,
            ],
        ]);
    }
    if (!isset($Json['debug']) && check_perms('site_debug')) {
        /** @var DEBUG $Debug */
        global $Debug;
        $Json = array_merge($Json, [
            'debug' => [
                'queries' => $Debug->get_queries(),
                'searches' => $Debug->get_sphinxql_queries()
            ],
        ]);
    }
    return $Json;
}


/**
 * Print the site's URL including the appropriate URI scheme, including the trailing slash
 *
 * @return string for site
 */
function site_url($Slash = true) {
    if ($Slash) {
        return SITE_URL . '/';
    }
    return SITE_URL;
}

/**
 * The text of the pop-up confirmation when burning an FL token.
 *
 * @param integer $seeders - number of seeders for the torrent
 * @return string Warns if there are no seeders on the torrent
 */
function FL_confirmation_msg($seeders, $Size) {
    /* Coder Beware: this text is emitted as part of a Javascript single quoted string.
     * Any apostrophes should be avoided or escaped appropriately (with \\').
     */
    $TokenUses = ceil($Size / (5 * 1024 * 1024 * 1024));
    $FTStr = "$TokenUses" . Lang::get('global', 'n_fl_token') . ($TokenUses > 1 ? Lang::get('global', 's') : "");
    return ($seeders == 0)
        ? Lang::get('global', 'not_seeded_sure_use_fl_before') . "$FTStr" . Lang::get('global', 'not_seeded_sure_use_fl_after')
        : Lang::get('global', 'sure_use_fl_before') . "$FTStr" . Lang::get('global', 'sure_use_fl_after');
}

/**
 * Utility function that unserializes an array, and then if the unserialization fails,
 * it'll then return an empty array instead of a null or false which will break downstream
 * things that require an incoming array
 *
 * @param string $array
 * @return array
 */
function unserialize_array($array) {
    $array = empty($array) ? array() : unserialize($array);
    return (empty($array)) ? array() : $array;
}

/**
 * Utility function for determining if checkbox should be checked if some $value is set or not
 * @param $value
 * @return string
 */
function isset_array_checked($array, $value) {
    return (isset($array[$value])) ? "checked" : "";
}

/**
 * Helper function to return an string of N elements from an array.
 *
 * (e.g. [2, 4, 6] into a list of query placeholders (e.g. '?,?,?')
 * By default '?' is used, but a custom placeholder may be specified,
 * such as '(?)' or '(?, now(), 100)', for use in a bulk insert.
 *
 * @param array $list The list of elements
 * @param string $placeholder ('?' by default).
 * @return string The resulting placeholder string.
 */
function placeholders(array $list, $placeholder = '?') {
    return implode(',', array_fill(0, count($list), $placeholder));
}

/**
 * Awful anglo-centric hack for handling plurals ;-)
 *
 * @param int $n the number
 * @return string '' if 1, otherwise 's'
 */
function plural(int $n) {
    return $n == 1 ? '' : 's';
}
