# 🚀 Démarrage rapide - Dolibarr Peppol Export

Guide pour installer et configurer le module en 10 minutes.

## ⚡ Installation rapide (3 minutes)

### Option 1 : Installateur automatique ⭐ Recommandé

```bash
# 1. Téléchargez install_peppolexport.php
wget https://raw.githubusercontent.com/votre-username/dolibarr-peppol-export/main/install_peppolexport.php

# 2. Placez-le dans /custom/
mv install_peppolexport.php /var/www/html/dolibarr/htdocs/custom/

# 3. Accédez via navigateur
https://votre-dolibarr.com/custom/install_peppolexport.php

# 4. Suivez les instructions
# 5. Supprimez le fichier après installation
rm /var/www/html/dolibarr/htdocs/custom/install_peppolexport.php
```

### Option 2 : Installation manuelle

```bash
cd /var/www/html/dolibarr/htdocs/custom/
git clone https://github.com/votre-username/dolibarr-peppol-export.git peppolexport
chmod -R 755 peppolexport/
chown -R www-data:www-data peppolexport/
```

## ⚙️ Configuration rapide (5 minutes)

### 1. Activez le module
1. Dolibarr → Configuration → Modules
2. Recherchez "Peppol Export"
3. Cliquez sur "Activer"

### 2. Configurez Peppyrus
1. Créez un compte sur [peppyrus.be](https://peppyrus.be) (gratuit)
2. Obtenez votre clé API
3. Notez votre ID Peppol

### 3. Configurez le module
1. Configuration → Modules → Peppol Export → ⚙️
2. Remplissez :
   - **URL API** : `https://api.test.peppyrus.be/v1` (pour tester)
   - **Clé API** : Votre clé depuis Peppyrus
   - **Votre ID Peppol** : Format `9925:be0123456789`
3. Sauvegardez

### 4. Configurez votre entreprise
1. Configuration → Société/Organisation
2. Ajoutez :
   - **IBAN** : Votre compte bancaire (sans espaces)
   - **BIC** : Votre code BIC
3. Sauvegardez

## 🧪 Premier test (2 minutes)

### 1. Créez une facture de test
1. Créez un nouveau client avec un ID Peppol
2. Créez une facture pour ce client
3. Validez la facture

### 2. Testez la génération UBL
1. Ouvrez la facture
2. Cliquez sur **📄 Générer UBL**
3. Un fichier XML doit se télécharger

### 3. Testez la recherche Peppol
1. Sur la même facture
2. Cliquez sur **🔍 Rechercher dans Peppol**
3. Vérifiez que le client est trouvé

### 4. Testez l'envoi (en mode test)
1. Cliquez sur **📤 Envoyer vers Peppol**
2. Confirmez l'envoi
3. Vérifiez sur [customer.test.peppyrus.be](https://customer.test.peppyrus.be)

## ✅ Passage en production

Une fois les tests OK :

1. **Changez l'URL API** :
   - Configuration → Modules → Peppol Export → ⚙️
   - URL API → `https://api.peppyrus.be/v1`
   - Clé API → Votre vraie clé (pas de test)
   - Sauvegardez

2. **Supprimez les outils de test** :
   ```bash
   rm -rf /var/www/html/dolibarr/htdocs/custom/peppolexport/tools/
   ```

3. **Envoyez votre première vraie facture** !

## 📋 Checklist de démarrage

- [ ] Module installé et activé
- [ ] Compte Peppyrus créé
- [ ] Configuration du module complète
- [ ] IBAN configuré
- [ ] Client de test avec ID Peppol
- [ ] Facture de test créée
- [ ] Génération UBL testée
- [ ] Recherche Peppol testée
- [ ] Envoi test réussi
- [ ] URL API changée en production
- [ ] Outils de test supprimés
- [ ] Premier envoi réel validé

## 🆘 Problèmes courants

### "Module n'apparaît pas"
```bash
sudo chmod -R 755 /var/www/html/dolibarr/htdocs/custom/peppolexport
# Puis videz le cache Dolibarr
```

### "API Key Missing"
Allez dans Configuration → Modules → Peppol Export → ⚙️ et ajoutez votre clé API.

### "IBAN manquant" (erreur BR-61)
Configuration → Société/Organisation → Ajoutez votre IBAN.

### "Participant not found"
Le client n'est pas encore sur Peppol. Vérifiez son ID ou demandez-lui de s'inscrire.

## 📚 Pour aller plus loin

- [Documentation complète](README.md)
- [Guide d'installation détaillé](docs/installation.md)
- [Guide de configuration](docs/configuration.md)
- [Guide de test complet](docs/testing.md)

## 🌟 Formats d'ID Peppol par pays

| Pays | Exemple |
|------|---------|
| 🇧🇪 Belgique | `9925:be0838264694` |
| 🇳🇱 Pays-Bas | `9925:nl123456789B01` |
| 🇫🇷 France | `9957:fr12345678901` |
| 🇩🇪 Allemagne | `9930:de123456789` |

[Liste complète](https://docs.peppol.eu/poacc/billing/3.0/codelist/eas/)

## 💬 Support

- 🐛 [Issues GitHub](https://github.com/votre-username/dolibarr-peppol-export/issues)
- 💬 [Discussions](https://github.com/votre-username/dolibarr-peppol-export/discussions)
- 📖 [Documentation](https://github.com/votre-username/dolibarr-peppol-export/tree/main/docs)

---

**Vous êtes prêt ! 🎉**

*Temps total : ~10 minutes*
