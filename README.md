## Messagerie Sécurisée avec Chiffrement de Données

#### Pour garantir des échanges confidentiels et protéger la vie privée des utilisateurs, notre système de messagerie interne intègre un chiffrement de bout en bout (end-to-end encryption). Ce mécanisme assure que seuls l’expéditeur et le destinataire peuvent lire les messages, empêchant ainsi tout accès non autorisé, même par les administrateurs du système.

## Chiffrement et Déchiffrement des Données en Base

1. Fonctionnalités principales :

  -  Interception des événements de Doctrine
      - Utilisation d’un Event Subscriber pour écouter et réagir automatiquement aux événements liés aux entités Doctrine.
      - Garantit un traitement en temps réel des modifications effectuées sur les données sensibles.

  -  Automatisation du chiffrement et du déchiffrement
      - Chiffrement des données avant leur persistance en base (événement prePersist).
      - Déchiffrement des données lors de leur récupération pour garantir leur lisibilité uniquement aux utilisateurs autorisés.
      - Application des opérations lors des événements Doctrine : Création d'une entité (persist), Mise à jour d'une entité (update), Suppression d'une entité (delete)

  -  Architecture et gestion des événements
      - L'Event Subscriber dédié au chiffrement des données est placé dans le répertoire suivant : /src/EventListener/DatabaseActivitySubscriber.php

2. Bénéfices de cette approche :

  -  Protection des informations sensibles contre les fuites de données.
  -  Respect des exigences de conformité et des réglementations sur la protection des données (RGPD, ISO 27001).
  - Transparence pour l’utilisateur final avec un système sécurisé et automatique.

![Alt text](<Screenshot.png>)

![Alt text](<Enregistrement.mov>)

## Environnement technique

1. Backend
  - PHP 8, SQL, Doctrine ORM, Symfony 6.4 et son écosystème, Elasticsearch

2. Frontend
  - Webpack encore, HTML5, CSS3, Bootstrap, Twig, JavaScript, Bibliothèque JavaScript jQuery, Icons Font Awesome

3. Technologie de conteneurisation
  - Docker

5. Dépôt local
  - Git

6. Database
  - MySql

## Installation

1 : Clonage du dépôt.

2 : Lancer la stack docker-compose
  - docker-compose up -d

4 : Entrer dans le shell du conteneur "symfony_app"
  - docker exec -it symfony_app bash

5 : Installer les dépendances.
  - composer install
  - npm install
  - npm run build

6 : Créer la base de données.
  - php bin/console doctrine:database:create

7 : Lancer les migrations.
  - php bin/console make:migration
  - php bin/console doctrine:migrations:migrate

8 : Create Secrets (Définissez des secrets sensibles)
  - php bin/console secrets:set IV (exemple : 1234567891011121)
  - php bin/console secrets:set KEY_SECRETS (exemple : secret key)
  - documentation technique : https://symfony.com/doc/current/configuration/secrets.html

9 : Lancé les fixtures.
  - php bin/console doctrine:fixtures:load

10 : indexer les données dans Elasticsearch.
  - php bin/console fos:elastica:populate

## URL

1 : Application
   - https://localhost/fr/login

2 : phpMyAdmin
  - http://localhost:8081/
  - Authentification ("username": "root", "password": "root")

1 : MailDev
   - http://localhost:8025/