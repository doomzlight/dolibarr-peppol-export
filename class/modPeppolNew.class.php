<?php
error_log("=== PEPPOLNEW HOOK FILE LOADED ===");
include_once DOL_DOCUMENT_ROOT.'/core/modules/DolibarrModules.class.php';

class modPeppolNew extends DolibarrModules
{
    public function __construct($db)
    {
        $this->db = $db;
        $this->numero = 500002;
        $this->rights_class = 'peppolnew';
        $this->family = "technic";
        $this->name = preg_replace('/^mod/i', '', get_class($this));
        $this->description = "PEPPOL Export NEW";
        $this->version = '1.0.0';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto = 'generic';
        
        $this->module_parts = array(
            'hooks' => array(
                'data' => array('invoicecard'),
                'entity' => '0'
            )
        );
    }
}