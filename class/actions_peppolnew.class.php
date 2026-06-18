<?php

class ActionsPeppolnew
{
    public function addMoreActionsButtons($parameters, &$object, &$action, $hookmanager)
    {
        global $conf, $langs, $user;
        
        if (in_array('invoicecard', explode(':', $parameters['context']))) {
            if (($object->statut == 1 || $object->statut == 2) && $object->type != 2) {

                require_once DOL_DOCUMENT_ROOT.'/custom/peppolnew/lib/peppolnew.lib.php';
                $ml = peppolnewGetLangs();

                // Expose la racine d'URL Dolibarr au JS (corrige les chemins codés en dur)
                print '<script type="text/javascript">var PEPPOLNEW_URL_ROOT = "'.dol_escape_js(DOL_URL_ROOT).'";</script>';

                // Libellés traduits transmis au JavaScript
                $i18n = array(
                    'missingId'        => $ml->trans('JsMissingInvoiceId'),
                    'confirmLookup'    => $ml->trans('JsConfirmLookup'),
                    'searching'        => $ml->trans('JsSearching'),
                    'participantFound' => $ml->trans('JsParticipantFound'),
                    'participantId'    => $ml->trans('JsParticipantId'),
                    'services'         => $ml->trans('JsServices'),
                    'confirmSend'      => $ml->trans('JsConfirmSend'),
                    'sending'          => $ml->trans('JsSending'),
                    'invoiceSent'      => $ml->trans('JsInvoiceSent'),
                    'transactionId'    => $ml->trans('JsTransactionId'),
                    'checkDashboard'   => $ml->trans('JsCheckDashboard'),
                    'connectionError'  => $ml->trans('JsConnectionError'),
                    'error'            => $ml->trans('JsError'),
                );
                print '<script type="text/javascript">var PEPPOLNEW_I18N = '.json_encode($i18n).';</script>';

                // Charge le JS
                print '<script src="'.DOL_URL_ROOT.'/custom/peppolnew/js/peppolnew.js"></script>';

                // Boutons PEPPOL
                print '<div class="inline-block divButAction">';

                // Bouton Générer UBL
                print '<a class="butAction" href="'.DOL_URL_ROOT.'/custom/peppolnew/peppol_send.php?id='.$object->id.'&action=generate_ubl">';
                print '📄 '.$ml->trans('GenerateUBL').'</a>';

                // Bouton Rechercher Peppol
                print '<a class="butAction" href="#" onclick="searchPeppol('.$object->id.'); return false;">';
                print '🔍 '.$ml->trans('PeppolNetworkLookup').'</a>';

                // Bouton Envoyer vers Peppol
                print '<a class="butAction" href="#" onclick="sendToPeppol('.$object->id.'); return false;">';
                print '📤 '.$ml->trans('SendToPeppol').'</a>';

                print '</div>';
            }
        }

        return 0;
    }

    public function formObjectOptions($parameters, &$object, &$action, $hookmanager)
    {
        if (in_array('invoicecard', explode(':', $parameters['context']))) {
            require_once DOL_DOCUMENT_ROOT.'/custom/peppolnew/lib/peppolnew.lib.php';
            $ml = peppolnewGetLangs();
            // Affiche l'ID Peppol du client si configuré
            if (!empty($object->thirdparty->idprof6)) {
                print '<tr><td>'.$ml->trans('CustomerPeppolID').'</td><td>'.$object->thirdparty->idprof6.'</td></tr>';
            }
        }
        return 0;
    }
}