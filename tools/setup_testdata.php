<?php
/**
 * DEV ONLY - CLI helper to seed reproducible test data and generate a UBL file.
 * Run: docker exec peppol-dolibarr php /var/www/html/custom/peppolnew/tools/setup_testdata.php
 * NOT part of the module runtime. Safe to delete.
 */

if (php_sapi_name() !== 'cli') {
    die("CLI only\n");
}

// Bootstrap Dolibarr (CLI)
$path = __DIR__.'/../../../'; // -> /var/www/html/
require_once $path.'master.inc.php';

require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';
require_once DOL_DOCUMENT_ROOT.'/user/class/user.class.php';
require_once DOL_DOCUMENT_ROOT.'/societe/class/societe.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/bank/class/account.class.php';
require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';

$user = new User($db);
$user->fetch(0, 'admin');
$user->getrights();

function out($m) { echo $m."\n"; }

// ---------------------------------------------------------------------------
// 1) Own company (mysoc) constants
// ---------------------------------------------------------------------------
$be_id = 0;
$resql = $db->query("SELECT rowid FROM ".MAIN_DB_PREFIX."c_country WHERE code = 'BE'");
if ($resql && ($o = $db->fetch_object($resql))) { $be_id = $o->rowid; }

$consts = array(
    'MAIN_INFO_SOCIETE_NOM'     => 'Demo Seller BV',
    'MAIN_INFO_SOCIETE_ADDRESS' => 'Grote Markt 1',
    'MAIN_INFO_SOCIETE_TOWN'    => 'Brussel',
    'MAIN_INFO_SOCIETE_ZIP'     => '1000',
    'MAIN_INFO_SOCIETE_COUNTRY' => $be_id.':BE:Belgium',
    'MAIN_INFO_TVAINTRA'        => 'BE0886776275',
    'MAIN_INFO_SIREN'           => '0886776275',
    'MAIN_INFO_SOCIETE_MAIL'    => 'invoicing@demoseller.be',
    'MAIN_INFO_SOCIETE_TEL'     => '+3221234567',
    'MAIN_MONNAIE'              => 'EUR',
);
foreach ($consts as $k => $v) {
    dolibarr_set_const($db, $k, $v, 'chaine', 0, '', $conf->entity);
}
out("Company constants set (BE id=$be_id).");

// ---------------------------------------------------------------------------
// 2) Own bank account with IBAN/BIC (this is what the IBAN fix reads)
// ---------------------------------------------------------------------------
$existing = 0;
$resql = $db->query("SELECT rowid FROM ".MAIN_DB_PREFIX."bank_account WHERE ref = 'DEMO-IBAN'");
if ($resql && ($o = $db->fetch_object($resql))) { $existing = $o->rowid; }

if (!$existing) {
    $acc = new Account($db);
    $acc->ref = 'DEMO-IBAN';
    $acc->label = 'Demo bank account';
    $acc->courant = 1;                 // current account
    $acc->clos = 0;
    $acc->type = 1;
    $acc->bank = 'BNP Paribas Fortis';
    $acc->iban = 'BE68539007547034';   // valid example IBAN -> stored in iban_prefix
    $acc->bic = 'GKCCBEBB';
    $acc->datec = dol_now();
    $acc->date_solde = dol_now();      // DateInitialBalance (required)
    $acc->solde = 0;
    $acc->country_id = $be_id;
    $acc->currency_code = 'EUR';
    $r = $acc->create($user);
    if ($r > 0) {
        out("Bank account created (id=".$acc->id.", IBAN BE68539007547034).");
    } else {
        out("Bank account create FAILED: ".$acc->error);
    }
} else {
    out("Bank account already exists (id=$existing).");
}

// ---------------------------------------------------------------------------
// 3) Customer third party (BE, with VAT -> EndpointID fallback)
// ---------------------------------------------------------------------------
$socid = 0;
$resql = $db->query("SELECT rowid FROM ".MAIN_DB_PREFIX."societe WHERE nom = 'Demo Buyer NV'");
if ($resql && ($o = $db->fetch_object($resql))) { $socid = $o->rowid; }

if (!$socid) {
    $soc = new Societe($db);
    $soc->name = 'Demo Buyer NV';
    $soc->client = 1;
    $soc->code_client = -1;
    $soc->address = 'Meir 100';
    $soc->zip = '2000';
    $soc->town = 'Antwerpen';
    $soc->country_id = $be_id;
    $soc->tva_intra = 'BE0897223670';
    $soc->idprof1 = '0897223670';
    $soc->email = 'ap@demobuyer.be';
    $socid = $soc->create($user);
    if ($socid > 0) {
        out("Customer created (id=$socid).");
    } else {
        out("Customer create FAILED: ".$soc->error);
        exit(1);
    }
} else {
    out("Customer already exists (id=$socid).");
}

// ---------------------------------------------------------------------------
// 4) Invoice with one 21% line, validated
// ---------------------------------------------------------------------------
$inv = new Facture($db);
$inv->socid = $socid;
$inv->date = dol_now();
$inv->cond_reglement_id = 1;
$inv->type = Facture::TYPE_STANDARD;
$invid = $inv->create($user);
if ($invid <= 0) {
    out("Invoice create FAILED: ".$inv->error);
    exit(1);
}
$inv->addline('Consultancy services - June 2026', 100.00, 5, 21.000, 0, 0, 0, 0, '', '', 0, 0, '', 'HT', 0, 0);
$inv->addline('Project setup fee', 250.00, 1, 21.000, 0, 0, 0, 0, '', '', 0, 0, '', 'HT', 0, 0);

$inv->fetch($invid);
$r = $inv->validate($user);
if ($r <= 0) {
    out("Invoice validate FAILED: ".$inv->error);
}
$inv->fetch($invid);
out("Invoice created & validated: ref=".$inv->ref." id=".$invid." total_ttc=".$inv->total_ttc);

// ---------------------------------------------------------------------------
// 5) Generate UBL
// ---------------------------------------------------------------------------
// Reload mysoc so the freshly-set constants are reflected
$mysoc = new Societe($db);
$mysoc->setMysoc($conf);

dol_include_once('/peppolnew/class/ublgenerator.class.php');
$gen = new UBLGenerator($db);
$xml = $gen->generateFromInvoice($invid);
if (!$xml) {
    out("UBL generation FAILED");
    exit(1);
}
$outfile = '/tmp/invoice_'.$inv->ref.'.xml';
file_put_contents($outfile, $xml);
out("UBL written to ".$outfile." (".strlen($xml)." bytes)");
out("INVOICE_ID=".$invid);
out("INVOICE_REF=".$inv->ref);
