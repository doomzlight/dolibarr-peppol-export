GUIDE_UTILISATEUR.md 
# 📘 Guide Utilisateur - Module Peppol Export

Guide pratique pour utiliser le module Peppol Export dans Dolibarr.

---

## 🎯 Qu'est-ce que Peppol ?

**Peppol** est un réseau européen de facturation électronique qui permet d'envoyer et recevoir des factures entre entreprises de manière standardisée et sécurisée.

**Avantages :**
- ✅ Envoi instantané de factures
- ✅ Réduction des coûts d'impression et d'envoi postal
- ✅ Traçabilité complète des envois
- ✅ Conformité aux normes européennes
- ✅ Gratuit via Peppyrus

---

## 📋 Prérequis

Avant de commencer, vous devez :

1. **Avoir un compte Peppyrus** (gratuit)
   - Créez un compte sur [peppyrus.be](https://peppyrus.be)
   - Obtenez votre clé API
   - Notez votre ID Peppol

2. **Configurer votre société dans Dolibarr**
   - IBAN et BIC renseignés
   - TVA intracommunautaire renseignée
   - Adresse complète

3. **Vos clients doivent être sur Peppol**
   - Demandez leur ID Peppol
   - Format : `9925:beXXXXXXXXXX` (Belgique)

---

## ⚙️ Configuration initiale

### 1. Activer le module

1. Dans Dolibarr : **Configuration → Modules/Applications**
2. Recherchez **"PeppolNew"**
3. Cliquez sur **Activer**

### 2. Configurer l'API Peppyrus

1. Cliquez sur **⚙️** à côté du module
2. Remplissez :
   - **URL API** : `https://api.peppyrus.be/v1`
   - **Clé API** : Votre clé depuis peppyrus.be
   - **Votre ID Peppol** : Format `9925:beXXXXXXXXXX`
3. Cliquez sur **Enregistrer**

### 3. Activer le champ ID Peppol pour les clients

1. **Configuration → Modules → Tiers**
2. Section **"Identifiants professionnels"**
3. Cochez **"Activer ID Professionnel 6"**
4. Libellé : `ID Peppol`
5. **Enregistrer**

### 4. Configurer votre société

1. **Configuration → Société/Organisation**
2. Onglet **"Carte"**
3. Remplissez **"ID Peppol"** : `9925:beXXXXXXXXXX`
4. Section **Informations bancaires** :
   - **IBAN** : Votre IBAN complet
   - **BIC** : Votre code BIC/SWIFT
5. **Enregistrer**

---

## 👥 Configurer vos clients

Pour chaque client qui accepte les factures Peppol :

1. **Ouvrez la fiche du client**
2. **Onglet "Carte"**
3. Cherchez le champ **"ID Peppol"**
4. Entrez l'ID au format : `9925:beXXXXXXXXXX`
5. **Enregistrer**

### Comment trouver l'ID Peppol d'un client ?

**Option 1** : Demandez-lui directement

**Option 2** : Recherchez dans l'annuaire Peppol
- Allez sur [directory.peppol.eu](https://directory.peppol.eu)
- Recherchez par nom d'entreprise ou numéro BCE

---

## 📤 Envoyer une facture via Peppol

### Étape 1 : Créer une facture normale

1. Créez votre facture comme d'habitude dans Dolibarr
2. **Validez** la facture

### Étape 2 : Vérifier le client

Sur la facture validée, vous verrez **3 nouveaux boutons** en bas de page :

**🔍 Rechercher dans Peppol**
- Vérifie que votre client existe bien dans le réseau Peppol
- Confirme que son ID Peppol est correct
- **Cliquez d'abord sur ce bouton pour vérifier !**

### Étape 3 : Générer le fichier UBL (optionnel)

**📄 Générer UBL**
- Télécharge le fichier XML au format UBL 2.1
- Permet de vérifier le contenu avant envoi
- Utile pour archivage ou envoi manuel

### Étape 4 : Envoyer vers Peppol

**📤 Envoyer vers Peppol**
- Génère automatiquement le fichier UBL
- L'envoie au réseau Peppol via Peppyrus
- Affiche la confirmation d'envoi

**Message de succès :**
```
✅ Facture envoyée avec succès vers Peppol !

ID Transaction: 12345-abcd-6789
Vérifiez votre tableau de bord Peppyrus pour le suivi.
```

---

## 📊 Suivre vos envois

### Tableau de bord Peppyrus

1. Connectez-vous sur [customer.peppyrus.be](https://customer.peppyrus.be)
2. Vous verrez :
   - Liste des factures envoyées
   - Statut de transmission
   - Accusés de réception
   - Erreurs éventuelles

### Statuts possibles

| Statut | Signification |
|--------|---------------|
| ✅ **Delivered** | Facture reçue par le client |
| ⏳ **Processing** | En cours de transmission |
| ⚠️ **Error** | Erreur d'envoi (voir détails) |
| 📨 **Sent** | Envoyée au réseau Peppol |

---

## ❓ Questions fréquentes

### Mon client n'a pas d'ID Peppol, que faire ?

Deux options :
1. **Il s'inscrit sur Peppol** (gratuit via peppyrus.be, codabox, etc.)
2. **Vous envoyez la facture normalement** (PDF par email)

Le module n'empêche pas l'envoi classique de factures !

### Puis-je envoyer une facture déjà envoyée ?

Oui ! Cliquez à nouveau sur **📤 Envoyer vers Peppol**. 

⚠️ Attention : Le client recevra la facture deux fois !

### Comment annuler une facture envoyée ?

Vous devez créer un **avoir** (facture de crédit) et l'envoyer via Peppol également.

### Combien coûte l'envoi via Peppol ?

**Totalement gratuit** grâce à Peppyrus ! 🎉

Peppyrus est un point d'accès Peppol gratuit financé par la Région Flamande.

### La facture est rejetée, pourquoi ?

Les causes possibles :
- ❌ ID Peppol du client incorrect
- ❌ IBAN/BIC manquant dans votre fiche société
- ❌ Format de facture invalide
- ❌ Client n'accepte pas les factures automatiquement

**Solution :** Vérifiez les logs sur customer.peppyrus.be

### Puis-je tester sans envoyer de vraies factures ?

Oui ! Peppyrus propose un **environnement de test** :

1. Changez l'URL API pour : `https://api.test.peppyrus.be/v1`
2. Créez un compte test sur [customer.test.peppyrus.be](https://customer.test.peppyrus.be)
3. Envoyez vos factures de test

---

## 🛠️ Dépannage

### Les boutons n'apparaissent pas

**Vérifiez :**
- ✅ Le module est bien activé
- ✅ La facture est **validée** (pas en brouillon)
- ✅ Videz le cache : Configuration → Outils → Purge cache
- ✅ Rafraîchissez la page (Ctrl+F5)

### Erreur "Sender Peppol ID not configured"

**Solution :**
1. Configuration → Modules → PeppolNew → ⚙️
2. Vérifiez que **"Votre ID Peppol"** est rempli
3. Configuration → Société → Vérifiez **"ID Peppol"** (ID Prof 6)

### Erreur "Participant not found"

**Solution :**
- Vérifiez l'ID Peppol du client dans sa fiche
- Testez avec 🔍 Rechercher dans Peppol
- Vérifiez sur [directory.peppol.eu](https://directory.peppol.eu)

### Erreur BR-61 : IBAN manquant

**Solution :**
1. Configuration → Société/Organisation
2. Section **Informations bancaires**
3. Renseignez IBAN et BIC

---

## 📞 Support

### Documentation technique
- 📖 [README.md](README.md) - Vue d'ensemble
- 📖 [INSTALL.md](INSTALL.md) - Installation
- 📖 [CONFIGURATION.md](CONFIGURATION.md) - Configuration détaillée

### Aide communautaire
- 💬 [Forum Dolibarr](https://forum.dolibarr.org)
- 🐛 [Issues GitHub](https://github.com/marcrant1/dolibarr-peppol-export/issues)

### Aide Peppyrus
- 📧 Support Peppyrus : support@peppyrus.be
- 📖 Documentation : [docs.peppyrus.be](https://docs.peppyrus.be)

---

## ✅ Checklist avant le premier envoi

- [ ] Compte Peppyrus créé et clé API obtenue
- [ ] Module activé et configuré
- [ ] Votre ID Peppol configuré (module + société)
- [ ] IBAN et BIC renseignés
- [ ] Champ "ID Peppol" activé pour les tiers
- [ ] ID Peppol du client renseigné
- [ ] Test avec 🔍 Rechercher dans Peppol : OK
- [ ] Première facture créée et validée
- [ ] Prêt à envoyer ! 🚀

---

**Bonne facturation électronique !** 📨✨
EOFGUIDE

cat GUIDE_UTILISATEUR.md
