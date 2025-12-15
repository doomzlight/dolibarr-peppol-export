-- Table de logs pour les exports Peppol
CREATE TABLE IF NOT EXISTS llx_peppolnew_log (
    rowid INTEGER AUTO_INCREMENT PRIMARY KEY,
    fk_facture INTEGER NOT NULL,
    date_export DATETIME NOT NULL,
    recipient_id VARCHAR(255),
    document_type VARCHAR(255),
    status VARCHAR(50),
    response_message TEXT,
    fk_user_export INTEGER,
    INDEX idx_fk_facture (fk_facture),
    INDEX idx_date_export (date_export)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;