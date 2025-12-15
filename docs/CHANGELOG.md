# Changelog

Toutes les modifications notables de ce projet seront documentées dans ce fichier.

Le format est basé sur [Keep a Changelog](https://keepachangelog.com/fr/1.0.0/),
et ce projet adhère au [Semantic Versioning](https://semver.org/lang/fr/).

## [1.0.0] - 2024-11-26

### Ajouté
- ✨ Génération de fichiers UBL 2.1 conformes PEPPOL BIS Billing 3.0
- ✨ Support des factures et avoirs
- ✨ Intégration complète avec l'API Peppyrus
- ✨ Recherche de participants dans l'annuaire Peppol
- ✨ Boutons d'action dans les fiches factures Dolibarr
- ✨ Validation automatique des documents UBL
- ✨ Logs d'envoi en base de données
- ✨ Page d'administration avec configuration
- ✨ Outil de diagnostic intégré
- ✨ Support multilingue (FR, EN)
- ✨ Hooks Dolibarr pour intégration native
- ✨ Documentation complète
- ✨ Outils de test (test_config.php, test_ubl.php, test_send.php)
- ✨ Installateur automatique

### Sécurité
- 🔒 Validation des IDs Peppol
- 🔒 Échappement des données SQL
- 🔒 Contrôle d'accès basé sur les droits Dolibarr
- 🔒 Validation des réponses API

### Documentation
- 📚 README.md complet avec guide d'installation
- 📚 Guides dans /docs/ (installation, configuration, test)
- 📚 Commentaires de code détaillés
- 📚 FAQ avec solutions aux problèmes courants

## [À venir] - Version 2.0.0

### Prévu
- 🚀 Support des commandes (Orders)
- 🚀 Support des bons de livraison (Despatch Advice)
- 🚀 Interface de suivi des envois dans Dolibarr
- 🚀 Notifications par email des statuts d'envoi
- 🚀 Export en masse de factures
- 🚀 Historique détaillé par facture
- 🚀 Support d'autres points d'accès Peppol
- 🚀 Mode debug avancé
- 🚀 Tests unitaires automatisés

### Améliorations envisagées
- ⚡ Cache des recherches de participants
- ⚡ Optimisation des requêtes SQL
- ⚡ Support asynchrone des envois
- 🌍 Traductions supplémentaires (NL, DE, ES)
- 📊 Statistiques d'utilisation
- 🎨 Interface utilisateur améliorée

---

## Format des versions

Le format de version suit [Semantic Versioning](https://semver.org/) :

- **MAJOR** : changements incompatibles avec les versions précédentes
- **MINOR** : nouvelles fonctionnalités rétrocompatibles
- **PATCH** : corrections de bugs rétrocompatibles

---

## Contributeurs

Un grand merci à tous les contributeurs !

- Développé avec l'assistance de Claude (Anthropic AI)
- Testé par la communauté Dolibarr
- API fournie par Peppyrus (Tigron B.V.)

---

*Pour signaler un bug ou suggérer une amélioration, utilisez les [Issues GitHub](https://github.com/votre-username/dolibarr-peppol-export/issues)*
