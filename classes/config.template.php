<?php
if (PHP_VERSION_ID < 50400) {
    die("Gazelle requires PHP 5.4 or later to function properly");
}
date_default_timezone_set('UTC');

// Main settings
define('SITE_NAME', ''); //The name of your site
define('SITE_HOST', ''); // The host for your site (e.g. localhost, orpheus.network)
define('SITE_URL', ''); // The base URL to access the site (e.g. http://localhost:8080, https://orpheus.network)
define('MAIL_HOST', 'greatposterwall.com'); // The host to use for mail delivery (e.g. mail.orpheus.network)
define('SERVER_ROOT', '/path'); //The root of the server, used for includes, purpose is to shorten the path string
define('ANNOUNCE_HTTP_URL', 'http://' . SITE_HOST . ':2710'); //Announce HTTP URL
define('ANNOUNCE_HTTPS_URL', 'http://' . SITE_HOST . ':2710'); //Announce HTTPS URL

define('TORRENT_SOURCE', 'GPW-DEV');
define('IMAGE_URL', "http://bineawang.synology.me:6500");

// Allows you to run static content off another server. Default is usually what you want.
define('STATIC_SERVER', 'static/');

// Keys
define('ENCKEY', ''); //Random key. The key for encryption
define('SITE_SALT', ''); //Random key. Default site wide salt for passwords, DO NOT LEAVE THIS BLANK/CHANGE AFTER LAUNCH!
define('SCHEDULE_KEY', ''); // Random key. This key must be the argument to schedule.php for the schedule to work.
define('RSS_HASH', ''); //Random key. Used for generating unique RSS auth key.

// MySQL details
define('SQLHOST', 'localhost'); //The MySQL host ip/fqdn
define('SQLLOGIN', ''); //The MySQL login
define('SQLPASS', ''); //The MySQL password
define('SQLDB', 'gazelle'); //The MySQL database to use
define('SQLPORT', 3306); //The MySQL port to connect on
define('SQLSOCK', '/var/run/mysqld/mysqld.sock');

// Memcached details
$MemcachedServers = array(
    // unix sockets are fast, and other people can't telnet into them
    array('host' => 'unix:///var/run/memcached.sock', 'port' => 0, 'buckets' => 1),
);

// Sphinx details
define('SPHINX_HOST', 'localhost');
define('SPHINX_PORT', 9312);
define('SPHINXQL_HOST', '127.0.0.1');
define('SPHINXQL_PORT', 9306);
define('SPHINXQL_SOCK', false);
define('SPHINX_MAX_MATCHES', 1000); // Must be <= the server's max_matches variable (default 1000)
define('SPHINX_INDEX', 'torrents');

// Ocelot details
// define('DISABLE_TRACKER', false);
define('TRACKER_HOST', 'localhost');
define('TRACKER_PORT', 2710);
define('TRACKER_SECRET', ''); // Must be 32 characters and match site_password in Ocelot's config.cpp
define('TRACKER_REPORTKEY', ''); // Must be 32 characters and match report_password in Ocelot's config.cpp

// Site settings
define('CRYPT_HASH_PREFIX', '$2y$07$');
define('DEBUG_MODE', false); //Set to false if you dont want everyone to see debug information, can be overriden with 'site_debug'
define('DEBUG_WARNINGS', true); //Set to true if you want to see PHP warnings in the footer
define('SHOW_PUBLIC_INDEX', true); // Show the public index.php landing page
define('OPEN_REGISTRATION', true); //Set to false to disable open registration, true to allow anyone to register
define('OPEN_EXTERNAL_REFERRALS', true); //Set to false to disable external tracker referrals, true to allow them
define('USER_LIMIT', 5000); //The maximum number of users the site can have, 0 for no limit
define('REQUEST_TAX', 0.0); //Percentage Tax (0 - 1) to charge users on making requests
define('STARTING_UPLOAD', 3221225472); //Upload given to newly registered users, in bytes using IEC standard (1024 bytes per KiB)
define('STARTING_INVITES', 0); //# of invites to give to newly registered users
define('BLOCK_TOR', false); //Set to true to block Tor users
define('BLOCK_OPERA_MINI', false); //Set to true to block Opera Mini proxy
define('DONOR_INVITES', 2);
define('SYSTEM_USER_ID', 17); // ID for user to create "system" threads under (e.g. Edit Requests)
define('TRASH_FORUM_ID', 4); // ID of forum to send threads to when trash button is pressed
define('EDITING_FORUM_ID', 34); // ID of forum to send editing requests to
define('EDITING_TRASH_FORUM_ID', 48); // ID of forum to send editing threads to when trash button is pressed in EDITING_FORUM_ID
define('ENABLE_BADGE', false); // Set to enable badge system
define('ENABLE_COLLAGES', false); // Set to enable collages system
define('ENABLE_VOTES', false); // Set to enable votes system
define('ENABLE_HNR', false); // Set to enable H&R

