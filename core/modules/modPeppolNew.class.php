<?php
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
        $this->description = "Module pour exporter les factures au format UBL vers Peppol";
        $this->version = '1.0.0';
        $this->const_name = 'MAIN_MODULE_'.strtoupper($this->name);
        $this->picto = 'generic';
        
        // Page de configuration
        $this->config_page_url = array("setup.php@peppolnew");
        
        // Hooks
        $this->module_parts = array(
            'hooks' => array(
                'data' => array('invoicecard'),
                'entity' => '0'
            )
        );
        
        // Constantes de configuration
        $this->const = array(
            0 => array('PEPPOLNEW_API_URL', 'chaine', 'https://api.peppyrus.be/v1', 'URL de l\'API Peppol', 0),
            1 => array('PEPPOLNEW_API_KEY', 'chaine', '', 'Clé API Peppol', 0),
            2 => array('PEPPOLNEW_PEPPOL_ID', 'chaine', '', 'Votre ID Peppol', 0),
        );
        
        // Permissions
        $this->rights = array();
        $r = 0;
        $r++;
        $this->rights[$r][0] = $this->numero + $r;
        $this->rights[$r][1] = 'Exporter les factures vers Peppol';
        $this->rights[$r][3] = 0;
        $this->rights[$r][4] = 'export';
    }
    
    public function init($options = '')
    {
        $result = $this->_load_tables('/peppolnew/sql/');
        if ($result < 0) return -1;
        return $this->_init(array(), $options);
    }
}