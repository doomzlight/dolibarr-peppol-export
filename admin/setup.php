<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Chemin absolu vers main.inc.php
$main_path = __DIR__.'/../../../main.inc.php';

if (!file_exists($main_path)) {
    die('main.inc.php not found at: ' . $main_path);
}

$res = include $main_path;

if (!$res || !defined('DOL_DOCUMENT_ROOT')) {
    die('Failed to load Dolibarr');
}

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
dol_include_once('/peppolnew/lib/peppolnew.lib.php');

$langs->loadLangs(array("admin", "peppolnew@peppolnew"));

if (!$user->admin) {
    accessforbidden();
}

$action = GETPOST('action', 'aZ09');

// Actions
if ($action == 'setvalue') {
    $api_url = GETPOST('PEPPOLNEW_API_URL', 'alpha');
    $api_key = GETPOST('PEPPOLNEW_API_KEY', 'alpha');
    $peppol_id = GETPOST('PEPPOLNEW_PEPPOL_ID', 'alpha');
    $lang = GETPOST('PEPPOLNEW_LANG', 'alpha');

    if ($api_url !== null) {
        dolibarr_set_const($db, 'PEPPOLNEW_API_URL', $api_url, 'chaine', 0, '', $conf->entity);
    }
    if ($api_key !== null) {
        dolibarr_set_const($db, 'PEPPOLNEW_API_KEY', $api_key, 'chaine', 0, '', $conf->entity);
    }
    if ($peppol_id !== null) {
        dolibarr_set_const($db, 'PEPPOLNEW_PEPPOL_ID', $peppol_id, 'chaine', 0, '', $conf->entity);
    }
    if (in_array($lang, array('fr_FR', 'en_US'), true)) {
        dolibarr_set_const($db, 'PEPPOLNEW_LANG', $lang, 'chaine', 0, '', $conf->entity);
    }
    
    setEventMessages($langs->trans("SetupSaved"), null, 'mesgs');
    header("Location: ".$_SERVER["PHP_SELF"]);
    exit;
}

// Objet de traduction forcé sur la langue choisie pour le module
$mylangs = peppolnewGetLangs();
$current_lang = !empty($conf->global->PEPPOLNEW_LANG) ? $conf->global->PEPPOLNEW_LANG : 'fr_FR';

// View
$page_name = $mylangs->trans("PeppolExportSetup");
llxHeader('', $page_name);

$linkback = '<a href="'.DOL_URL_ROOT.'/admin/modules.php?restore_lastsearch_values=1">'.$langs->trans("BackToModuleList").'</a>';
print load_fiche_titre($page_name, $linkback, 'title_setup');

$head = peppolnewAdminPrepareHead();
print dol_get_fiche_head($head, 'settings', $page_name, -1, 'generic');

print '<form method="POST" action="'.$_SERVER['PHP_SELF'].'">';
print '<input type="hidden" name="token" value="'.newToken().'">';
print '<input type="hidden" name="action" value="setvalue">';

print '<table class="noborder centpercent">';
print '<tr class="liste_titre">';
print '<td>'.$mylangs->trans("Parameter").'</td>';
print '<td>'.$mylangs->trans("Value").'</td>';
print '</tr>';

// Module language
print '<tr class="oddeven">';
print '<td width="50%">'.$mylangs->trans("PeppolModuleLanguage").'<br>';
print '<span class="opacitymedium">'.$mylangs->trans("PeppolModuleLanguageHelp").'</span></td>';
print '<td><select class="flat" name="PEPPOLNEW_LANG">';
print '<option value="fr_FR"'.($current_lang == 'fr_FR' ? ' selected' : '').'>'.$mylangs->trans("LangFrench").'</option>';
print '<option value="en_US"'.($current_lang == 'en_US' ? ' selected' : '').'>'.$mylangs->trans("LangEnglish").'</option>';
print '</select></td>';
print '</tr>';

// API URL
print '<tr class="oddeven">';
print '<td><span class="fieldrequired">'.$mylangs->trans("PeppolAPIURL").'</span><br>';
print '<span class="opacitymedium">'.$mylangs->trans("PeppolAPIURLHelp").'</span></td>';
print '<td><input type="text" class="flat minwidth500" name="PEPPOLNEW_API_URL" value="'.dol_escape_htmltag(!empty($conf->global->PEPPOLNEW_API_URL) ? $conf->global->PEPPOLNEW_API_URL : 'https://api.peppyrus.be/v1').'"></td>';
print '</tr>';

// API Key
print '<tr class="oddeven">';
print '<td><span class="fieldrequired">'.$mylangs->trans("PeppolAPIKey").'</span><br>';
print '<span class="opacitymedium">'.$mylangs->trans("PeppolAPIKeyHelp").'</span></td>';
print '<td><input type="password" class="flat minwidth500" name="PEPPOLNEW_API_KEY" value="'.dol_escape_htmltag(!empty($conf->global->PEPPOLNEW_API_KEY) ? $conf->global->PEPPOLNEW_API_KEY : '').'"></td>';
print '</tr>';

// Peppol ID
print '<tr class="oddeven">';
print '<td><span class="fieldrequired">'.$mylangs->trans("YourPeppolID").'</span><br>';
print '<span class="opacitymedium">'.$mylangs->trans("YourPeppolIDHelp").'</span></td>';
print '<td><input type="text" class="flat minwidth300" name="PEPPOLNEW_PEPPOL_ID" value="'.dol_escape_htmltag(!empty($conf->global->PEPPOLNEW_PEPPOL_ID) ? $conf->global->PEPPOLNEW_PEPPOL_ID : '').'" placeholder="0208:0123456789"></td>';
print '</tr>';

print '</table>';

print '<div class="center"><input type="submit" class="button" value="'.$mylangs->trans("Save").'"></div>';
print '</form>';

// Info box
print '<br><div class="info hideonsmartphone">';
print '<b>'.$mylangs->trans("HowToUseTitle").'</b><br>';
print $mylangs->trans("HowToUseIntro").'<ul>';
print '<li>1. '.$mylangs->trans("HowToUseStep1").'</li>';
print '<li>2. '.$mylangs->trans("HowToUseStep2").'</li>';
print '<li>3. '.$mylangs->trans("HowToUseStep3").'</li>';
print '<li>4. '.$mylangs->trans("HowToUseStep4").'</li>';
print '</ul></div>';

print dol_get_fiche_end();

llxFooter();
$db->close();