if (!defined('FEATURE_EMAIL_REENABLE')) {
    define('FEATURE_EMAIL_REENABLE', true);
    // Email delivery method and information
    define('EMAIL_DELIVERY_TYPE', 'mailgun'); // should be either 'mailgun' to use mailgun services or 'local' to use a local SMTP server or relay
    define('MAILGUN_API_KEY', 'key-19df7169b59cc6618e871ba7c8779803');
    define('MAILGUN_API_URL', 'https://api.mailgun.net/v3/greatposterwall.com');
}

// User class IDs needed for automatic promotions. Found in the 'permissions' table
// Name of class    Class ID (NOT level)
define('ADMIN',     '1');
define('USER',      '2');
define('MEMBER',    '3');
define('POWER',     '4');
define('ELITE',     '5');
define('MOD',       '11');
define('DESIGNER',  '13');
define('CODER',     '14');
define('SYSOP',     '15');
define('ARTIST',    '19');
define('DONOR',     '20');
define('FORUM_MOD', '21');
define('TORRENT_MOD', '22');
define('FLS_TEAM',  '23');
define('DEV',      '24');
define('TORRENT_MASTER', '25');
define('VIP',       '26');
define('LEGEND',    '27');
define('GURU',  '28');
define('POWER_TM',  '29');
define('INTERVIEW', '30');
define('CELEB',     '31');
define('ELITE_TM',  '44');


// Pagination
define('TORRENT_COMMENTS_PER_PAGE', 10);
define('POSTS_PER_PAGE', 25);
define('TOPICS_PER_PAGE', 50);
define('TORRENTS_PER_PAGE', 50);
define('REQUESTS_PER_PAGE', 25);
define('MESSAGES_PER_PAGE', 25);
define('LOG_ENTRIES_PER_PAGE', 50);

// Cache catalogues
define('THREAD_CATALOGUE', 500); // Limit to THREAD_CATALOGUE posts per cache key.

// IRC settings
define('DISABLE_IRC', false);
define('BOT_NICK', '');
define('BOT_SERVER', ''); // IRC server address. Used for onsite chat tool.
define('BOT_PORT', 6667);
define('BOT_CHAN', '#' . SITE_URL);
define('BOT_ANNOUNCE_CHAN', '#');
define('BOT_STAFF_CHAN', '#');
define('BOT_DISABLED_CHAN', '#'); // Channel to refer disabled users to.
define('BOT_HELP_CHAN', '#');
define('BOT_DEBUG_CHAN', '#');
define('BOT_REPORT_CHAN', '#');
define('BOT_NICKSERV_PASS', '');
define('BOT_INVITE_CHAN', BOT_CHAN . '-invites'); // Channel for non-members seeking an interview
define('BOT_INTERVIEW_CHAN', BOT_CHAN . '-interview'); // Channel for the interviews
define('BOT_INTERVIEW_NUM', 5);
define('BOT_INTERVIEW_STAFF', BOT_CHAN . '-interviewers'); // Channel for the interviewers
define('SOCKET_LISTEN_PORT', 51010);
define('SOCKET_LISTEN_ADDRESS', 'localhost');
define('ADMIN_CHAN', '#');
define('LAB_CHAN', '#');
define('STATUS_CHAN', '#');

// Miscellaneous values
define('RANK_ONE_COST', 5);
define('RANK_TWO_COST', 10);
define('RANK_THREE_COST', 15);
define('RANK_FOUR_COST', 20);
define('RANK_FIVE_COST', 30);
define('MAX_RANK', 6);
define('MAX_SPECIAL_RANK', 5);
define('DONOR_FORUM_RANK', 6);
define('DONOR_FORUM', 70);

define('TMDB_API_KEY', '8684c0e585b87739c36ddade875ad5fd');
define('OMDB_API_KEY', 'cbcdf914');
define('DOUBAN_API_URL', 'https://gpw.tobecontinued.workers.dev/');

define('QINIU_ACCESS_KEY', 'rCmEmAe-dvm1VWOGff23y2ROhf7uUeNgYlqe8ctv');
define('QINIU_SECRET_KEY', '9TTKoT61IAmixnUyTyI9PWLa3MOg2xERaU-1AeKE');
define('QINIU_BUCKET', 'cdndemoimg');


$ForumsRevealVoters = array();
$ForumsDoublePost = array();

$Categories = array('Movies');
$GroupedCategories = array_intersect(array('Movies'), $Categories);
$CategoryIcons = array('movies.png', 'apps.png', 'ebook.png', 'audiobook.png', 'elearning.png', 'comedy.png', 'comics.png');

