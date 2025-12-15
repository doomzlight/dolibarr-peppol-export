# Dolibarr Peppol Export Module

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Dolibarr](https://img.shields.io/badge/Dolibarr-11.0%2B-green.svg)](https://www.dolibarr.org/)
[![PHP](https://img.shields.io/badge/PHP-7.0%2B-purple.svg)](https://www.php.net/)

Module Dolibarr pour exporter et envoyer des factures et avoirs au format UBL 2.1 (PEPPOL BIS Billing 3.0) vers le réseau Peppol via le point d'accès gratuit [Peppyrus](https://peppyrus.be).

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Structure du module](#-structure-du-module)
- [FAQ](#-faq)
- [Contribution](#-contribution)
- [Licence](#-licence)
- [Crédits](#-crédits)

## ✨ Fonctionnalités

- ✅ **Génération de fichiers UBL 2.1** conformes PEPPOL BIS Billing 3.0
- ✅ **Export factures et avoirs** au format XML
- ✅ **Envoi automatique** vers le réseau Peppol via API Peppyrus
- ✅ **Recherche de participants** dans l'annuaire Peppol
- ✅ **Validation automatique** des documents UBL
- ✅ **Logs d'envoi** en base de données
- ✅ **Interface intégrée** dans les fiches factures Dolibarr
- ✅ **Gratuit** grâce à Peppyrus

## 🔧 Prérequis

### Technique
- **Dolibarr** 11.0 ou supérieur (testé sur 19.0 et 20.x)
- **PHP** 7.0 ou supérieur
- Extensions PHP requises :
  - `curl`
  - `json`
  - `openssl`
  - `xml`
  - `dom`

### Compte Peppyrus
- Créer un compte gratuit sur [peppyrus.be](https://peppyrus.be)
- Obtenir une clé API depuis votre tableau de bord
- Enregistrer votre participant ID Peppol

## 📥 Installation

### Méthode 1 : Installation automatique (recommandée)

1. **Téléchargez** le fichier `install_peppolexport.php`
2. **Placez-le** dans `/htdocs/custom/` de votre Dolibarr
3. **Accédez** à `https://votre-dolibarr.com/custom/install_peppolexport.php`
4. **Suivez** les instructions à l'écran
5. **Supprimez** le fichier d'installation après usage

### Méthode 2 : Installation manuelle

1. **Téléchargez** le module :
   ```bash
   cd /chemin/vers/dolibarr/htdocs/custom/
   git clone https://github.com/votre-username/dolibarr-peppol-export.git peppolexport
   ```

2. **Vérifiez les permissions** :
   ```bash
   chmod -R 755 peppolexport/
   chown -R www-data:www-data peppolexport/
   ```

3. **Activez le module** :
   - Connectez-vous à Dolibarr en tant qu'administrateur
   - Allez dans **Configuration** > **Modules/Applications**
   - Recherchez **"Peppol Export"**
   - Cliquez sur **Activer**

## ⚙️ Configuration

### 1. Configuration du module

**Configuration** > **Modules** > **Peppol Export** > ⚙️

Configurez les paramètres suivants :

| Paramètre | Description | Exemple |
|-----------|-------------|---------|
| **URL API** | URL de l'API Peppyrus | `https://api.peppyrus.be/v1` |
| **Clé API** | Votre clé API Peppyrus | Obtenue depuis peppyrus.be |
| **Votre ID Peppol** | Votre identifiant Peppol | `9925:be0838264694` |

### 2. Configuration des coordonnées bancaires

Pour éviter l'erreur de validation **BR-61**, configurez vos coordonnées bancaires :

**Configuration** > **Société/Organisation** > Section "Informations bancaires"

- **IBAN** : Votre numéro IBAN (sans espaces)
- **BIC/SWIFT** : Votre code BIC

### 3. Configuration des clients

Pour chaque client Peppol, configurez son identifiant :

1. Ouvrez la **fiche tiers**
2. Ajoutez dans un champ personnalisé ou **"ID Prof 6"**
3. Format : `9925:be0123456789` (Belgique) ou selon le pays

#### Formats d'identifiants par pays

| Pays | Scheme | Format | Exemple |
|------|--------|--------|---------|
| 🇧🇪 Belgique | 9925 | 9925:beXXXXXXXXXX | `9925:be0123456789` |
| 🇳🇱 Pays-Bas | 9925 | 9925:nlXXXXXXXXXX | `9925:nl123456789B01` |
| 🇫🇷 France | 9957 | 9957:frXXXXXXXXXXX | `9957:fr12345678901` |
| 🇩🇪 Allemagne | 9930 | 9930:deXXXXXXXXXX | `9930:de123456789` |

[Liste complète des schemes](https://docs.peppol.eu/poacc/billing/3.0/codelist/eas/)

## 🚀 Utilisation

### Envoyer une facture vers Peppol

1. **Ouvrez une facture validée** dans Dolibarr
2. Trois boutons apparaissent en bas de page :
   - 📄 **Générer UBL** : Télécharge le fichier XML
   - 🔍 **Rechercher dans Peppol** : Vérifie que le client existe sur Peppol
   - 📤 **Envoyer vers Peppol** : Envoie la facture électroniquement

3. **Cliquez sur "Envoyer vers Peppol"**
4. Confirmez l'envoi
5. Vérifiez le statut sur [customer.peppyrus.be](https://customer.peppyrus.be)

### Vérifier les envois

Connectez-vous à votre tableau de bord Peppyrus pour :
- ✅ Voir les factures envoyées
- ✅ Vérifier les statuts de transmission
- ✅ Consulter les erreurs de validation
- ✅ Suivre les accusés de réception

## 📁 Structure du module

```
peppolexport/
├── README.md                           # Documentation principale
├── LICENSE                             # Licence GPL v3
├── CHANGELOG.md                        # Historique des versions
├── admin/                              # Interface d'administration
│   ├── setup.php                       # Page de configuration
│   └── diagnostic.php                  # Outil de diagnostic
├── class/                              # Classes PHP
│   ├── actions_peppolexport.class.php  # Hooks Dolibarr
│   ├── peppolapi.class.php             # Client API Peppyrus
│   └── ublgenerator.class.php          # Générateur UBL 2.1
├── core/                               # Composants Dolibarr
│   ├── modules/
│   │   └── modPeppolExport.class.php   # Définition du module
│   └── triggers/
│       └── interface_99_modPeppolExport_PeppolExportTrigger.class.php
├── js/                                 # JavaScript
│   └── peppolexport.js                 # Interface utilisateur
├── langs/                              # Traductions
│   ├── en_US/
│   │   └── peppolexport.lang           # Anglais
│   └── fr_FR/
│       └── peppolexport.lang           # Français
├── lib/                                # Bibliothèques
│   └── peppolexport.lib.php            # Fonctions utilitaires
├── sql/                                # Scripts SQL
│   └── llx_peppolexport_log.sql        # Table de logs
├── peppol_send.php                     # Script d'envoi AJAX
├── docs/                               # Documentation détaillée
│   ├── installation.md                 # Guide d'installation
│   ├── configuration.md                # Guide de configuration
│   ├── testing.md                      # Guide de test
│   └── api-reference.md                # Référence API
└── tools/                              # Outils de test
    ├── test_config.php                 # Tester la configuration
    ├── test_ubl.php                    # Tester la génération UBL
    └── test_send.php                   # Tester l'envoi
```

## 🧪 Outils de test

Le module inclut des outils de diagnostic dans le dossier `/tools/` :

### test_config.php
Vérifie la configuration du module :
```bash
https://votre-dolibarr/custom/peppolexport/tools/test_config.php
```

### test_ubl.php
Teste la génération d'un fichier UBL :
```bash
https://votre-dolibarr/custom/peppolexport/tools/test_ubl.php?id=123
```

### test_send.php
Teste l'envoi vers Peppol :
```bash
https://votre-dolibarr/custom/peppolexport/tools/test_send.php?id=123
```

⚠️ **Supprimez le dossier `/tools/` en production** pour des raisons de sécurité !

## ❓ FAQ

### Le module n'apparaît pas dans la liste

1. Vérifiez que le dossier est bien dans `/htdocs/custom/peppolexport/`
2. Vérifiez les permissions (755 pour les dossiers, 644 pour les fichiers)
3. Videz le cache : **Configuration** > **Sécurité** > **Vider le cache**

### Erreur "Sender Peppol ID not configured"

Votre ID Peppol n'est pas configuré dans le module. Allez dans la configuration et ajoutez-le.

### Erreur BR-61 : IBAN manquant

Configurez vos coordonnées bancaires dans **Configuration** > **Société/Organisation**.

### Erreur BR-25 : Item name manquant

Les lignes de facture doivent avoir un libellé. Vérifiez que vos produits ont un nom.

### Les boutons n'apparaissent pas sur les factures

1. La facture doit être **validée** (statut 1 ou 2)
2. Videz le cache navigateur (Ctrl+Shift+R)
3. Vérifiez la console JavaScript (F12) pour les erreurs

### Comment tester sans envoyer de vraies factures ?

Peppyrus propose un environnement de test :
- API Test : `https://api.test.peppyrus.be/v1`
- Frontend Test : [customer.test.peppyrus.be](https://customer.test.peppyrus.be)

Configurez l'URL de test dans le module pour vos tests.

## 🤝 Contribution

Les contributions sont les bienvenues ! 

### Comment contribuer

1. **Fork** le projet
2. Créez une **branche** pour votre fonctionnalité (`git checkout -b feature/amelioration`)
3. **Committez** vos changements (`git commit -am 'Ajout nouvelle fonctionnalité'`)
4. **Poussez** vers la branche (`git push origin feature/amelioration`)
5. Créez une **Pull Request**

### Signaler un bug

Utilisez les [Issues GitHub](https://github.com/votre-username/dolibarr-peppol-export/issues) avec :
- Description détaillée du problème
- Version de Dolibarr
- Version du module
- Messages d'erreur (logs)
- Étapes pour reproduire

## 📜 Licence

Ce projet est sous licence **GNU General Public License v3.0**.

Vous êtes libre de :
- ✅ Utiliser le logiciel commercialement
- ✅ Modifier le code source
- ✅ Distribuer des copies
- ✅ Utiliser et modifier en privé

Sous conditions :
- 📄 Inclure la licence et les droits d'auteur
- 📄 Rendre disponible le code source
- 📄 Documenter les modifications
- 📄 Utiliser la même licence pour les travaux dérivés

Voir [LICENSE](LICENSE) pour plus de détails.

## 💝 Crédits

### Développement
- **Développé avec l'aide de** : [Claude](https://claude.ai) (Anthropic AI)
- **Contributeurs** : Voir [CONTRIBUTORS.md](CONTRIBUTORS.md)

### Technologies utilisées
- [Dolibarr ERP CRM](https://www.dolibarr.org/) - Plateforme ERP/CRM
- [Peppyrus](https://peppyrus.be) - Point d'accès Peppol gratuit
- [Peppol](https://peppol.org) - Réseau de facturation électronique
- [UBL 2.1](http://docs.oasis-open.org/ubl/UBL-2.1.html) - Format de document standardisé

### Remerciements
- Communauté Dolibarr pour l'écosystème de modules
- [Tigron](https://www.tigron.be) pour Peppyrus et leur API bien documentée
- Tous les contributeurs du projet

## 🔗 Liens utiles

- [Documentation Dolibarr](https://wiki.dolibarr.org/)
- [Documentation Peppyrus](https://peppyrus.be)
- [Spécifications PEPPOL BIS Billing 3.0](https://docs.peppol.eu/poacc/billing/3.0/)
- [Format UBL 2.1](http://docs.oasis-open.org/ubl/UBL-2.1.html)
- [Annuaire Peppol](https://directory.peppol.eu/)

---

## 📞 Support

- 🐛 **Bugs** : [Issues GitHub](https://github.com/votre-username/dolibarr-peppol-export/issues)
- 💬 **Questions** : [Discussions GitHub](https://github.com/votre-username/dolibarr-peppol-export/discussions)
- 🌐 **Forum Dolibarr** : [forum.dolibarr.org](https://forum.dolibarr.org)

---

⭐ **Si ce module vous est utile, n'hésitez pas à mettre une étoile sur GitHub !**

---

*Dernière mise à jour : Novembre 2024*
