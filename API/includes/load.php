
<?php
// -----------------------------------------------------------------------
// DEFINE SEPERATOR ALIASES
// -----------------------------------------------------------------------
define("URL_SEPARATOR", '/');

define("DS", DIRECTORY_SEPARATOR);

// -----------------------------------------------------------------------
// DEFINE ROOT PATHS
// -----------------------------------------------------------------------
defined('SITE_ROOT')? null: define('SITE_ROOT', realpath(dirname(__FILE__)));
define("LIB_PATH_INC", SITE_ROOT.DS);


require_once('includes/config.php');
require_once('includes/functions.php');
require_once('includes/session.php');
require_once('includes/database.php');
require_once('includes/sql.php');


?>
