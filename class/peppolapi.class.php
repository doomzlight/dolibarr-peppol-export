<?php
/**
 * Class to manage Peppyrus API communication
 * CORRIGÉ selon la documentation officielle Peppyrus
 */

class PeppolAPI
{
    private $api_url;
    private $api_key;
    private $db;
    
    public function __construct($db)
    {
        global $conf;
        
        $this->db = $db;
        $this->api_url = !empty($conf->global->PEPPOLNEW_API_URL) ? $conf->global->PEPPOLNEW_API_URL : 'https://api.peppyrus.be/v1';
        $this->api_key = !empty($conf->global->PEPPOLNEW_API_KEY) ? $conf->global->PEPPOLNEW_API_KEY : '';
    }
    
    /**
     * Send UBL document to Peppyrus
     * 
     * @param string $ubl_content UBL XML content
     * @param string $recipient_id Peppol participant ID (format: 9925:be0123456789)
     * @param string $document_type Document type identifier
     * @return array Result array with success status and message
     */
    public function sendDocument($ubl_content, $recipient_id, $document_type = 'busdox-docid-qns::urn:oasis:names:specification:ubl:schema:xsd:Invoice-2::Invoice##urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0::2.1')
    {
        global $conf;
        
        if (empty($this->api_key)) {
            return array('success' => false, 'message' => 'API Key not configured');
        }
        
        // Récupérer l'ID Peppol de l'expéditeur (votre entreprise)
       global $conf;
$sender_id = '';
if (isset($conf->global->PEPPOLNEW_PEPPOL_ID)) {
    $sender_id = $conf->global->PEPPOLNEW_PEPPOL_ID;
}

// DEBUG
error_log('=== PEPPOL SENDER DEBUG ===');
error_log('Sender ID from config: ' . $sender_id);
error_log('Config object exists: ' . (isset($conf->global) ? 'YES' : 'NO'));
error_log('===========================');
        
        if (empty($sender_id)) {
            return array('success' => false, 'message' => 'Sender Peppol ID not configured');
        }
        
        try {
            // Endpoint correct selon la documentation
            $endpoint = rtrim($this->api_url, '/') . '/message';
            
            // Préparer les données selon le format Peppyrus
            $payload = array(
                'sender' => $sender_id,
                'recipient' => $recipient_id,
                'processType' => 'cenbii-procid-ubl::urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
                'documentType' => $document_type,
                'fileContent' => base64_encode($ubl_content) // IMPORTANT: encoder en base64
            );
            
            $json_payload = json_encode($payload);
            // DEBUG - À supprimer après test
            error_log('=== PEPPOL DEBUG ===');
            error_log('Endpoint: ' . $endpoint);
            error_log('Sender: ' . $sender_id);
            error_log('Recipient: ' . $recipient_id);
            error_log('Payload: ' . $json_payload);
            error_log('===================');
            // Initialiser cURL
            $ch = curl_init($endpoint);
            
            curl_setopt_array($ch, array(
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'X-Api-Key: ' . $this->api_key,  // En-tête correct pour Peppyrus
                    'Content-Length: ' . strlen($json_payload)
                ),
                CURLOPT_POSTFIELDS => $json_payload,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 30
            ));
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                return array('success' => false, 'message' => 'cURL Error: ' . $error);
            }
            
            $result = json_decode($response, true);
            
            if ($http_code >= 200 && $http_code < 300) {
                return array(
                    'success' => true, 
                    'message' => 'Document sent successfully to Peppol',
                    'response' => $result,
                    'message_id' => isset($result['id']) ? $result['id'] : null
                );
            } else {
                // Gérer les erreurs selon la documentation
                $error_message = 'API Error (HTTP ' . $http_code . ')';
                
                if (is_array($result)) {
                    if (isset($result['message'])) {
                        $error_message .= ': ' . $result['message'];
                    } elseif (isset($result['error'])) {
                        $error_message .= ': ' . $result['error'];
                    }
                } else {
                    $error_message .= ': ' . $response;
                }
                
                return array(
                    'success' => false, 
                    'message' => $error_message,
                    'http_code' => $http_code,
                    'response' => $result
                );
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => 'Exception: ' . $e->getMessage());
        }
    }
    
    /**
     * Lookup participant in Peppol directory
     * 
     * @param string $participant_id Participant ID (format: 9925:be0123456789)
     * @return array Result with participant info
     */
    public function lookupParticipant($participant_id)
    {
        if (empty($this->api_key)) {
            return array('success' => false, 'message' => 'API Key not configured');
        }
        
        try {
            // Endpoint pour lookup selon la documentation
            $endpoint = rtrim($this->api_url, '/') . '/peppol/lookup?participantId=' . urlencode($participant_id);
            
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'X-Api-Key: ' . $this->api_key,
                    'Accept: application/json'
                ),
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 10
            ));
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                return array(
                    'success' => true, 
                    'data' => $result,
                    'participant_id' => isset($result['participantId']) ? $result['participantId'] : $participant_id,
                    'services' => isset($result['services']) ? $result['services'] : array()
                );
            } else {
                $error_msg = 'Participant not found';
                $result = json_decode($response, true);
                if (is_string($result)) {
                    $error_msg = $result;
                }
                return array('success' => false, 'message' => $error_msg, 'http_code' => $http_code);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
    
    /**
     * Get organization info from Peppyrus
     * 
     * @return array Result with organization info
     */
    public function getOrganizationInfo()
    {
        if (empty($this->api_key)) {
            return array('success' => false, 'message' => 'API Key not configured');
        }
        
        try {
            $endpoint = rtrim($this->api_url, '/') . '/organization/info';
            
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'X-Api-Key: ' . $this->api_key,
                    'Accept: application/json'
                ),
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_TIMEOUT => 10
            ));
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($http_code === 200) {
                $result = json_decode($response, true);
                return array('success' => true, 'data' => $result);
            } else {
                return array('success' => false, 'message' => 'Failed to get organization info', 'http_code' => $http_code);
            }
            
        } catch (Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }
}