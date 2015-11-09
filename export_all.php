#!/usr/bin/env php

<?php

/**
 * pr batch script
 *
 * Export all
 *
 * Creates a list with the IDs of the existing Series to be exported later.
 *
 * Use mode:
 *
 * php export_all.php EXPORT_PATH > script_export_series_bat.sh
 *
 * @package    pumukituvigo
 * @subpackage batch
 * @version    $Id$
 */

define('SF_ROOT_DIR',    realpath(dirname(__file__).'/../..'));
define('SF_APP',         'editar');
define('SF_ENVIRONMENT', 'prod');
define('SF_DEBUG',       0);

require_once(SF_ROOT_DIR.DIRECTORY_SEPARATOR.'apps'.DIRECTORY_SEPARATOR.SF_APP.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'config.php');

// initialize database manager
$databaseManager = new sfDatabaseManager();
$databaseManager->initialize();

// Check input
if (2 != count($argv)) {
    throw new \Exception("ERROR: Use mode: php export_all.php EXPORT_PATH > export_script_series_bat.sh");
    exit(-1);
}

if (!is_dir($dir=$argv[1])) {
    throw new \Exception("ERROR: Unable to find '".$dir."' directory. Please, give an existing folder.");
    exit(-1);
}

$c = new Criteria();
$c->addAscendingOrderByColumn(SerialPeer::ID);
$series = SerialPeer::doSelect($c);

foreach($series as $s){
    echo "echo " . $s->getId() ."\n";
    echo "php export.php " . $s->getId() ." > ". $dir . ((substr($dir, -1) === "/")?"":"/") . "serial" . sprintf("%04s", $s->getId()) .".xml\n";
}