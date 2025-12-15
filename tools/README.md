# Outils de test - Peppol Export

⚠️ **ATTENTION** : Ces outils sont destinés au développement et aux tests. **Supprimez ce dossier en production** pour des raisons de sécurité !

## 🧪 Outils disponibles

### 1. test_config.php
**Teste la configuration du module**

**URL :** `https://votre-dolibarr.com/custom/peppolexport/tools/test_config.php`

**Vérifie :**
- ✅ Configuration de l'API Peppyrus
- ✅ Votre identifiant Peppol
- ✅ Extensions PHP requises
- ✅ Permissions fichiers
- ✅ Configuration de votre entreprise
- ✅ Connexion à l'API

**Utilisation :**
```bash
# Via navigateur
https://votre-dolibarr.com/custom/peppolexport/tools/test_config.php

# Via ligne de commande
php test_config.php
```

---

### 2. test_ubl.php
**Teste la génération de fichiers UBL**

**URL :** `https://votre-dolibarr.com/custom/peppolexport/tools/test_ubl.php?id=123`

**Fonctionnalités :**
- ✅ Génère un fichier UBL pour une facture
- ✅ Affiche le XML généré
- ✅ Valide le format XML
- ✅ Permet de télécharger le fichier

**Utilisation :**
```bash
# Via navigateur
https://votre-dolibarr.com/custom/peppolexport/tools/test_ubl.php?id=123

# Via ligne de commande
php test_ubl.php id=123
```

**Paramètres :**
- `id` : ID de la facture à tester

---

### 3. test_send.php
**Teste l'envoi vers Peppol**

**URL :** `https://votre-dolibarr.com/custom/peppolexport/tools/test_send.php?id=123`

**Fonctionnalités :**
- ✅ Génère le fichier UBL
- ✅ Envoie vers l'API Peppyrus
- ✅ Affiche la réponse de l'API
- ✅ Teste sans valider l'envoi réel

**Utilisation :**
```bash
# Via navigateur
https://votre-dolibarr.com/custom/peppolexport/tools/test_send.php?id=123

# Via ligne de commande
php test_send.php id=123
```

**Paramètres :**
- `id` : ID de la facture à tester

---

## 📋 Scénarios de test recommandés

### Test 1 : Vérification de l'installation
```bash
1. Exécutez test_config.php
2. Vérifiez que tous les tests sont ✅ verts
3. Si des erreurs ❌ apparaissent, corrigez-les avant de continuer
```

### Test 2 : Génération UBL
```bash
1. Créez une facture de test dans Dolibarr
2. Validez la facture
3. Exécutez test_ubl.php?id=XXX
4. Vérifiez que le XML est bien-formé
5. Téléchargez et inspectez le fichier XML
```

### Test 3 : Envoi test
```bash
1. Configurez l'URL API de test : https://api.test.peppyrus.be/v1
2. Utilisez une clé API de test
3. Exécutez test_send.php?id=XXX
4. Vérifiez la réponse de l'API
5. Si succès ✅, passez en production
```

---

## 🔧 Résolution des problèmes

### Erreur : "Cannot load Dolibarr"
**Cause :** Le chemin vers Dolibarr est incorrect.

**Solution :**
- Vérifiez que les outils sont dans `/custom/peppolexport/tools/`
- Vérifiez que Dolibarr est bien installé

### Erreur : "Access forbidden"
**Cause :** Droits insuffisants.

**Solution :**
- Connectez-vous avec un compte administrateur
- Vérifiez les permissions utilisateur

### Erreur : "API Connection Failed"
**Cause :** Impossible de contacter l'API Peppyrus.

**Solutions :**
- Vérifiez votre connexion internet
- Vérifiez que l'URL API est correcte
- Vérifiez que votre clé API est valide
- Vérifiez les pare-feu/proxies

### XML non valide
**Cause :** Données de facture incomplètes.

**Solutions :**
- Vérifiez que la facture a toutes les informations requises
- Vérifiez que votre entreprise a un IBAN configuré
- Vérifiez que le client a un identifiant Peppol

---

## ⚠️ Sécurité

### Avant de passer en production

**SUPPRIMEZ ce dossier `/tools/` :**

```bash
# Via ligne de commande
rm -rf /var/www/html/dolibarr/htdocs/custom/peppolexport/tools/

# Ou via FTP
Supprimez le dossier /custom/peppolexport/tools/
```

**Pourquoi ?**
- Ces outils exposent des informations sensibles
- Ils permettent de tester l'API sans restrictions
- Ils peuvent être exploités par des tiers malveillants

### Protection temporaire

Si vous devez garder les outils temporairement :

**Option 1 : Protection par .htaccess**

Créez un fichier `.htaccess` dans `/tools/` :

```apache
# .htaccess
Order Deny,Allow
Deny from all
Allow from 127.0.0.1
Allow from ::1
Allow from YOUR_IP_ADDRESS
```

**Option 2 : Protection par mot de passe**

Ajoutez une vérification au début de chaque fichier :

```php
<?php
// Protection par mot de passe simple
$password = 'votre-mot-de-passe-secret';
if (!isset($_GET['pwd']) || $_GET['pwd'] !== $password) {
    die('Access denied');
}
?>
```

---

## 📚 Documentation

Pour plus d'informations :

- [Guide d'installation](../docs/installation.md)
- [Guide de configuration](../docs/configuration.md)
- [Guide de test complet](../docs/testing.md)
- [README principal](../README.md)

---

## 🆘 Support

- 🐛 **Bugs** : [GitHub Issues](https://github.com/votre-username/dolibarr-peppol-export/issues)
- 💬 **Questions** : [GitHub Discussions](https://github.com/votre-username/dolibarr-peppol-export/discussions)
- 📖 **Documentation** : [docs/](https://github.com/votre-username/dolibarr-peppol-export/tree/main/docs)
