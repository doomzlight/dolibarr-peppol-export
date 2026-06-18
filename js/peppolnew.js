/**
 * JavaScript pour le module Peppol Export
 */

// Racine d'URL Dolibarr (injectée par le hook). Fallback sur la racine du site.
var PEPPOLNEW_BASE = (typeof PEPPOLNEW_URL_ROOT !== 'undefined' ? PEPPOLNEW_URL_ROOT : '') + '/custom/peppolnew/peppol_send.php';

// Libellés traduits injectés par le hook (PEPPOLNEW_I18N). Fallback en français.
var PEPPOLNEW_T = (typeof PEPPOLNEW_I18N !== 'undefined') ? PEPPOLNEW_I18N : {
    missingId: 'ID de facture manquant',
    confirmLookup: 'Rechercher ce client dans l\'annuaire Peppol ?',
    searching: 'Recherche...',
    participantFound: 'Participant trouvé dans Peppol !',
    participantId: 'ID Peppol',
    services: 'Services',
    confirmSend: 'Voulez-vous envoyer cette facture vers le réseau Peppol ?',
    sending: 'Envoi en cours...',
    invoiceSent: 'Facture envoyée avec succès vers Peppol !',
    transactionId: 'ID Transaction',
    checkDashboard: 'Vérifiez votre tableau de bord Peppyrus pour le suivi.',
    connectionError: 'Erreur de connexion',
    error: 'Erreur'
};

function searchPeppol(invoiceId) {
    if (!invoiceId) {
        alert(PEPPOLNEW_T.missingId);
        return;
    }

    if (!confirm(PEPPOLNEW_T.confirmLookup)) {
        return;
    }

    // Affiche un loader
    var btn = event.target;
    var originalText = btn.innerHTML;
    btn.innerHTML = '⏳ ' + PEPPOLNEW_T.searching;
    btn.disabled = true;

    fetch(PEPPOLNEW_BASE + '?id=' + invoiceId + '&action=lookup')
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;

            if (data.success) {
                var participant = data.data || data;
                alert('✅ ' + PEPPOLNEW_T.participantFound + '\n\n' +
                      PEPPOLNEW_T.participantId + ': ' + (participant.participantId || data.participant_id || 'N/A') + '\n' +
                      PEPPOLNEW_T.services + ': ' + (participant.services ? participant.services.length : '0'));
            } else {
                alert('❌ ' + PEPPOLNEW_T.error + ': ' + data.message);
            }
        })
        .catch(error => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('❌ ' + PEPPOLNEW_T.connectionError + ': ' + error);
        });
}

function sendToPeppol(invoiceId) {
    if (!invoiceId) {
        alert(PEPPOLNEW_T.missingId);
        return;
    }

    if (!confirm(PEPPOLNEW_T.confirmSend)) {
        return;
    }

    // Affiche un loader
    var btn = event.target;
    var originalText = btn.innerHTML;
    btn.innerHTML = '⏳ ' + PEPPOLNEW_T.sending;
    btn.disabled = true;

    fetch(PEPPOLNEW_BASE + '?id=' + invoiceId + '&action=send')
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;

            if (data.success) {
                alert('✅ ' + PEPPOLNEW_T.invoiceSent + '\n\n' +
                      PEPPOLNEW_T.transactionId + ': ' + (data.message_id || data.transaction_id || 'N/A') + '\n' +
                      PEPPOLNEW_T.checkDashboard);
            } else {
                alert('❌ ' + PEPPOLNEW_T.error + ': ' + data.message);
            }
        })
        .catch(error => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('❌ ' + PEPPOLNEW_T.connectionError + ': ' + error);
        });
}
