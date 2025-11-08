## Messagerie Sécurisée avec Chiffrement de Données

#### Pour garantir la confidentialité des échanges entre utilisateurs, le système de messagerie implémente un chiffrement de bout en bout (E2EE).Seuls l’expéditeur et le destinataire peuvent lire les messages.

## Fonctionnalités principales :

  -  Interception des événements Doctrine
      - EventSubscriber dédié pour écouter le cycle de vie des entités sensibles.
      - Chiffrement avant l’écriture (prePersist, preUpdate) et déchiffrement lors de la lecture (postLoad)
      - Support complet des opérations Doctrine : création, mise à jour et suppression.

  -  Gestion des clés et secrets
      - Les clés et vecteurs d’initialisation sont stockés via le système de secrets Symfony

## Architecture globale et flux de données

  - Séparation claire des responsabilités
     - EventListener/DatabaseActivitySubscriber.php : Écoute les événements Doctrine (prePersist, postPersist, postLoad, preUpdate) et délègue le traitement à ActivityRouter.
    - EventListener/Activity/Router/ActivityRouter.php : Parcourt la liste des handlers et appelle celui qui prend en charge l’entité (par exemple, User).
    - EventListener/Activity/Handler/UserActivityHandler.php : Gère le cycle de vie de l’entité User et délègue les appels à ActivityUser.
    - EventListener/Activity/ActivityUser.php : Contient la logique métier d’encryptage et de décryptage des données.
  -  Extensibilité
      - L’ajout d’une nouvelle entité se limite à la création d’un handler dédié et l’ajout de sa logique métier associée.
      - Aucune modification n’est nécessaire dans ActivityRouter ou DatabaseActivitySubscriber
  -  Respect des principes SOLID
      - SRP : Chaque classe a une seule responsabilité.
      - OCP : Ouvert à l'extension, fermé à la modification.
      - DIP : Dépendances injectées via des interfaces.

## Bénéfices de cette approche :

  -  Protection des informations sensibles contre les fuites de données.
  -  Respect des exigences de conformité et des réglementations sur la protection des données (RGPD, ISO 27001).
  - Transparence pour l’utilisateur final avec un système sécurisé et automatique.

## Démo de l'application : 

![Aperçu de la vidéo](Screens.gif)

## Environnement technique

- Backend
    - PHP 8, SQL, Doctrine ORM, Symfony 6.4 et son écosystème, Elasticsearch

- Frontend
    - Webpack encore, HTML5, CSS3, Bootstrap, Twig, JavaScript, Bibliothèque JavaScript jQuery, Icons Font Awesome

- Technologie de conteneurisation
    - Docker

- Dépôt local
    - Git

- Database
    - MySql

## Installation

- Clonage du dépôt.

- Lancer la stack docker-compose
    - docker-compose up -d

- Entrer dans le shell du conteneur "symfony_app"
    - docker exec -it symfony_app bash

- Installer les dépendances.
    - composer install
    - npm install
    - npm run build

- Créer la base de données.
    - php bin/console doctrine:database:create

- Lancer les migrations.
    - php bin/console make:migration
    - php bin/console doctrine:migrations:migrate

- Create Secrets (Définissez des secrets sensibles)
    - php bin/console secrets:set IV (exemple : 1234567891011121)
    - php bin/console secrets:set KEY_SECRETS (exemple : secret key)
    - documentation technique : https://symfony.com/doc/current/configuration/secrets.html

- Lancé les fixtures.
    - php bin/console doctrine:fixtures:load

- indexer les données dans Elasticsearch.
    - php bin/console fos:elastica:populate

## URL

- Application
    - https://localhost/fr/login

- phpMyAdmin
    - http://localhost:8081/
    - Authentification ("username": "root", "password": "root")

- MailDev
    - http://localhost:8025/