$Formats = array('FLAC', 'WAV', 'DSD', 'MP3', 'AAC', 'AC3', 'DTS');
$Bitrates = array('192', 'APS (VBR)', 'V2 (VBR)', 'V1 (VBR)', '256', 'APX (VBR)', 'V0 (VBR)', 'q8.x (VBR)', '320', 'Lossless', '24bit Lossless', 'Other');
$Media = array('CD', 'DVD', 'Vinyl', 'Soundboard', 'SACD', 'Blu-ray', 'DAT', 'Cassette', 'WEB', 'Unknown Media');
$Codecs = array('DivX', 'XviD', 'x264', 'H.264', 'x265', 'H.265', 'Other');
$Sources = array('VHS', 'DVD', 'HD-DVD', 'TV', 'HDTV', 'WEB', 'Blu-ray', 'Other');
$Containers = array('AVI', 'MPG', 'MP4', 'MKV', 'VOB IFO', 'ISO', 'm2ts', 'Other');
$Resolutions = array('NTSC', 'PAL', '480p', '576p', '720p', '1080i', '1080p', '2160p', 'Other');
$Processings = array("---", "Encode", "Remux", "DIY", "Untouched");
$Makers = array('DIC', 'Other');

$StandardDefinition = ['Other', 'NTSC', 'PAL', '480p', '576p'];
$HighDefinition = ['720p', '1080i', '1080p'];
$UltraDefinition = ['2160p'];
define('SUBGROUP_SD', 1);
define('SUBGROUP_HD', 2);
define('SUBGROUP_UHD', 3);
define('SUBGROUP_3D', 4);
define('SUBGROUP_Extra', 5);

define('ANNOUNCEMENT_FORUM_ID', 43);


$CollageCats = array(0 => 'Personal', 1 => 'Theme', 2 => 'Genre introduction', 3 => 'Discography', 4 => 'Label', 5 => 'Staff picks', 6 => 'Charts', 7 => 'Artists');

$MovieTypes = array(1 => 'Feature Film', 2 => 'Short Film', 3 => 'Movie Collection', 4 => '单口喜剧', 5 => '现场演出', 6 => '电影集');
$ReleaseTypes = array(1 => 'Feature Film', 2 => 'Short Film', 3 => 'Movie Collection', 4 => '单口喜剧', 5 => '现场演出', 6 => '电影集');

$ZIPGroups = array(
    0 => 'MP3 (VBR) - High Quality',
    1 => 'MP3 (VBR) - Low Quality',
    2 => 'MP3 (CBR)',
    3 => 'FLAC - Lossless',
    4 => 'Others'
);

//3D array of attributes, OptionGroup, OptionNumber, Name
$ZIPOptions = array();

// Ratio requirements, in descending order
// Columns: Download amount, required ratio, grace period
$RatioRequirements = array(
    array(50 * 1024 * 1024 * 1024, 0.60, date('Y-m-d H:i:s')),
    array(40 * 1024 * 1024 * 1024, 0.50, date('Y-m-d H:i:s')),
    array(30 * 1024 * 1024 * 1024, 0.40, date('Y-m-d H:i:s')),
    array(20 * 1024 * 1024 * 1024, 0.30, date('Y-m-d H:i:s')),
    array(10 * 1024 * 1024 * 1024, 0.20, date('Y-m-d H:i:s')),
    array(5 * 1024 * 1024 * 1024,  0.15, date('Y-m-d H:i:s', time() - (60 * 60 * 24 * 14)))
);

//Captcha fonts should be located in /classes/fonts
$CaptchaFonts = array(
    'ARIBLK.TTF',
    'IMPACT.TTF',
    'TREBUC.TTF',
    'TREBUCBD.TTF',
    'TREBUCBI.TTF',
    'TREBUCIT.TTF',
    'VERDANA.TTF',
    'VERDANAB.TTF',
    'VERDANAI.TTF',
    'VERDANAZ.TTF'
);
//Captcha images should be located in /captcha
$CaptchaBGs = array(
    'captcha1.png',
    'captcha2.png',
    'captcha3.png',
    'captcha4.png',
    'captcha5.png',
    'captcha6.png',
    'captcha7.png',
    'captcha8.png',
    'captcha9.png'
);

// Special characters, and what they should be converted to
// Used for torrent searching
$SpecialChars = array(
    '&' => 'and'
);

