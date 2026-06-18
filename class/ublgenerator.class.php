<?php
/**
 * Class to generate UBL 2.1 XML (PEPPOL BIS Billing 3.0) from Dolibarr invoices
 */

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';

class UBLGenerator
{
    private $db;
    private $invoice;
    private $company;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function generateFromInvoice($invoice_id)
    {
        global $conf, $mysoc;
        
        $this->invoice = new Facture($this->db);
        if ($this->invoice->fetch($invoice_id) <= 0) {
            return false;
        }
        $this->invoice->fetch_lines();
        
        $this->company = new Societe($this->db);
        $this->company->fetch($this->invoice->socid);
        
        $is_credit_note = ($this->invoice->type == Facture::TYPE_CREDIT_NOTE);
        
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = true;
        
        $root_name = $is_credit_note ? 'CreditNote' : 'Invoice';
        $root = $xml->createElement($root_name);
        $xml->appendChild($root);
        
        $root->setAttribute('xmlns', 'urn:oasis:names:specification:ubl:schema:xsd:' . $root_name . '-2');
        $root->setAttribute('xmlns:cac', 'urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2');
        $root->setAttribute('xmlns:cbc', 'urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2');
        
        $this->addElement($xml, $root, 'cbc:UBLVersionID', '2.1');
        $this->addElement($xml, $root, 'cbc:CustomizationID', 'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0');
        $this->addElement($xml, $root, 'cbc:ProfileID', 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0');
        
        $this->addElement($xml, $root, 'cbc:ID', $this->invoice->ref);
        $this->addElement($xml, $root, 'cbc:IssueDate', dol_print_date($this->invoice->date, '%Y-%m-%d'));
        
        if ($this->invoice->date_lim_reglement) {
            $this->addElement($xml, $root, 'cbc:DueDate', dol_print_date($this->invoice->date_lim_reglement, '%Y-%m-%d'));
        }
        
        // L'élément du type de document diffère entre Invoice et CreditNote
        $this->addElement($xml, $root, $is_credit_note ? 'cbc:CreditNoteTypeCode' : 'cbc:InvoiceTypeCode', $is_credit_note ? '381' : '380');
        
        if ($this->invoice->note_public) {
            $this->addElement($xml, $root, 'cbc:Note', $this->invoice->note_public);
        }
        
        $this->addElement($xml, $root, 'cbc:DocumentCurrencyCode', $conf->currency);
        
        // BuyerReference (BT-10) est obligatoire pour PEPPOL (règle PEPPOL-EN16931-R003 :
        // une référence acheteur OU une référence de commande doit être présente).
        // On utilise la référence client si disponible, sinon la référence de la facture.
        $buyer_reference = !empty($this->invoice->ref_client) ? $this->invoice->ref_client : $this->invoice->ref;
        $this->addElement($xml, $root, 'cbc:BuyerReference', $buyer_reference);
        
        $this->addSupplierParty($xml, $root, $mysoc);
        $this->addCustomerParty($xml, $root, $this->company);
        $this->addPaymentMeans($xml, $root);
        $this->addTaxTotal($xml, $root);
        $this->addLegalMonetaryTotal($xml, $root);
        $this->addInvoiceLines($xml, $root, $is_credit_note);
        
        return $xml->saveXML();
    }
    
    private function addSupplierParty($xml, $root, $mysoc)
    {
        $supplier = $xml->createElement('cac:AccountingSupplierParty');
        $root->appendChild($supplier);
        
        $party = $xml->createElement('cac:Party');
        $supplier->appendChild($party);
        
        $ep = $this->getEndpoint($mysoc);
        if ($ep !== null) {
            $endpoint = $this->addElement($xml, $party, 'cbc:EndpointID', $ep['value']);
            $endpoint->setAttribute('schemeID', $ep['scheme']);
        }
        
        $partyName = $xml->createElement('cac:PartyName');
        $party->appendChild($partyName);
        $this->addElement($xml, $partyName, 'cbc:Name', $mysoc->name);
        
        $address = $xml->createElement('cac:PostalAddress');
        $party->appendChild($address);
        if ($mysoc->address) $this->addElement($xml, $address, 'cbc:StreetName', $mysoc->address);
        if ($mysoc->town) $this->addElement($xml, $address, 'cbc:CityName', $mysoc->town);
        if ($mysoc->zip) $this->addElement($xml, $address, 'cbc:PostalZone', $mysoc->zip);
        if ($mysoc->country_code) {
            $country = $xml->createElement('cac:Country');
            $address->appendChild($country);
            $this->addElement($xml, $country, 'cbc:IdentificationCode', $mysoc->country_code);
        }
        
        if ($mysoc->tva_intra) {
            $taxScheme = $xml->createElement('cac:PartyTaxScheme');
            $party->appendChild($taxScheme);
            $this->addElement($xml, $taxScheme, 'cbc:CompanyID', $mysoc->tva_intra);
            $taxSchemeNode = $xml->createElement('cac:TaxScheme');
            $taxScheme->appendChild($taxSchemeNode);
            $this->addElement($xml, $taxSchemeNode, 'cbc:ID', 'VAT');
        }

        $legalEntity = $xml->createElement('cac:PartyLegalEntity');
        $party->appendChild($legalEntity);
        $this->addElement($xml, $legalEntity, 'cbc:RegistrationName', $mysoc->name);
        if ($mysoc->idprof1) {
            $this->addElement($xml, $legalEntity, 'cbc:CompanyID', $mysoc->idprof1);
        }
        
        if ($mysoc->email || $mysoc->phone) {
            $contact = $xml->createElement('cac:Contact');
            $party->appendChild($contact);
            if ($mysoc->phone) $this->addElement($xml, $contact, 'cbc:Telephone', $mysoc->phone);
            if ($mysoc->email) $this->addElement($xml, $contact, 'cbc:ElectronicMail', $mysoc->email);
        }
    }
    
    private function addCustomerParty($xml, $root, $company)
    {
        $customer = $xml->createElement('cac:AccountingCustomerParty');
        $root->appendChild($customer);
        
        $party = $xml->createElement('cac:Party');
        $customer->appendChild($party);
        
        $ep = $this->getEndpoint($company);
        if ($ep !== null) {
            $endpoint = $this->addElement($xml, $party, 'cbc:EndpointID', $ep['value']);
            $endpoint->setAttribute('schemeID', $ep['scheme']);
        }
        
        $partyName = $xml->createElement('cac:PartyName');
        $party->appendChild($partyName);
        $this->addElement($xml, $partyName, 'cbc:Name', $company->name);
        
        $address = $xml->createElement('cac:PostalAddress');
        $party->appendChild($address);
        if ($company->address) $this->addElement($xml, $address, 'cbc:StreetName', $company->address);
        if ($company->town) $this->addElement($xml, $address, 'cbc:CityName', $company->town);
        if ($company->zip) $this->addElement($xml, $address, 'cbc:PostalZone', $company->zip);
        if ($company->country_code) {
            $country = $xml->createElement('cac:Country');
            $address->appendChild($country);
            $this->addElement($xml, $country, 'cbc:IdentificationCode', $company->country_code);
        }
        
        if ($company->tva_intra) {
            $taxScheme = $xml->createElement('cac:PartyTaxScheme');
            $party->appendChild($taxScheme);
            $this->addElement($xml, $taxScheme, 'cbc:CompanyID', $company->tva_intra);
            $taxSchemeNode = $xml->createElement('cac:TaxScheme');
            $taxScheme->appendChild($taxSchemeNode);
            $this->addElement($xml, $taxSchemeNode, 'cbc:ID', 'VAT');
        }
        
        $legalEntity = $xml->createElement('cac:PartyLegalEntity');
        $party->appendChild($legalEntity);
        $this->addElement($xml, $legalEntity, 'cbc:RegistrationName', $company->name);
        if ($company->idprof1) {
            $this->addElement($xml, $legalEntity, 'cbc:CompanyID', $company->idprof1);
        }
        
        if ($company->email || $company->phone) {
            $contact = $xml->createElement('cac:Contact');
            $party->appendChild($contact);
            if ($company->phone) $this->addElement($xml, $contact, 'cbc:Telephone', $company->phone);
            if ($company->email) $this->addElement($xml, $contact, 'cbc:ElectronicMail', $company->email);
        }
    }
    
    private function addPaymentMeans($xml, $root)
    {
        global $conf;
        
        $paymentMeans = $xml->createElement('cac:PaymentMeans');
        $root->appendChild($paymentMeans);
        
        $this->addElement($xml, $paymentMeans, 'cbc:PaymentMeansCode', '30');
        
        if ($this->invoice->ref) {
            $this->addElement($xml, $paymentMeans, 'cbc:PaymentID', $this->invoice->ref);
        }
        
     // Bank account - Récupérer depuis les comptes bancaires de la société
global $mysoc;

// Méthode 1: Via les constantes globales
$iban = '';
$bic = '';

if (!empty($conf->global->MAIN_INFO_IBAN)) {
    $iban = $conf->global->MAIN_INFO_IBAN;
    $bic = !empty($conf->global->MAIN_INFO_BIC) ? $conf->global->MAIN_INFO_BIC : '';
}

// Méthode 2: Via les comptes bancaires
// NOTE: dans Dolibarr la colonne IBAN s'appelle "iban_prefix" (nom historique),
// et le BIC "bic". Trier pour prendre le compte par défaut en premier.
if (empty($iban)) {
    $sql = "SELECT iban_prefix as iban, bic FROM ".MAIN_DB_PREFIX."bank_account";
    $sql .= " WHERE entity = ".((int) $conf->entity)." AND clos = 0 AND iban_prefix <> ''";
    $sql .= " ORDER BY courant DESC, rowid ASC";
    $resql = $this->db->query($sql);
    if ($resql && $this->db->num_rows($resql) > 0) {
        $obj = $this->db->fetch_object($resql);
        $iban = $obj->iban;
        $bic = $obj->bic;
    }
}

// Ajouter l'IBAN au XML
if (!empty($iban)) {
    $financialAccount = $xml->createElement('cac:PayeeFinancialAccount');
    $paymentMeans->appendChild($financialAccount);
    
    // Nettoyer l'IBAN (enlever espaces)
    $iban_clean = str_replace(' ', '', $iban);
    $this->addElement($xml, $financialAccount, 'cbc:ID', $iban_clean);
    
    if (!empty($bic)) {
        $financialInstitution = $xml->createElement('cac:FinancialInstitutionBranch');
        $financialAccount->appendChild($financialInstitution);
        $bic_clean = str_replace(' ', '', $bic);
        $this->addElement($xml, $financialInstitution, 'cbc:ID', $bic_clean);
    }
}
    }
    
    private function addTaxTotal($xml, $root)
    {
        $taxTotal = $xml->createElement('cac:TaxTotal');
        $root->appendChild($taxTotal);
        
        $taxAmount = $this->addElement($xml, $taxTotal, 'cbc:TaxAmount', number_format($this->invoice->total_tva, 2, '.', ''));
        $taxAmount->setAttribute('currencyID', $GLOBALS['conf']->currency);
        
        $tax_rates = array();
        foreach ($this->invoice->lines as $line) {
            $rate = (float)$line->tva_tx;
            if (!isset($tax_rates[$rate])) {
                $tax_rates[$rate] = array('base' => 0, 'amount' => 0);
            }
            $tax_rates[$rate]['base'] += $line->total_ht;
            $tax_rates[$rate]['amount'] += $line->total_tva;
        }
        
        foreach ($tax_rates as $rate => $amounts) {
            $taxSubtotal = $xml->createElement('cac:TaxSubtotal');
            $taxTotal->appendChild($taxSubtotal);
            
            $taxableAmount = $this->addElement($xml, $taxSubtotal, 'cbc:TaxableAmount', number_format($amounts['base'], 2, '.', ''));
            $taxableAmount->setAttribute('currencyID', $GLOBALS['conf']->currency);
            
            $taxAmountSub = $this->addElement($xml, $taxSubtotal, 'cbc:TaxAmount', number_format($amounts['amount'], 2, '.', ''));
            $taxAmountSub->setAttribute('currencyID', $GLOBALS['conf']->currency);
            
            $taxCategory = $xml->createElement('cac:TaxCategory');
            $taxSubtotal->appendChild($taxCategory);
            $this->addElement($xml, $taxCategory, 'cbc:ID', $this->getTaxCategoryCode($rate));
            $this->addElement($xml, $taxCategory, 'cbc:Percent', number_format($rate, 2, '.', ''));
            
            $taxScheme = $xml->createElement('cac:TaxScheme');
            $taxCategory->appendChild($taxScheme);
            $this->addElement($xml, $taxScheme, 'cbc:ID', 'VAT');
        }
    }
    
    private function addLegalMonetaryTotal($xml, $root)
    {
        $monetaryTotal = $xml->createElement('cac:LegalMonetaryTotal');
        $root->appendChild($monetaryTotal);
        
        $currency = $GLOBALS['conf']->currency;
        
        $lineExtension = $this->addElement($xml, $monetaryTotal, 'cbc:LineExtensionAmount', number_format($this->invoice->total_ht, 2, '.', ''));
        $lineExtension->setAttribute('currencyID', $currency);
        
        $taxExclusive = $this->addElement($xml, $monetaryTotal, 'cbc:TaxExclusiveAmount', number_format($this->invoice->total_ht, 2, '.', ''));
        $taxExclusive->setAttribute('currencyID', $currency);
        
        $taxInclusive = $this->addElement($xml, $monetaryTotal, 'cbc:TaxInclusiveAmount', number_format($this->invoice->total_ttc, 2, '.', ''));
        $taxInclusive->setAttribute('currencyID', $currency);
        
        $payable = $this->addElement($xml, $monetaryTotal, 'cbc:PayableAmount', number_format($this->invoice->total_ttc, 2, '.', ''));
        $payable->setAttribute('currencyID', $currency);
    }
    
    private function addInvoiceLines($xml, $root, $is_credit_note)
    {
        $line_tag = $is_credit_note ? 'cac:CreditNoteLine' : 'cac:InvoiceLine';
        $currency = $GLOBALS['conf']->currency;
        
        foreach ($this->invoice->lines as $i => $line) {
            $invoiceLine = $xml->createElement($line_tag);
            $root->appendChild($invoiceLine);
            
            $this->addElement($xml, $invoiceLine, 'cbc:ID', ($i + 1));
            
            $quantity = $this->addElement($xml, $invoiceLine, 'cbc:' . ($is_credit_note ? 'CreditedQuantity' : 'InvoicedQuantity'), $line->qty);
            $quantity->setAttribute('unitCode', 'C62');
            
            $lineExtension = $this->addElement($xml, $invoiceLine, 'cbc:LineExtensionAmount', number_format($line->total_ht, 2, '.', ''));
            $lineExtension->setAttribute('currencyID', $currency);
            
            $item = $xml->createElement('cac:Item');
            $invoiceLine->appendChild($item);
            
            // Description et Name (obligatoire pour PEPPOL)
            $description = $line->desc ? strip_tags($line->desc) : $line->product_label;
            $name = $line->product_label ? $line->product_label : $line->libelle;

// Si le nom est vide, utiliser la description tronquée
            if (empty($name) || trim($name) == '') {
            $name = $line->desc ? strip_tags($line->desc) : 'Article';
// Tronquer à 100 caractères pour le nom
            if (strlen($name) > 100) {
            $name = substr($name, 0, 97) . '...';
    }
}

            $this->addElement($xml, $item, 'cbc:Description', $description);
            $this->addElement($xml, $item, 'cbc:Name', $name);
        
            $classifiedTaxCategory = $xml->createElement('cac:ClassifiedTaxCategory');
            $item->appendChild($classifiedTaxCategory);
            $this->addElement($xml, $classifiedTaxCategory, 'cbc:ID', $this->getTaxCategoryCode($line->tva_tx));
            $this->addElement($xml, $classifiedTaxCategory, 'cbc:Percent', number_format($line->tva_tx, 2, '.', ''));
            
            $taxScheme = $xml->createElement('cac:TaxScheme');
            $classifiedTaxCategory->appendChild($taxScheme);
            $this->addElement($xml, $taxScheme, 'cbc:ID', 'VAT');
            
            $price = $xml->createElement('cac:Price');
            $invoiceLine->appendChild($price);
            
            $priceAmount = $this->addElement($xml, $price, 'cbc:PriceAmount', number_format($line->subprice, 2, '.', ''));
            $priceAmount->setAttribute('currencyID', $currency);
        }
    }
    
    /**
     * Détermine l'EndpointID Peppol (BT-34 / BT-49) d'une société.
     * Ordre de priorité : champ personnalisé peppyrus_id, idprof6, puis numéro de TVA.
     * Retourne array('scheme' => '9925', 'value' => 'BE0886776275') ou null.
     */
    private function getEndpoint($company)
    {
        $pid = '';
        if (!empty($company->array_options['options_peppyrus_id'])) {
            $pid = $company->array_options['options_peppyrus_id'];
        } elseif (!empty($company->idprof6)) {
            $pid = $company->idprof6;
        }

        // Format déjà qualifié "scheme:value" (ex: 9925:BE0886776275)
        if ($pid !== '' && strpos($pid, ':') !== false) {
            list($scheme, $value) = explode(':', $pid, 2);
            return array('scheme' => trim($scheme), 'value' => strtoupper(trim($value)));
        }
        if ($pid !== '') {
            return array('scheme' => '9925', 'value' => strtoupper($pid));
        }

        // Fallback : construire depuis le numéro de TVA intracommunautaire
        if (!empty($company->tva_intra)) {
            $vat = strtoupper(str_replace(array(' ', '.', '-'), '', $company->tva_intra));
            $cc = substr($vat, 0, 2);
            // Codes EAS par pays (liste non exhaustive, principaux pays UE)
            $eas = array(
                'BE' => '9925', 'NL' => '9944', 'FR' => '9957', 'DE' => '9930',
                'LU' => '9938', 'ES' => '9920', 'IT' => '9906', 'AT' => '9914',
            );
            if (isset($eas[$cc])) {
                return array('scheme' => $eas[$cc], 'value' => $vat);
            }
        }

        return null;
    }

    /**
     * Code de catégorie de TVA (UNCL5305) en fonction du taux.
     * 'S' = taux normal (>0), 'Z' = taux zéro (0%).
     */
    private function getTaxCategoryCode($rate)
    {
        return ((float) $rate > 0) ? 'S' : 'Z';
    }

    private function addElement($xml, $parent, $name, $value)
    {
        $element = $xml->createElement($name);
        $parent->appendChild($element);
        $element->appendChild($xml->createTextNode($value));
        return $element;
    }
}