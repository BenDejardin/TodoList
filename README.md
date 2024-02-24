## Installation du Projet

Pour installer et exécuter ce projet sur votre machine locale, suivez les étapes ci-dessous :

### Prérequis
Assurez-vous d'avoir les éléments suivants installés sur votre système :
- PHP
- Composer
- Symfony CLI

### Instructions d'Installation

1. Clonez le dépôt GitHub :

```bash
git clone https://github.com/BenDejardin/TodoList.git
```

2. Accédez au répertoire du projet :

```bash
cd TodoList
```

3. Installez les dépendances via Composer :

```bash
composer install
```

4. Configurez votre base de données dans le fichier `.env` :

```bash
# Exemple de configuration pour MySQL
DATABASE_URL=mysql://user:password@localhost:3306/database_name
```

5. Créez la base de données et exécutez les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

6. Lancez le serveur Symfony :

```bash
symfony serve
```

Vous pouvez maintenant accéder à l'application dans votre navigateur à l'adresse `http://localhost:8000`.

---

## Documentation Technique - Authentification dans Symfony

### Introduction

Cette documentation vise à expliquer l'implémentation de l'authentification dans le projet Symfony. Elle est conçue pour aider les développeurs juniors à comprendre comment fonctionne l'authentification, quels fichiers doivent être modifiés et où sont stockées les informations sur les utilisateurs.

### Fichiers à Modifier

1. **security.yaml** : Ce fichier se trouve dans le répertoire `config/packages` et contient la configuration de sécurité de l'application. Vous devrez modifier ce fichier pour configurer les fournisseurs d'authentification, les fournisseurs de gestion d'utilisateurs, les pare-feu et les stratégies d'authentification.

2. **LoginController.php** : Si vous devez personnaliser le processus de connexion, vous trouverez le contrôleur de connexion dans le répertoire `src/Controller`.

### Processus d'Authentification

L'authentification dans Symfony s'opère de la manière suivante :

1. L'utilisateur soumet ses identifiants de connexion via un formulaire.
2. Les identifiants sont envoyés au contrôleur de sécurité de Symfony.
3. Symfony utilise les informations de configuration fournies dans `security.yaml` pour vérifier les identifiants de l'utilisateur.
4. Si les identifiants sont valides, l'utilisateur est authentifié et une session est créée.

### Stockage des Utilisateurs

Les informations sur les utilisateurs sont stockées en base de données. Vous pouvez accéder à ces informations via les entités User définies dans le répertoire src/Entity.

### Récupération des Informations de Connexion

#### Depuis un Controller
```php
use Symfony\Component\Security\Core\Security;

class MonController extends AbstractController
{
    public function maFonction(Security $security)
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        
        // Accéder aux propriétés de l'utilisateur connecté
        $username = $user->getUsername();
        $email = $user->getEmail();
        // etc.
        
        // Récupérer le rôle de l'utilisateur
        $role = $user->getRoles();
    }
}
```

#### Depuis la vue
```twig
{% if app.user %}
    <p>Bienvenue, {{ app.user.username }}</p>
    <p>Votre rôle est : {{ app.user.roles }}</p>
{% endif %}
```

### Tests Unitaires et Fonctionnels

Avez-vous terminé la mise en place de votre nouvelle fonctionnalité ? Il est alors temps de procéder à la réalisation des tests. Ces tests doivent être effectués dans le dossier `/tests`.

Il est impératif de respecter les conventions de nommage pour vos fichiers de test.

Une fois la création de vos tests effectuée, vous devez maintenant vérifier que l'ensemble d'entre eux fonctionne correctement.

Pour ce faire, nous utilisons PHPUnit avec Xdebug.

Pour tester vos fonctionnalités, veuillez exécuter la commande

```bash
vendor/bin/phpunit --coverage-html tests/xDebug
```

Les tests doivent couvrir plus de 70% du code.

### Collaboration sur le Projet

Le projet est hébergé sur GitHub à l'adresse suivante : [https://github.com/BenDejardin/TodoList](https://github.com/BenDejardin/TodoList).

Pour comprendre le fonctionnement des branches, chaque branche correspond à une version Symfony spécifique du site. Assurez-vous donc de vous positionner sur la dernière version disponible.

Une fois que votre push sera accepté, Codacy procédera à l'analyse de votre code et générera un rapport. Veillez à corriger les anomalies détectées par Codacy.
