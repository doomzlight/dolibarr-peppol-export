<?php
/**
 * DEV ONLY - CLI helper to enable the core modules needed for the demo
 * (Societe, Invoices, Bank, Products) plus the peppolnew module.
 * Run before setup_testdata.php. Safe to delete.
 */
if (php_sapi_name() !== 'cli') { die("CLI only\n"); }

$path = __DIR__.'/../../../';
require_once $path.'master.inc.php';
require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

$modules = array(
    '/core/modules/modSociete.class.php'  => 'modSociete',
    '/core/modules/modProduct.class.php'  => 'modProduct',
    '/core/modules/modBanque.class.php'   => 'modBanque',
    '/core/modules/modFacture.class.php'  => 'modFacture',
    '/custom/peppolnew/core/modules/modPeppolNew.class.php' => 'modPeppolNew',
);

foreach ($modules as $file => $class) {
    $full = ($class === 'modPeppolNew') ? DOL_DOCUMENT_ROOT.$file : DOL_DOCUMENT_ROOT.$file;
    if (!file_exists($full)) { echo "MISSING $full\n"; continue; }
    require_once $full;
    $obj = new $class($db);
    $r = $obj->init();
    echo $class.": ".($r >= 0 ? "enabled" : ("FAILED ".$obj->error))."\n";
}
echo "Done.\n";
