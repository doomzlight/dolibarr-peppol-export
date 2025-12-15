<?php
/**
 * Test de configuration Peppol
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Recherche de main.inc.php
$main_path = '';
$paths_to_try = array(
    __DIR__.'/../../../main.inc.php',
    __DIR__.'/../../main.inc.php',
    dirname(__DIR__, 3).'/main.inc.php',
    dirname(__DIR__, 2).'/main.inc.php'
);

foreach ($paths_to_try as $path) {
    if (file_exists($path)) {
        $main_path = $path;
        break;
    }
}

if (empty($main_path)) {
    die('ERROR: main.inc.php not found. Tried: ' . implode(', ', $paths_to_try));
}

$res = @include_once $main_path;
if (!$res || !defined('DOL_DOCUMENT_ROOT')) {
    die('ERROR: Failed to load Dolibarr from: ' . $main_path);
}

echo "<html><body><pre>";
echo "=== TEST CONFIGURATION PEPPOL ===\n\n";
echo "1. Dolibarr chargé : OUI\n";
echo "   Fichier : $main_path\n";
echo "2. DOL_DOCUMENT_ROOT : " . DOL_DOCUMENT_ROOT . "\n\n";

echo "3. Configuration Peppol Export :\n";
echo "   - Module activé : " . (!empty($conf->peppolnew->enabled) ? 'OUI' : 'NON') . "\n";
echo "   - API URL : " . (!empty($conf->global->PEPPOLNEW_API_URL) ? $conf->global->PEPPOLNEW_API_URL : 'NON CONFIGURÉ') . "\n";
echo "   - API KEY : " . (!empty($conf->global->PEPPOLNEW_API_KEY) ? 'CONFIGURÉ (' . strlen($conf->global->PEPPOLNEW_API_KEY) . ' caractères)' : 'NON CONFIGURÉ') . "\n";
echo "   - PEPPOL ID : " . (!empty($conf->global->PEPPOLNEW_PEPPOL_ID) ? $conf->global->PEPPOLNEW_PEPPOL_ID : 'NON CONFIGURÉ') . "\n\n";

echo "4. Test de récupération directe depuis la base :\n";
$sql = "SELECT name, value FROM " . MAIN_DB_PREFIX . "const WHERE name LIKE 'PEPPOLNEW%' ORDER BY name";
$resql = $db->query($sql);
if ($resql) {
    $num = $db->num_rows($resql);
    if ($num > 0) {
        while ($obj = $db->fetch_object($resql)) {
            echo "   - " . $obj->name . " = " . ($obj->name == 'PEPPOLNEW_API_KEY' ? '***MASQUÉ***' : $obj->value) . "\n";
        }
    } else {
        echo "   AUCUNE configuration trouvée en base !\n";
    }
} else {
    echo "   ERREUR SQL : " . $db->lasterror() . "\n";
}

echo "\n=================================\n";
echo "</pre></body></html>";