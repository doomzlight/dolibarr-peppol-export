<?php
/**
 * Script de diagnostic - Module Peppol Export
 * 
 * Placez ce fichier dans /htdocs/custom/peppolnew/admin/
 * Nommez-le : diagnostic.php
 * Accédez via : https://votre-url/custom/peppolnew/admin/diagnostic.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<html><head><title>Diagnostic Peppol Export</title>";
echo "<style>
body { font-family: Arial; margin: 20px; }
.ok { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.info { color: blue; }
pre { background: #f5f5f5; padding: 10px; border: 1px solid #ddd; }
table { border-collapse: collapse; width: 100%; margin: 20px 0; }
td, th { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background: #4CAF50; color: white; }
</style></head><body>";

echo "<h1>🔍 Diagnostic du Module Peppol Export</h1>";
echo "<hr>";

// 1. Informations système
echo "<h2>1. Informations système</h2>";
echo "<table>";
echo "<tr><th>Information</th><th>Valeur</th></tr>";
echo "<tr><td>PHP Version</td><td>" . PHP_VERSION . "</td></tr>";
echo "<tr><td>Serveur</td><td>" . $_SERVER['SERVER_SOFTWARE'] . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>";
echo "<tr><td>Script actuel</td><td>" . __FILE__ . "</td></tr>";
echo "<tr><td>Dossier actuel</td><td>" . getcwd() . "</td></tr>";
echo "</table>";

// 2. Recherche de main.inc.php
echo "<h2>2. Recherche de main.inc.php</h2>";
$paths_to_try = array(
    "../../main.inc.php",
    "../../../main.inc.php",
    "../../../../main.inc.php",
    "../../../../../main.inc.php",
    "../../../../../../main.inc.php"
);

echo "<table>";
echo "<tr><th>Chemin</th><th>Chemin complet</th><th>Existe ?</th></tr>";
foreach ($paths_to_try as $path) {
    $full_path = realpath($path);
    $exists = file_exists($path);
    $class = $exists ? 'ok' : 'error';
    echo "<tr>";
    echo "<td>" . htmlspecialchars($path) . "</td>";
    echo "<td>" . htmlspecialchars($full_path ? $full_path : 'N/A') . "</td>";
    echo "<td class='$class'>" . ($exists ? '✓ OUI' : '✗ NON') . "</td>";
    echo "</tr>";
    
    if ($exists && !defined('DOL_DOCUMENT_ROOT')) {
        $main_path = $path;
        echo "</table>";
        echo "<p class='ok'>✓ Fichier main.inc.php trouvé : $full_path</p>";
        break;
    }
}

if (!isset($main_path)) {
    echo "</table>";
    echo "<p class='error'>✗ Impossible de trouver main.inc.php</p>";
    echo "<p class='info'>Le module doit être dans /htdocs/custom/peppolnew/</p>";
} else {
    // 3. Tentative de chargement de Dolibarr
    echo "<h2>3. Chargement de Dolibarr</h2>";
    
    try {
        include $main_path;
        
        if (defined('DOL_DOCUMENT_ROOT')) {
            echo "<p class='ok'>✓ Dolibarr chargé avec succès</p>";
            echo "<table>";
            echo "<tr><th>Constante</th><th>Valeur</th></tr>";
            echo "<tr><td>DOL_DOCUMENT_ROOT</td><td>" . DOL_DOCUMENT_ROOT . "</td></tr>";
            echo "<tr><td>DOL_URL_ROOT</td><td>" . (defined('DOL_URL_ROOT') ? DOL_URL_ROOT : 'N/A') . "</td></tr>";
            echo "<tr><td>DOL_MAIN_URL_ROOT</td><td>" . (defined('DOL_MAIN_URL_ROOT') ? DOL_MAIN_URL_ROOT : 'N/A') . "</td></tr>";
            echo "</table>";
            
            // 4. Vérification du module
            echo "<h2>4. Vérification du module</h2>";
            
            $module_files = array(
                'core/modules/modPeppolExport.class.php',
                'class/peppolapi.class.php',
                'class/ublgenerator.class.php',
                'class/actions_peppolnew.class.php',
                'lib/peppolnew.lib.php',
                'peppol_send.php',
                'js/peppolnew.js',
                'langs/fr_FR/peppolnew.lang',
                'sql/llx_peppolnew_log.sql'
            );
            
            echo "<table>";
            echo "<tr><th>Fichier</th><th>Existe ?</th><th>Taille</th></tr>";
            
            $module_path = DOL_DOCUMENT_ROOT . '/custom/peppolnew/';
            $all_ok = true;
            
            foreach ($module_files as $file) {
                $full_file = $module_path . $file;
                $exists = file_exists($full_file);
                $size = $exists ? filesize($full_file) : 0;
                $class = $exists && $size > 100 ? 'ok' : 'error';
                
                if (!$exists || $size <= 100) $all_ok = false;
                
                echo "<tr>";
                echo "<td>" . htmlspecialchars($file) . "</td>";
                echo "<td class='$class'>" . ($exists ? '✓ OUI' : '✗ NON') . "</td>";
                echo "<td>" . ($exists ? number_format($size) . ' octets' : 'N/A') . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            
            if ($all_ok) {
                echo "<p class='ok'>✓ Tous les fichiers du module sont présents</p>";
            } else {
                echo "<p class='error'>✗ Certains fichiers sont manquants ou vides</p>";
            }
            
            // 5. URL de configuration correcte
            echo "<h2>5. URL de configuration</h2>";
            
            $base_url = DOL_MAIN_URL_ROOT;
            $setup_url = $base_url . '/custom/peppolnew/admin/setup.php';
            
            echo "<p><strong>URL correcte pour accéder à la configuration :</strong></p>";
            echo "<pre>$setup_url</pre>";
            echo "<p><a href='$setup_url' style='padding:10px 20px;background:#4CAF50;color:white;text-decoration:none;border-radius:4px;'>Accéder à la configuration</a></p>";
            
            // 6. État du module
            echo "<h2>6. État du module dans Dolibarr</h2>";
            
            if (isset($conf->peppolnew->enabled)) {
                if (!empty($conf->peppolnew->enabled)) {
                    echo "<p class='ok'>✓ Module activé</p>";
                    
                    echo "<table>";
                    echo "<tr><th>Configuration</th><th>Valeur</th></tr>";
                    echo "<tr><td>API URL</td><td>" . (!empty($conf->global->PEPPOLNEW_API_URL) ? $conf->global->PEPPOLNEW_API_URL : '<span class="error">Non configuré</span>') . "</td></tr>";
                    echo "<tr><td>API Key</td><td>" . (!empty($conf->global->PEPPOLNEW_API_KEY) ? '<span class="ok">Configuré (' . strlen($conf->global->PEPPOLNEW_API_KEY) . ' caractères)</span>' : '<span class="error">Non configuré</span>') . "</td></tr>";
                    echo "<tr><td>Peppol ID</td><td>" . (!empty($conf->global->PEPPOLNEW_PEPPOL_ID) ? $conf->global->PEPPOLNEW_PEPPOL_ID : '<span class="error">Non configuré</span>') . "</td></tr>";
                    echo "</table>";
                } else {
                    echo "<p class='error'>✗ Module installé mais non activé</p>";
                    echo "<p><a href='" . DOL_MAIN_URL_ROOT . "/admin/modules.php'>Aller dans Configuration > Modules</a></p>";
                }
            } else {
                echo "<p class='error'>✗ Module non détecté par Dolibarr</p>";
                echo "<p class='info'>Le fichier modPeppolExport.class.php est peut-être incomplet ou mal formaté.</p>";
            }
            
        } else {
            echo "<p class='error'>✗ DOL_DOCUMENT_ROOT non défini après inclusion</p>";
        }
        
    } catch (Exception $e) {
        echo "<p class='error'>✗ Erreur lors du chargement : " . $e->getMessage() . "</p>";
    }
}

// 7. Recommandations
echo "<h2>7. Recommandations</h2>";
echo "<ul>";
echo "<li>Assurez-vous que le module est dans <code>/htdocs/custom/peppolnew/</code></li>";
echo "<li>Vérifiez les permissions des fichiers (755 pour les dossiers, 644 pour les fichiers)</li>";
echo "<li>Videz le cache Dolibarr après installation : Configuration > Sécurité > Vider le cache</li>";
echo "<li>Si le module n'est pas visible, vérifiez les logs Apache/PHP</li>";
echo "</ul>";

echo "<hr>";
echo "<p><em>Diagnostic généré le " . date('Y-m-d H:i:s') . "</em></p>";

echo "</body></html>";
?>