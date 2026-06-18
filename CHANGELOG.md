cat > CHANGELOG.md << 'EOFCHANGELOG'
# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

---

## [1.1.0] - 2026-06-18

### 🐛 Corrigé (conformité PEPPOL BIS Billing 3.0)

- **Parsing IBAN** : la requête SQL lisait une colonne `iban` inexistante. Dans
  Dolibarr l'IBAN est stocké dans `bank_account.iban_prefix`. La requête utilise
  désormais `iban_prefix AS iban` et un tri sûr (`courant DESC, rowid ASC`).
  L'IBAN/BIC apparaît maintenant correctement dans `cac:PayeeFinancialAccount`.
- **`BuyerReference` obligatoire** (règle `PEPPOL-EN16931-R003`) : toujours émis,
  avec repli sur la référence de la facture quand la référence client est absente.
- **Note de crédit** : émet `cbc:CreditNoteTypeCode` au lieu de `cbc:InvoiceTypeCode`.
- **`EndpointID`** (BT-34 / BT-49, obligatoire) : dérivé du champ `peppyrus_id` /
  `idprof6`, sinon construit depuis le numéro de TVA (code EAS par pays).
- **`PartyTaxScheme` fournisseur** : n'est plus émis vide lorsque le numéro de TVA
  est absent (un `CompanyID` vide était invalide).
- **Catégorie de TVA** : `Z` pour les taux à 0 %, `S` pour les taux normaux,
  au lieu d'un `S` codé en dur.
- **Descripteur de module en double** supprimé (`class/modPeppolNew.class.php`)
  pour éviter une redéclaration de classe.

Validé : facture de test générée → **statut `valid`** sur un validateur officiel
PEPPOL BIS Billing 3.0 (0 erreur, 0 avertissement).

---

## [1.0.0] - 2024-12-15

### 🎉 Première version publique

Version initiale complète et fonctionnelle du module Peppol Export pour Dolibarr.

### ✨ Ajouté

#### Fonctionnalités principales
- Génération de fichiers UBL 2.1 conformes PEPPOL BIS Billing 3.0
- Export de factures et avoirs au format XML
- Envoi automatique vers le réseau Peppol via API Peppyrus
- Recherche de participants dans l'annuaire Peppol
- Validation automatique des documents UBL
- Logs de transmission en base de données
- Interface intégrée dans les fiches factures Dolibarr

#### Interface utilisateur
- Trois boutons sur les factures validées :
  - 📄 Générer UBL : télécharge le fichier XML
  - 🔍 Rechercher Peppol : vérifie l'existence du participant
  - 📤 Envoyer Peppol : envoie la facture électroniquement

#### Configuration
- Page de configuration avec URL API, clé API et ID Peppol
- Support du champ "ID Professionnel 6" pour stocker les ID Peppol des clients
- Configuration des coordonnées bancaires (IBAN/BIC)

#### Outils de diagnostic
- Script de test de configuration (`tools/test_config.php`)
- Script de test de génération UBL (`tools/test_ubl.php`)
- Script de test d'envoi (`tools/test_send.php`)
- Page de diagnostic système (`admin/diagnostic.php`)

#### Documentation
- README.md complet avec exemples
- Guide d'installation (INSTALL.md)
- Guide de configuration (CONFIGURATION.md)
- Guide utilisateur (GUIDE_UTILISATEUR.md)
- Documentation des formats d'ID Peppol par pays

### 🔧 Technique

- Support Dolibarr 19.0 et supérieur
- PHP 7.0 minimum requis
- Utilisation de l'API Peppyrus v1
- Hooks Dolibarr pour intégration native
- Chemins relatifs portables (aucun chemin hardcodé)
- Gestion d'erreurs robuste
- Support multilingue (FR)

### 📦 Structure
```
peppolnew/
├── admin/          # Pages d'administration
├── class/          # Classes PHP (API, générateur UBL, hooks)
├── core/modules/   # Descripteur du module
├── docs/           # Documentation
├── js/             # Scripts JavaScript
├── langs/          # Traductions
├── lib/            # Bibliothèques
├── sql/            # Scripts SQL
└── tools/          # Outils de test et diagnostic
```

### 🙏 Crédits

- Développé avec l'aide de Claude (Anthropic AI)
- Tests et intégration : Pierre 
- API gratuite fournie par Peppyrus (Tigron)

---

## [Non publié]

### En développement

*Aucune modification en cours*

### Prévu

- Support des notes de crédit complexes
- Export batch de plusieurs factures
- Statistiques d'envoi dans le tableau de bord
- Support d'autres points d'accès Peppol
- Notifications par email des statuts d'envoi

---

## Notes de version

### Comment mettre à jour

1. Téléchargez la nouvelle version
2. Sauvegardez votre dossier `peppolnew/` actuel
3. Remplacez par la nouvelle version
4. Videz le cache Dolibarr
5. Vérifiez la configuration du module

### Compatibilité

| Version Module | Dolibarr      | PHP   |
|---------------|---------------|-------|
| 1.0.0         | 19.0 - 22.0+  | 7.0+  |

### Support

- **Bugs** : [GitHub Issues](https://github.com/marcrant1/dolibarr-peppol-export/issues)
- **Questions** : [Forum Dolibarr](https://forum.dolibarr.org)
- **Email** : maxomatic34@gmail.com

---

[1.0.0]: https://github.com/marcrant1/dolibarr-peppol-export/releases/tag/v1.0.0
EOFCHANGELOG

cat CHANGELOG.md
