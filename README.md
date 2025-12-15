cat > README.md << 'EOFREADME'
# 📨 Dolibarr Peppol Export Module

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![Dolibarr](https://img.shields.io/badge/Dolibarr-19%2B-green.svg)](https://www.dolibarr.org)

Module Dolibarr pour exporter et envoyer des factures au format **UBL 2.1** (PEPPOL BIS Billing 3.0) vers le réseau **Peppol** via le point d'accès gratuit **Peppyrus**.

---

## ✨ Fonctionnalités

- ✅ **Génération UBL 2.1** conforme PEPPOL BIS Billing 3.0
- ✅ **Export factures et avoirs** au format XML
- ✅ **Envoi automatique** vers le réseau Peppol via API Peppyrus
- ✅ **Recherche de participants** dans l'annuaire Peppol
- ✅ **Validation automatique** des documents UBL
- ✅ **Logs d'envoi** en base de données
- ✅ **Interface intégrée** dans les fiches factures Dolibarr
- ✅ **100% Gratuit** grâce à Peppyrus

---

## 🔧 Prérequis

### Technique

- **Dolibarr** : Version 19.0 ou supérieure (testé sur 19.0 et 22.0)
- **PHP** : Version 7.0 ou supérieure
- **Extensions PHP requises** :
  - `curl`
  - `json`
  - `xml`
  - `dom`

### Compte Peppyrus

1. Créer un compte gratuit sur [peppyrus.be](https://peppyrus.be)
2. Obtenir une clé API depuis votre tableau de bord
3. Enregistrer votre ID Peppol (format : `9925:beXXXXXXXXXX`)

---

## 📥 Installation

### Méthode 1 : Installation manuelle (recommandée)

1. **Téléchargez** l'archive du module
2. **Extrayez** dans `/chemin/vers/dolibarr/htdocs/custom/peppolnew/`
3. **Permissions** :
```bash
   cd /chemin/vers/dolibarr/htdocs/custom/
   chown -R www-data:www-data peppolnew
   chmod -R 755 peppolnew
```
4. **Activez** le module dans Dolibarr :
   - Configuration → Modules/Applications
   - Recherchez "PeppolNew"
   - Cliquez sur **Activer**

### Méthode 2 : Via Git
```bash
cd /chemin/vers/dolibarr/htdocs/custom/
git clone https://github.com/marcrant1/dolibarr-peppol-export.git peppolnew
chown -R www-data:www-data peppolnew
chmod -R 755 peppolnew
```

---

## ⚙️ Configuration

### 1. Activer le champ ID Peppol pour les tiers

Le module utilise le champ **"ID Professionnel 6"** pour stocker l'ID Peppol des clients.

**Activez-le** :
1. Configuration → Modules → **Tiers**
2. Section **"Identifiants professionnels"**
3. Cochez **"Activer ID Professionnel 6"**
4. Dans **"Libellé de l'identifiant 6"**, saisissez : `ID Peppol`
5. Cliquez sur **Enregistrer**

### 2. Configurer le module

1. Configuration → Modules → **PeppolNew**
2. Cliquez sur **⚙️** (roue dentée)
3. Renseignez :
   - **URL API** : `https://api.peppyrus.be/v1`
   - **Clé API** : Votre clé obtenue sur peppyrus.be
   - **Votre ID Peppol** : Format `9925:beXXXXXXXXXX`
4. Cliquez sur **Enregistrer**

### 3. Configurer VOTRE société (émetteur)

**Important** : Votre ID Peppol doit aussi être dans votre fiche société !

1. Configuration → **Société/Organisation**
2. Onglet **"Carte"**
3. Cherchez le champ **"ID Peppol"** (ou "ID Prof 6") sinon il faut le créer: dans Module/Tiers/Attribut suplémentaire/ajouter un champs ID Prof 6**
4. Entrez votre ID : `9925:beXXXXXXXXXX`

### 4. Configurer vos coordonnées bancaires

Pour éviter l'erreur de validation **BR-61** :

1. Configuration → **Société/Organisation**
2. Section **"Informations bancaires"**
3. Renseignez :
   - **IBAN** : Votre numéro IBAN (sans espaces)
   - **BIC/SWIFT** : Votre code BIC

### 5. Configurer les clients

Pour chaque client Peppol :

1. **Ouvrez la fiche du tiers**
2. **Onglet "Carte"**
3. Remplissez le champ **"ID Prof 6" que vous pouvez nommer identifiant Peppol dans Module/Tiers/Attribut **
4. Format : `9925:beXXXXXXXXXX` (voir formats ci-dessous)

#### 📋 Formats d'ID Peppol par pays

| Pays | Préfixe | Format | Exemple |
|------|---------|--------|---------|
| 🇧🇪 **Belgique** | 9925 | `9925:beXXXXXXXXXX` | `9925:be0838264694` |
| 🇫🇷 **France** | 9957 | `9957:frXXXXXXXXXXX` | `9957:fr12345678901` |
| 🇳🇱 **Pays-Bas** | 9925 | `9925:nlXXXXXXXXXX` | `9925:nl123456789B01` |
| 🇩🇪 **Allemagne** | 9930 | `9930:deXXXXXXXXXX` | `9930:de123456789` |

[Liste complète des schemes Peppol](https://docs.peppol.eu/poacc/billing/3.0/codelist/eas/)

---

## 🚀 Utilisation

### Sur une facture validée

Trois boutons apparaissent en bas de la fiche facture :

1. **📄 Générer UBL**
   - Télécharge le fichier XML au format UBL 2.1
   - Permet de vérifier le contenu avant envoi

2. **🔍 Rechercher dans Peppol**
   - Vérifie que le client existe dans l'annuaire Peppol
   - Confirme que son ID Peppol est correct

3. **📤 Envoyer vers Peppol**
   - Génère le fichier UBL
   - L'envoie via l'API Peppyrus
   - Enregistre un log de transmission

### Vérifier les envois

Connectez-vous à votre tableau de bord Peppyrus :
- URL : [customer.peppyrus.be](https://customer.peppyrus.be)
- Consultez les factures envoyées
- Vérifiez les statuts de transmission
- Suivez les accusés de réception

---

## 📁 Structure du module
```
peppolnew/
├── README.md                    # Ce fichier
├── INSTALL.md                   # Guide d'installation
├── CONFIGURATION.md             # Guide de configuration détaillé
├── LICENSE                      # Licence GPL v3
├── admin/
│   ├── setup.php               # Page de configuration
│   └── diagnostic.php          # Outil de diagnostic
├── class/
│   ├── actions_peppolnew.class.php   # Hooks Dolibarr
│   ├── peppolapi.class.php           # Client API Peppyrus
│   └── ublgenerator.class.php        # Générateur UBL 2.1
├── core/
│   └── modules/
│       └── modPeppolNew.class.php    # Descripteur du module
├── js/
│   └── peppolnew.js            # Interface utilisateur
├── langs/
│   └── fr_FR/
│       └── peppolnew.lang      # Traductions françaises
├── lib/
│   └── peppolnew.lib.php       # Fonctions utilitaires
├── sql/
│   └── llx_peppolnew_log.sql   # Table de logs
└── peppol_send.php             # Script d'envoi AJAX
```

---

## ❓ FAQ

### Le module n'apparaît pas dans la liste

- Vérifiez que le dossier est bien dans `/htdocs/custom/peppolnew/`
- Vérifiez les permissions (755 pour dossiers, 644 pour fichiers)
- Videz le cache : Configuration → Outils → Purge cache

### Erreur "Sender Peppol ID not configured"

Votre ID Peppol n'est pas configuré. Vérifiez :
1. Configuration du module (⚙️)
2. Fiche de votre société (ID Prof 6)

### Erreur BR-61 : IBAN manquant

Configurez vos coordonnées bancaires dans :
Configuration → Société/Organisation → Informations bancaires

### Les boutons n'apparaissent pas

- La facture doit être **validée** (statut Validée ou Payée)
- Videz le cache navigateur (Ctrl+Shift+R)
- Vérifiez la console JavaScript (F12) pour erreurs

### Comment tester sans envoyer de vraies factures ?

Peppyrus propose un environnement de test :
- **API Test** : `https://api.test.peppyrus.be/v1`
- **Frontend Test** : [customer.test.peppyrus.be](https://customer.test.peppyrus.be)

---

## 🤝 Contribution

Les contributions sont les bienvenues !

### Comment contribuer

1. **Fork** le projet
2. Créez une branche : `git checkout -b feature/amelioration`
3. Committez : `git commit -am 'Ajout nouvelle fonctionnalité'`
4. Poussez : `git push origin feature/amelioration`
5. Créez une **Pull Request**

### Signaler un bug

Utilisez les **Issues GitHub** avec :
- Description détaillée du problème
- Version de Dolibarr
- Version du module
- Messages d'erreur (logs)
- Étapes pour reproduire

---

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

---

## 💝 Crédits

### Développement

- **Développé avec l'aide de** : Claude (Anthropic AI)
- **Contributeur principal** : Pierre 

### Technologies utilisées

- [Dolibarr ERP CRM](https://www.dolibarr.org) - Plateforme ERP/CRM
- [Peppyrus](https://peppyrus.be) - Point d'accès Peppol gratuit
- [Peppol](https://peppol.org) - Réseau de facturation électronique
- [UBL 2.1](https://docs.peppol.eu/poacc/billing/3.0/) - Format de document standardisé

### Remerciements

- Communauté Dolibarr pour l'écosystème de modules
- Tigron pour Peppyrus et leur API documentée
- Tous les contributeurs du projet

---

## 🔗 Liens utiles

- 📖 [Documentation Dolibarr](https://wiki.dolibarr.org)
- 📖 [Documentation Peppyrus](https://docs.peppyrus.be)
- 📖 [Spécifications PEPPOL BIS Billing 3.0](https://docs.peppol.eu/poacc/billing/3.0/)
- 📖 [Format UBL 2.1](https://docs.oasis-open.org/ubl/UBL-2.1.html)
- 🔍 [Annuaire Peppol](https://directory.peppol.eu)

---

## 📞 Support

- 🐛 **Bugs** : [Issues GitHub](https://github.com/marcrant1/dolibarr-peppol-export/issues)
- 💬 **Questions** : [Forum Dolibarr](https://forum.dolibarr.org)
- 📧 **Contact** : maxomatic34@gmail.com
---

⭐ **Si ce module vous est utile, n'hésitez pas à mettre une étoile sur GitHub !**
EOFREADME

cat README.md
