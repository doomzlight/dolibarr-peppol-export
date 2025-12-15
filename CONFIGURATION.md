# Configuration détaillée

## 1. Activer le champ ID Peppol

Le module utilise le champ **"ID Professionnel 6"** pour stocker l'ID Peppol des clients.

### Activer ce champ :

1. **Configuration → Modules → Tiers**
2. Cherchez **"Identifiants professionnels"**
3. Cochez **"Activer ID Professionnel 6"**
4. Dans **"Libellé de l'identifiant 6"**, tapez : `ID Peppol`

### Remplir l'ID Peppol d'un client :

1. **Ouvrez la fiche du tiers**
2. **Onglet "Carte"**
3. Cherchez le champ **"ID Peppol"** (ou "ID Prof 6")
4. Entrez l'ID au format : `9925:be0123456789`

## 2. Formats d'ID Peppol par pays

| Pays | Préfixe | Format | Exemple |
|------|---------|--------|---------|
| 🇧🇪 Belgique | 9925 | 9925:beXXXXXXXXXX | 9925:be0838264694 |
| 🇫🇷 France | 9957 | 9957:frXXXXXXXXXXX | 9957:fr12345678901 |
| 🇳🇱 Pays-Bas | 9925 | 9925:nlXXXXXXXXXX | 9925:nl123456789B01 |
| 🇩🇪 Allemagne | 9930 | 9930:deXXXXXXXXXX | 9930:de123456789 |

## 3. Configurer VOTRE ID Peppol (émetteur)

Votre propre ID Peppol doit être configuré dans **deux endroits** :

### A. Dans le module :
Configuration → Modules → PeppolNew → ⚙️
- Entrez votre ID Peppol : `9925:be0XXXXXXXXX`

### B. Dans votre société :
Configuration → Société → ID Prof 6
- Entrez le même ID Peppol : `9925:be0XXXXXXXXX`

## 4. Tester la configuration

1. Créez une facture de test
2. Le client doit avoir son ID Peppol rempli
3. Validez la facture
4. Cliquez sur **"🔍 Rechercher Peppol"**
5. Si tout est OK, vous verrez : "Participant trouvé !"

