<?
class G {
    /** @var DB_MYSQL */
    public static $DB;
    /** @var CACHE */
    public static $Cache;
    /** @var \Gazelle\Router */
    public static $Router;

    public static $LoggedUser;

    public static $Twig;

    public static function initialize() {
        global $DB, $Cache, $LoggedUser, $Twig;
        self::$DB = $DB;
        self::$Cache = $Cache;
        self::$LoggedUser = &$LoggedUser;
        self::$Twig = &$Twig;
    }
}
