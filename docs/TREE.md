# Structure du projet - Dolibarr Peppol Export

```
dolibarr-peppol-export/
│
├── 📄 README.md                         # Documentation principale
├── 📄 LICENSE                           # Licence GPL v3
├── 📄 CHANGELOG.md                      # Historique des versions
├── 📄 CONTRIBUTORS.md                   # Liste des contributeurs
├── 📄 .gitignore                        # Fichiers à ignorer par Git
├── 📄 install_peppolexport.php          # Installateur automatique
│
├── 📁 peppolexport/                     # Module principal
│   │
│   ├── 📁 admin/                        # Interface d'administration
│   │   ├── setup.php                    # Page de configuration
│   │   └── diagnostic.php               # Outil de diagnostic système
│   │
│   ├── 📁 class/                        # Classes PHP
│   │   ├── actions_peppolexport.class.php    # Hooks Dolibarr
│   │   ├── peppolapi.class.php               # Client API Peppyrus
│   │   └── ublgenerator.class.php            # Générateur UBL 2.1
│   │
│   ├── 📁 core/                         # Composants Dolibarr
│   │   ├── modules/
│   │   │   └── modPeppolExport.class.php     # Définition du module
│   │   └── triggers/
│   │       └── interface_99_modPeppolExport_PeppolExportTrigger.class.php
│   │
│   ├── 📁 js/                           # JavaScript
│   │   └── peppolexport.js              # Interface utilisateur (AJAX)
│   │
│   ├── 📁 langs/                        # Traductions
│   │   └── fr_FR/
│   │       └── peppolexport.lang        # Français
│   │
│   ├── 📁 lib/                          # Bibliothèques
│   │   └── peppolexport.lib.php         # Fonctions utilitaires
│   │
│   ├── 📁 sql/                          # Scripts SQL
│   │   └── llx_peppolexport_log.sql     # Table de logs
│   │
│   └── 📄 peppol_send.php               # Script d'envoi AJAX
│
├── 📁 langs/                            # Traductions supplémentaires
│   └── en_US/
│       └── peppolexport.lang            # Anglais
│
├── 📁 docs/                             # Documentation détaillée
│   ├── installation.md                  # Guide d'installation
│   ├── configuration.md                 # Guide de configuration
│   └── testing.md                       # Guide de test
│
└── 📁 tools/                            # Outils de test
    ├── README.md                        # Documentation des outils
    ├── test_config.php                  # Test de configuration
    ├── test_ubl.php                     # Test de génération UBL
    └── test_send.php                    # Test d'envoi

```

## Description des dossiers et fichiers

### 📁 Racine du projet

| Fichier | Description |
|---------|-------------|
| `README.md` | Documentation principale avec guide d'utilisation |
| `LICENSE` | Licence GPL v3 complète |
| `CHANGELOG.md` | Historique des versions et modifications |
| `CONTRIBUTORS.md` | Liste des contributeurs et guide de contribution |
| `.gitignore` | Fichiers à exclure du versioning Git |
| `install_peppolexport.php` | Installateur automatique (à supprimer après usage) |

### 📁 peppolexport/ (Module principal)

#### 📁 admin/
Interface d'administration accessible depuis Dolibarr

- `setup.php` : Page de configuration des paramètres (API URL, clé API, ID Peppol)
- `diagnostic.php` : Outil de diagnostic complet du système

#### 📁 class/
Classes PHP principales du module

- `actions_peppolexport.class.php` : Hooks Dolibarr pour intégration dans les pages
- `peppolapi.class.php` : Client API pour communiquer avec Peppyrus
- `ublgenerator.class.php` : Générateur de fichiers UBL 2.1 (PEPPOL BIS Billing 3.0)

#### 📁 core/
Composants cœur suivant l'architecture Dolibarr

- `modules/modPeppolExport.class.php` : Définition du module (configuration, droits, constantes)
- `triggers/interface_99_modPeppolExport_PeppolExportTrigger.class.php` : Triggers pour événements Dolibarr

#### 📁 js/
Scripts JavaScript

- `peppolexport.js` : Gestion de l'interface utilisateur (boutons, AJAX, popups)

#### 📁 langs/
Fichiers de traduction

- `fr_FR/peppolexport.lang` : Traduction française
- (Les autres langues sont dans `/langs/` à la racine)

#### 📁 lib/
Fonctions utilitaires

- `peppolexport.lib.php` : Fonctions helper (récupération ID Peppol, formatage, etc.)

#### 📁 sql/
Scripts de base de données

- `llx_peppolexport_log.sql` : Définition de la table de logs d'envoi

#### Fichiers à la racine de peppolexport/

- `peppol_send.php` : Script AJAX pour gérer les envois vers Peppol

### 📁 langs/
Traductions supplémentaires (au niveau du projet)

- `en_US/peppolexport.lang` : Traduction anglaise

### 📁 docs/
Documentation détaillée

- `installation.md` : Guide d'installation pas à pas (manuel, Git, automatique)
- `configuration.md` : Guide de configuration complet (module, entreprise, clients)
- `testing.md` : Guide de test avec checklist et résolution de problèmes

### 📁 tools/
Outils de test et diagnostic

⚠️ **À supprimer en production !**

- `README.md` : Documentation des outils de test
- `test_config.php` : Teste la configuration du module
- `test_ubl.php` : Teste la génération de fichiers UBL
- `test_send.php` : Teste l'envoi vers Peppol

## Fichiers générés automatiquement

Ces fichiers sont créés automatiquement lors de l'utilisation :

```
peppolexport/
└── temp/                                # Fichiers temporaires (créé à la demande)
    └── *.xml                            # Fichiers UBL temporaires
```

## Base de données

### Table : llx_peppolexport_log

Structure de la table de logs :

```sql
llx_peppolexport_log
├── rowid               # ID auto-incrémenté
├── fk_facture          # Référence à la facture
├── date_export         # Date/heure de l'envoi
├── recipient_id        # ID Peppol du destinataire
├── document_type       # Type de document (Invoice/CreditNote)
├── status              # Statut (success/failed)
├── response_message    # Réponse de l'API
└── fk_user_export      # Utilisateur ayant effectué l'envoi
```

## Permissions recommandées

```bash
# Permissions des dossiers
chmod 755 peppolexport/
chmod 755 peppolexport/*/

# Permissions des fichiers
chmod 644 peppolexport/*.php
chmod 644 peppolexport/*/*.php
chmod 644 peppolexport/sql/*.sql
chmod 644 peppolexport/js/*.js
chmod 644 peppolexport/langs/*/*.lang

# Fichier de configuration (si vous en créez un)
chmod 600 peppolexport/config_local.php  # Lecture seule pour le propriétaire
```

## Taille du projet

Estimation des tailles :

```
Module total :           ~300 KB
├── Code PHP :          ~150 KB
├── Documentation :     ~100 KB
├── SQL/JS/Langs :      ~50 KB
└── Outils de test :    ~50 KB (à supprimer en prod)
```

## Dépendances

### Dépendances PHP
- PHP ≥ 7.0
- Extensions : curl, json, xml, dom, openssl

### Dépendances Dolibarr
- Dolibarr ≥ 11.0
- Module Factures activé

### Dépendances externes
- API Peppyrus (https://api.peppyrus.be/v1)
- Réseau Peppol

## Ressources externes

Le module ne contient pas de bibliothèques externes (vendor/) car il utilise uniquement :
- Les classes natives de Dolibarr
- Les extensions PHP standards
- L'API REST de Peppyrus

---

*Cette structure suit les conventions de développement de modules Dolibarr.*
