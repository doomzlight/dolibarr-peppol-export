<?php
/**
 * Library for Peppol Export module
 * MODIFIÉ pour utiliser le champ personnalisé peppyrus_id
 */

/**
 * Retourne un objet de traduction forcé sur la langue choisie dans la config
 * du module (PEPPOLNEW_LANG : 'fr_FR' ou 'en_US'), indépendamment de la langue
 * globale de l'utilisateur. Par défaut : fr_FR (comportement d'origine).
 *
 * @return Translate
 */
function peppolnewGetLangs()
{
    global $conf, $langs;

    $code = !empty($conf->global->PEPPOLNEW_LANG) ? $conf->global->PEPPOLNEW_LANG : 'fr_FR';

    $l = new Translate('', $conf);
    $l->setDefaultLang($code);
    $l->loadLangs(array('main', 'admin', 'peppolnew@peppolnew'));

    return $l;
}

function peppolnewAdminPrepareHead()
{
    global $langs, $conf;

    $langs->load("peppolnew@peppolnew");

    $h = 0;
    $head = array();

    $head[$h][0] = dol_buildpath("/peppolnew/admin/setup.php", 1);
    $head[$h][1] = $langs->trans("Settings");
    $head[$h][2] = 'settings';
    $h++;

    complete_head_from_modules($conf, $langs, null, $head, $h, 'peppolnew');
    complete_head_from_modules($conf, $langs, null, $head, $h, 'peppolnew', 'remove');

    return $head;
}

/**
 * Get Peppol participant ID from company
 * MODIFIÉ : Utilise le champ personnalisé peppyrus_id
 */
function getPeppolIdFromCompany($company)
{
    // 1. Vérifier le champ personnalisé peppyrus_id (PRIORITAIRE)
    if (!empty($company->array_options['options_peppyrus_id'])) {
        return $company->array_options['options_peppyrus_id'];
    }
    
    // 2. Fallback : vérifier idprof6 (au cas où certains l'utilisent encore)
    if (!empty($company->idprof6)) {
        return $company->idprof6;
    }
    
    // 3. Fallback : construire depuis le numéro d'entreprise belge (schéma 0208,
    //    recommandé et enregistré dans l'annuaire Peppol ; 9925/TVA est obsolète).
    if (!empty($company->tva_intra) && stripos($company->tva_intra, 'BE') === 0) {
        $enterprise = str_replace(array('BE', 'be', '.', ' '), '', $company->tva_intra);
        return '0208:' . str_pad($enterprise, 10, '0', STR_PAD_LEFT);
    }
    
    return '';
}