/**
 * JavaScript pour le module Peppol Export
 */

function searchPeppol(invoiceId) {
    if (!invoiceId) {
        alert('ID de facture manquant');
        return;
    }
    
    if (!confirm('Rechercher ce client dans l\'annuaire Peppol ?')) {
        return;
    }
    
    // Affiche un loader
    var btn = event.target;
    var originalText = btn.innerHTML;
    btn.innerHTML = '⏳ Recherche...';
    btn.disabled = true;
    
    fetch('/dolibarr-19.0.0/htdocs/custom/peppolnew/peppol_send.php?id=' + invoiceId + '&action=lookup')
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            if (data.success) {
                var participant = data.data || data;
                alert('✅ Participant trouvé dans Peppol !\n\n' + 
                      'ID Peppol: ' + (participant.participantId || data.participant_id || 'N/A') + '\n' +
                      'Services: ' + (participant.services ? participant.services.length : '0') + ' service(s)');
            } else {
                alert('❌ Erreur: ' + data.message);
            }
        })
        .catch(error => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('❌ Erreur de connexion: ' + error);
        });
}

function sendToPeppol(invoiceId) {
    if (!invoiceId) {
        alert('ID de facture manquant');
        return;
    }
    
    if (!confirm('Voulez-vous envoyer cette facture vers le réseau Peppol ?')) {
        return;
    }
    
    // Affiche un loader
    var btn = event.target;
    var originalText = btn.innerHTML;
    btn.innerHTML = '⏳ Envoi en cours...';
    btn.disabled = true;
    
    fetch('/dolibarr-19.0.0/htdocs/custom/peppolnew/peppol_send.php?id=' + invoiceId + '&action=send')
        .then(response => response.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            
            if (data.success) {
                alert('✅ Facture envoyée avec succès vers Peppol !\n\n' +
                      'ID Transaction: ' + (data.transaction_id || 'N/A') + '\n' +
                      'Vérifiez votre tableau de bord Peppyrus pour le suivi.');
            } else {
                alert('❌ Erreur lors de l\'envoi: ' + data.message);
            }
        })
        .catch(error => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            alert('❌ Erreur de connexion: ' + error);
        });
}
