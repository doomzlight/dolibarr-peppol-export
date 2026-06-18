<?php

class ActionsPeppolnew
{
    public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $langs, $user;
        
        if (in_array('invoicecard', explode(':', $parameters['context']))) {
            if (($object->statut == 1 || $object->statut == 2) && $object->type != 2) {
                
                // Expose la racine d'URL Dolibarr au JS (corrige les chemins codés en dur)
                print '<script type="text/javascript">var PEPPOLNEW_URL_ROOT = "'.dol_escape_js(DOL_URL_ROOT).'";</script>';
                // Charge le JS
                print '<script src="'.DOL_URL_ROOT.'/custom/peppolnew/js/peppolnew.js"></script>';
                
                // Boutons PEPPOL
                print '<div class="inline-block divButAction">';
                
                // Bouton Générer UBL
                print '<a class="butAction" href="'.DOL_URL_ROOT.'/custom/peppolnew/peppol_send.php?id='.$object->id.'&action=generate_ubl">';
                print '📄 Générer UBL</a>';
                
                // Bouton Rechercher Peppol
                print '<a class="butAction" href="#" onclick="searchPeppol('.$object->id.'); return false;">';
                print '🔍 Rechercher Peppol</a>';
                
                // Bouton Envoyer vers Peppol
                print '<a class="butAction" href="#" onclick="sendToPeppol('.$object->id.'); return false;">';
                print '📤 Envoyer Peppol</a>';
                
                print '</div>';
            }
        }
        
        return 0;
    }
    
    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        if (in_array('invoicecard', explode(':', $parameters['context']))) {
            // Affiche l'ID Peppol du client si configuré
            if (!empty($object->thirdparty->idprof6)) {
                print '<tr><td>ID Peppol Client</td><td>'.$object->thirdparty->idprof6.'</td></tr>';
            }
        }
        return 0;
    }
}