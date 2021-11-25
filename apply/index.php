<?PHP
define('FROM_INDEX', true);

require('function.php');
require('../classes/config.php');
require(SERVER_ROOT . '/classes/misc.class.php');
require(SERVER_ROOT . '/classes/lang.class.php');
require(SERVER_ROOT . '/classes/mysql.class.php');
$DB = new DB_MYSQL;
//时间检查
if (time() < strtotime(OPEN_APPLY_FROM) || time() > strtotime(OPEN_QUERY_TO)) {
    header('HTTP/1.1 403 Forbidden');
    die();
} else if (time() > strtotime(OPEN_APPLY_TO) && time() < strtotime(OPEN_QUERY_TO)) {
    if (!isset($_REQUEST['action']) || $_REQUEST['action'] != 'takestatus') $_REQUEST['action'] = 'status';
}
//applykey 检查
if (isset($_REQUEST['applykey'])) {
    setcookie('applykey', $_REQUEST['applykey'], time() + 60 * 60 * 24 * 365, '/');
    $ApplyKey = db_string($_REQUEST['applykey']);
} else if (isset($_COOKIE['applykey'])) {
    $ApplyKey = db_string($_COOKIE['applykey']);
} else {
    header('HTTP/1.1 403 Forbidden');
    die();
}
$DB->query("SELECT Email, Used, IP from register_apply_link where applykey='$ApplyKey'");
if (!$DB->record_count()) {
    header('HTTP/1.1 403 Forbidden');
    die();
}
list($ApplyEmail, $ApplyUsed, $ApplyIP) = $DB->next_record(MYSQLI_BOTH, false);

if (isset($_REQUEST['action'])) {
    if ($ApplyUsed) {
        if ($_REQUEST['action'] == 'takeapply' || $_REQUEST['action'] == 'apply') {
            $_REQUEST['action'] = 'status';
        }
    }
} else {
    if ($ApplyUsed) {
        $_REQUEST['action'] = 'status';
    } else {
        $_REQUEST['action'] = 'apply';
    }
}

switch ($_REQUEST['action']) {
    case 'takeapply':
        if (getRealIp() == $ApplyIP) {
            include('takeapply.php');
        } else {
            errPage('ip_not_eq');
        }
        break;
    case 'takestatus':
        include('takestatus.php');
        break;
    case 'status':
        include('status.php');
        break;
    case 'apply':
        if (getRealIp() == $ApplyIP) {
            include('apply.php');
        } else {
            errPage('ip_not_eq');
        }
        break;
    default:
        header('HTTP/1.1 403 Forbidden');
        die();
}
