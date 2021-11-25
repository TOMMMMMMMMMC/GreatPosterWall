<?
fromIndex();
function getRealIp() {
    static $realip;
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $realip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $realip = $_SERVER['REMOTE_ADDR'];
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $realip = getenv('HTTP_X_FORWARDED_FOR');
        } else if (getenv('HTTP_CLIENT_IP')) {
            $realip = getenv('HTTP_CLIENT_IP');
        } else {
            $realip = getenv('REMOTE_ADDR');
        }
    }
    if (filter_var($realip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE)) {
        return $realip;
    } elseif (filter_var($realip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
        return $realip;
    } else {
        return -1;
    }
}

function isEmail($email) {
    $mode = "/^([a-z0-9]*[-_.]?[a-z0-9]+)*@([a-z0-9]*[-_]?[a-z0-9]+)+[.][a-z]{2,3}([.][a-z]{3})?$/i";
    if (preg_match($mode, $email)) {
        return true;
    } else {
        return false;
    }
}

function isURL($url) {
    $mode = "/\b(?:(?:https?):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
    if (preg_match($mode, $url)) {
        return true;
    } else {
        return false;
    }
}

function genPassword($length = 8) {
    $chars = 'abcdefghijklmnopqrstuvwxyz123456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $chars[mt_rand(0, strlen($chars) - 1)];
    }
    return $password;
}

function Reindex() {
    header("Location: ../index.php");
}

function send_irc($Raw) {
    return;
}

function fromIndex() {
    if (!defined('FROM_INDEX')) {
        header('HTTP/1.1 403 Forbidden');
        die();
    }
}
function display_str($Str) {
    if ($Str != '') {
        $Str = make_utf8($Str);
        $Str = mb_convert_encoding($Str, 'HTML-ENTITIES', 'UTF-8');
        $Str = preg_replace("/&(?![A-Za-z]{0,4}\w{2,3};|#[0-9]{2,5};)/m", '&amp;', $Str);

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
function make_utf8($Str) {
    if ($Str != '') {
        if (is_utf8($Str)) {
            $Encoding = 'UTF-8';
        }
        if (empty($Encoding)) {
            $Encoding = mb_detect_encoding($Str, 'UTF-8, ISO-8859-1');
        }
        if (empty($Encoding)) {
            $Encoding = 'ISO-8859-1';
        }
        if ($Encoding == 'UTF-8') {
            return $Str;
        } else {
            return @mb_convert_encoding($Str, 'UTF-8', $Encoding);
        }
    }
}
function is_utf8($Str) {
    return preg_match(
        '%^(?:
		[\x09\x0A\x0D\x20-\x7E]			 // ASCII
		| [\xC2-\xDF][\x80-\xBF]			// non-overlong 2-byte
		| \xE0[\xA0-\xBF][\x80-\xBF]		// excluding overlongs
		| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} // straight 3-byte
		| \xED[\x80-\x9F][\x80-\xBF]		// excluding surrogates
		| \xF0[\x90-\xBF][\x80-\xBF]{2}	 // planes 1-3
		| [\xF1-\xF3][\x80-\xBF]{3}		 // planes 4-15
		| \xF4[\x80-\x8F][\x80-\xBF]{2}	 // plane 16
		)*$%xs',
        $Str
    );
}
function errPage($Label) {
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= Lang::get('pub', 'apply_to_join') ?> :: GreatPosterWall</title>
        <link rel="stylesheet" href="style/join.css" type="text/css" />
        <script type="text/javascript" src="scripts/gen_validatorv31.js"></script>
        <script src="scripts/lang.js" type="text/javascript"></script>
        <link rel="STYLESHEET" type="text/css" href="style/pwdwidget.css" />
        <script src="scripts/pwdwidget.js" type="text/javascript"></script>
    </head>

    <body>
        <div id="head">
            <select name="language" id="language" onchange="change_lang(this.options[this.options.selectedIndex].value)">
                <option value="chs" <? if (empty($_COOKIE['lang']) || $_COOKIE['lang'] == 'chs') { ?>selected<? } ?>>简体中文</option>
                <option value="en" <? if (!empty($_COOKIE['lang']) && $_COOKIE['lang'] == 'en') { ?>selected<? } ?>>English</option>
            </select>
        </div>
        <!-- Form Code Start -->
        <div id="container">
            <header>
                <div id="header">
                    <img id="logo" src="https://greatposterwall.com/static/styles/public/images/loginlogo.png" />
                    <h1 id="web-title"><?= Lang::get('pub', 'apply_to_join') ?></h1>
                </div>
            </header>
            <div id="main">
                <div class="container">
                    <div class="card" id="status-feedback-warning">
                        <p><?= Lang::get('pub', $Label) ?></p>
                    </div>
                </div>
                <? include('footer.php'); ?>
            </div>
    </body>

    </html>
<?
    die();
}
