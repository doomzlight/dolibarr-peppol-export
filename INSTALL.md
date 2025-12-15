# Guide d'installation

## Installation pas à pas

1. **Téléchargez le module**
2. **Placez-le dans** `/htdocs/custom/peppolnew/`
3. **Donnez les permissions** :
```bash
   chown -R www-data:www-data peppolnew
   chmod -R 755 peppolnew
```
4. **Activez dans Dolibarr** : Configuration > Modules
5. **Configurez** : Cliquez sur ⚙️

## Configuration API Peppyrus

1. Créez un compte sur https://peppyrus.be
2. Obtenez votre clé API
3. Notez votre ID Peppol
4. Entrez ces informations dans la configuration du module