// array to store external site credentials and API URIs, stored in cache to keep user sessions alive
$ExternalServicesConfig = [
    "Orpheus" => [
        'type' => 'gazelle',
        'inviter_id' => 1,
        'base_url' => 'https://orpheus.network/',
        'api_path' => 'ajax.php?action=',
        'login_path' => 'login.php',
        'username' => 'foo',
        'password' => 'bar',
        'cookie' => '',
        'cookie_expiry' => 0,
        'status' => TRUE
    ],
    "VagrantGazelle" => [
        'type' => 'gazelle',
        'inviter_id' => 1,
        'base_url' => 'http://localhost:80/',
        'api_path' => 'ajax.php?action=',
        'login_path' => 'login.php',
        'username' => 'foo',
        'password' => 'bar',
        'cookie' => '',
        'cookie_expiry' => 0,
        'status' => TRUE
    ],
    "PassThePopcorn" => [
        'type' => 'gazelle',
        'inviter_id' => 1,
        'base_url' => 'https://passthepopcorn.me/',
        'api_path' => 'ajax.php?action=',
        'login_path' => 'login.php',
        'username' => 'foo',
        'password' => 'bar',
        'cookie' => '',
        'cookie_expiry' => 0,
        'status' => TRUE
    ]
];
$UserCriteria = [
    USER => [
        'From' => USER,
        'To' => MEMBER,
        'MinUpload' => 0,
        'MinDownload' => 80 * 1024 * 1024 * 1024,
        'MinRatio' => 0.8,
        'MinUploads' => 0,
        'Weeks' => 1,
        'AwardLevel' => 1,
    ],
    MEMBER => [
        'From' => MEMBER,
        'To' => POWER,
        'MinUpload' => 0,
        'MinDownload' => 200 * 1024 * 1024 * 1024,
        'MinRatio' => 1.2,
        'MinUploads' => 1,
        'Weeks' => 2,
        'AwardLevel' => 2,
        'Invite' => 1,
    ],
    POWER => [
        'From' => POWER,
        'To' => ELITE,
        'MinUpload' => 0,
        'MinDownload' => 500 * 1024 * 1024 * 1024,
        'MinRatio' => 1.2,
        'MinUploads' => 25,
        'Weeks' => 4,
        'AwardLevel' => 3,
        'Invite' => 1,
    ],
    ELITE => [
        'From' => ELITE,
        'To' => TORRENT_MASTER,
        'MinUpload' => 0,
        'MinDownload' => 1 * 1024 * 1024 * 1024 * 1024,
        'MinRatio' => 1.2,
        'MinUploads' => 100,
        'Weeks' => 8,
        'AwardLevel' => 4,
        'Invite' => 2,

    ],
    TORRENT_MASTER => [
        'From' => TORRENT_MASTER,
        'To' => POWER_TM,
        'MinUpload' => 0,
        'MinDownload' => 2 * 1024 * 1024 * 1024 * 1024,
        'MinRatio' => 1.2,
        'MinUploads' => 250,
        'Weeks' => 12,
        'AwardLevel' => 5,
        'Invite' => 2,

    ],
    POWER_TM => [
        'From' => POWER_TM,
        'To' => ELITE_TM,
        'MinUpload' => 0,
        'MinDownload' => 5 * 1024 * 1024 * 1024 * 1024,
        'MinRatio' => 1.2,
        'MinUploads' => 500,
        'Weeks' => 16,
        'AwardLevel' => 6,
        'Invite' => 3,
    ],
    ELITE_TM => [
        'From' => ELITE_TM,
        'To' => GURU,
        'MinUpload' => 0,
        'MinDownload' => 10 * 1024 * 1024 * 1024 * 1024,
        'MinRatio' => 1.2,
        'MinUploads' => 1000,
        'Weeks' => 20,
        'AwardLevel' => 7,
    ]
];

define('RESTRICT_REGISTER', false);
$AllowedDICPermission = [];
$EmailWhiteList = [];
// DIC MySQL details
define('DICSQLHOST', ''); //The MySQL host ip/fqdn
define('DICSQLLOGIN', ''); //The MySQL login
define('DICSQLPASS', ''); //The MySQL password
define('DICSQLDB', ''); //The MySQL database to use
define('DICSQLPORT', 0); //The MySQL port to connect on
define('DICSQLSOCK', '/var/run/mysqld/mysqld.sock');

define('HNR_MIN_SIZE_PERCENT', 0.2);
define('HNR_MIN_MIN_RATIO', 1);
define('HNR_MIN_SEEEDING_TIME', 72 * 3600);
define('HNR_INTERVAL', 14 * 24 * 3600);

define('MINIO_ENDPOINT', "http://bineawang.synology.me:6500");
define('MINIO_KEY', "minioadmin");
define('MINIO_SECRET', "minioadmin");
define('MINIO_BUCKET', "test");

define('BANNER_URL', 'http://localhost:9000');
define('BANNER_TEXT', '公测在即，我们期待你的加入！');
