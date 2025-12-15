<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$main_path = __DIR__.'/../../main.inc.php';
if (!file_exists($main_path)) {
    die('main.inc.php not found');
}
include $main_path;

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
dol_include_once('/peppolnew/class/ublgenerator.class.php');
dol_include_once('/peppolnew/class/peppolapi.class.php');

echo "<pre>";
echo "=== TEST ENVOI PEPPOL ===\n\n";

// ID de facture à tester
$invoice_id = 40; // Changez si besoin

// Générer UBL
$ublGenerator = new UBLGenerator($db);
$ubl_xml = $ublGenerator->generateFromInvoice($invoice_id);

if (!$ubl_xml) {
    die("ERREUR: Impossible de générer l'UBL\n");
}

echo "1. UBL généré : " . strlen($ubl_xml) . " octets\n\n";

// Configuration
$api_url = $conf->global->PEPPOLNEW_API_URL;
$api_key = $conf->global->PEPPOLNEW_API_KEY;
$sender_id = $conf->global->PEPPOLNEW_PEPPOL_ID;

$recipient_id = '0208:0421801233'; // ID du client

echo "2. Configuration :\n";
echo "   API URL: $api_url\n";
echo "   API KEY: " . substr($api_key, 0, 10) . "...\n";
echo "   Sender: $sender_id\n";
echo "   Recipient: $recipient_id\n\n";

// Préparer la requête
$endpoint = rtrim($api_url, '/') . '/message';

$payload = array(
    'sender' => $sender_id,
    'recipient' => $recipient_id,
    'processType' => 'cenbii-procid-ubl::urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
    'documentType' => 'busdox-docid-qns::urn:oasis:names:specification:ubl:schema:xsd:Invoice-2::Invoice##urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0::2.1',
    'fileContent' => base64_encode($ubl_xml)
);

$json_payload = json_encode($payload, JSON_PRETTY_PRINT);

echo "3. Payload envoyé :\n";
echo substr($json_payload, 0, 500) . "...\n\n";

// Envoyer
echo "4. Envoi vers Peppyrus...\n";

$ch = curl_init($endpoint);
curl_setopt_array($ch, array(
    CURLOPT_POST => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'X-Api-Key: ' . $api_key,
        'Content-Length: ' . strlen($json_payload)
    ),
    CURLOPT_POSTFIELDS => $json_payload,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_TIMEOUT => 30
));

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: $http_code\n";
echo "   cURL Error: " . ($curl_error ? $curl_error : 'Aucune') . "\n\n";

echo "5. Réponse de l'API :\n";
echo $response . "\n\n";

$result = json_decode($response, true);
echo "6. Réponse décodée :\n";
print_r($result);

echo "\n=========================\n";
echo "</pre>";
?>