<?php
/**
 * Test UBL Generation
 * 
 * This tool tests the UBL XML generation for an invoice
 * 
 * @package    DolibarrPeppolExport
 * @author     Contributors
 * @copyright  2024 Contributors
 * @license    GPL-3.0-or-later
 * @link       https://github.com/votre-username/dolibarr-peppol-export
 * 
 * ⚠️ WARNING: Remove this file in production!
 */

// Load Dolibarr environment
$res = 0;
for ($i = 1; $i <= 10; $i++) {
    $path = __DIR__ . str_repeat('/..', $i) . '/main.inc.php';
    if (file_exists($path)) {
        $res = @include $path;
        if ($res) break;
    }
}

if (!$res || !defined('DOL_DOCUMENT_ROOT')) {
    die('❌ Error: Cannot load Dolibarr. Please check installation.');
}

require_once DOL_DOCUMENT_ROOT.'/compta/facture/class/facture.class.php';
dol_include_once('/peppolnew/class/ublgenerator.class.php');

// Security check
if (!$user->rights->facture->lire) {
    accessforbidden();
}

// Get invoice ID from URL
$invoice_id = GETPOST('id', 'int');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Test UBL Generation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .form { margin: 20px 0; }
        .form label { display: inline-block; width: 150px; font-weight: bold; }
        .form input { padding: 8px; width: 300px; border: 1px solid #ddd; border-radius: 3px; }
        .form button { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; margin-left: 150px; }
        .form button:hover { background: #0056b3; }
        .result { margin-top: 20px; }
        .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .xml-output { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px; overflow-x: auto; }
        .xml-output pre { margin: 0; white-space: pre-wrap; word-wrap: break-word; }
        .info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0; }
        .download-btn { background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 3px; display: inline-block; margin: 10px 0; }
        .download-btn:hover { background: #218838; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Test UBL Generation</h1>
        
        <div class="info">
            ⚠️ <strong>Warning:</strong> This tool is for testing purposes only. Remove it in production!
        </div>

        <div class="form">
            <form method="GET">
                <label>Invoice ID:</label>
                <input type="number" name="id" value="<?php echo $invoice_id; ?>" required>
                <br><br>
                <button type="submit">Generate UBL</button>
            </form>
        </div>

        <?php
        if ($invoice_id > 0) {
            // Load invoice
            $invoice = new Facture($db);
            $result = $invoice->fetch($invoice_id);
            
            if ($result > 0) {
                echo '<div class="success">';
                echo '✅ <strong>Invoice found:</strong> ' . $invoice->ref . '<br>';
                echo '<strong>Customer:</strong> ' . $invoice->thirdparty->name . '<br>';
                echo '<strong>Amount:</strong> ' . price($invoice->total_ttc) . '<br>';
                echo '<strong>Status:</strong> ' . $invoice->getLibStatut(1);
                echo '</div>';
                
                // Generate UBL
                echo '<h2>🔧 Generating UBL XML...</h2>';
                
                $ublGenerator = new UBLGenerator($db);
                $ubl_xml = $ublGenerator->generateFromInvoice($invoice_id);
                
                if ($ubl_xml) {
                    echo '<div class="success">';
                    echo '✅ <strong>UBL generated successfully!</strong><br>';
                    echo 'Size: ' . strlen($ubl_xml) . ' bytes';
                    echo '</div>';
                    
                    // Download link
                    $filename = $invoice->ref . '.xml';
                    $base64 = base64_encode($ubl_xml);
                    echo '<a href="data:application/xml;base64,' . $base64 . '" download="' . $filename . '" class="download-btn">📥 Download XML</a>';
                    
                    // Display XML
                    echo '<h2>📄 Generated XML</h2>';
                    echo '<div class="xml-output">';
                    echo '<pre>' . htmlspecialchars($ubl_xml) . '</pre>';
                    echo '</div>';
                    
                    // Validate XML
                    echo '<h2>✔️ XML Validation</h2>';
                    $dom = new DOMDocument();
                    if ($dom->loadXML($ubl_xml)) {
                        echo '<div class="success">✅ XML is well-formed</div>';
                    } else {
                        echo '<div class="error">❌ XML is not well-formed</div>';
                    }
                    
                } else {
                    echo '<div class="error">';
                    echo '❌ <strong>Error:</strong> Failed to generate UBL XML';
                    echo '</div>';
                }
                
            } else {
                echo '<div class="error">';
                echo '❌ <strong>Error:</strong> Invoice not found (ID: ' . $invoice_id . ')';
                echo '</div>';
            }
        }
        ?>
        
        <hr style="margin: 30px 0;">
        <p><a href="test_config.php">← Back to Configuration Test</a></p>
    </div>
</body>
</html>
