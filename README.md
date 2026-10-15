# Évaluation Symfony – Agenda d'événements

Application de gestion d'événements (concerts, conférences, ateliers, sport) à développer avec Symfony 7.4.

Le design est **déjà fait** : toutes les pages existent en HTML/CSS statique. Votre travail consiste à les rendre dynamiques (entités, formulaires, sécurité…), **sans vous occuper du CSS**.

---

## ⚠️ Avant de commencer : forkez le dépôt

> **Ne commitez et ne poussez JAMAIS sur ce dépôt.** Vous travaillez uniquement sur **votre fork**.

1. Rendez-vous sur <https://github.com/louvelmathieu/eval_b2_symfony> et cliquez sur le bouton **Fork** en haut à droite.

   > Votre fork doit être **public** : c'est lui qui sera corrigé. Utilisez bien le bouton **Fork** (le fork d'un dépôt public est public par défaut) et ne faites pas de copie privée du projet.

2. Clonez **votre fork** (et non le dépôt d'origine) :

   ```bash
   git clone https://github.com/<votre-compte>/eval_b2_symfony.git
   cd eval_b2_symfony
   ```

3. Vérifiez que `origin` pointe bien vers **votre** compte :

   ```bash
   git remote -v
   ```

4. Commitez régulièrement sur votre fork, avec des messages clairs.

---

## 📦 Rendu

Le rendu se fait sur **votre fork**. Avant la date limite, complétez ce bloc (remplacez les `...`) et commitez-le :

```text
Nom       : TERAOU 
Prénom      M.Christian

Compte admin (ROLE_ADMIN)
  Email        : ...
  Mot de passe : ...

Compte utilisateur
  Email        : christchristian760@gmail.com
  Mot de passe : neuZtu5Z3KUTQAH
```

Commitez aussi la **base SQLite** avec quelques événements, utilisateurs et inscriptions de test. Le dossier `var/` étant ignoré par Git, il faut forcer l'ajout :

```bash
git add -f var/data_dev.db
git commit -m "Ajout de la base de données"
git push
```

> Vérifiez sur GitHub que le fichier `var/data_dev.db` apparaît bien dans votre fork.

---

## Installation

### Prérequis

- PHP **8.3** ou plus, avec l'extension `pdo_sqlite` (`php -m | grep sqlite` pour vérifier)
- [Composer](https://getcomposer.org/)
- [Symfony CLI](https://symfony.com/download) (recommandé)

### Étapes

```bash
# 1. Installer les dépendances (installe aussi les assets JS)
composer install

# 2. Vérifier que l'environnement est correct
symfony check:requirements

# 3. Lancer le serveur
symfony server:start
```

Le site est accessible sur <https://127.0.0.1:8000>.

Sans Symfony CLI : `php -S 127.0.0.1:8000 -t public`

### Base de données

Le projet est configuré avec **SQLite** (dans `.env`) : aucun serveur à installer, la base est un simple fichier `var/data_dev.db` créé automatiquement.

```dotenv
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data_%kernel.environment%.db"
```

Une fois vos entités créées :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

> `doctrine:database:create` n'est pas nécessaire (et ne fonctionne pas) avec SQLite.

---

## Ce qui est fourni

| URL | Route | Template |
|---|---|---|
| `/` | `app_home` | `home/index.html.twig` |
| `/evenement/ajouter` | `app_event_new` | `event/new.html.twig` |
| `/evenement/{id}` | `app_event_show` | `event/show.html.twig` |
| `/validate` | `app_validate` | `validate/index.html.twig` |
| `/login` | `app_login` | `security/login.html.twig` |
| `/register` | `app_register` | `registration/register.html.twig` |
| (page 404) | – | `bundles/TwigBundle/Exception/error404.html.twig` |

- Les **contrôleurs** ne font qu'afficher les templates : à vous de les compléter.
- Les **formulaires** sont en HTML brut et ne sont pas traités : remplacez-les par des formulaires Symfony. Le CSS cible les balises (`label`, `input`, `select`…), donc `{{ form(form) }}` s'affiche correctement sans rien changer.
- Les **données** (titres, dates, images…) sont écrites en dur : remplacez-les par vos variables Twig.
- Certaines variantes (badge « Complet », utilisateur non connecté, messages flash, liste vide…) sont **en commentaire** dans les templates : décommentez-les quand vous en avez besoin.
- Les images d'exemple sont dans `public/images/`, vous pouvez les utiliser pour tester l'upload.

### Classes CSS utiles

- Couleur par catégorie : ajoutez `cat-concert`, `cat-conference`, `cat-atelier` ou `cat-sport` sur la carte (`event-card`) ou sur un `badge`.
- Messages flash : `flash flash-success` / `flash flash-error`.
- Boutons : `btn`, `btn-small`, `btn-success`, `btn-block`.

---

## Sujet

1. La page d'accueil affiche la date et le titre des événements **à venir** et **validés** (pas les passés) et un lien pour en ajouter un.

2. Sur la page pour ajouter un événement, un formulaire s'affiche avec les champs :
   - **Titre**
   - **Date**
   - **Informations** : description de l'événement (lieu, programme…)
   - **Catégorie** : liste déroulante avec « Concert » / « Conférence » / « Atelier » / « Sport »
   - **Nombre de places**
   - **Image à la une** : upload d'une image, affichée sur la page d'accueil et sur la page de détail (doc : <https://symfony.com/doc/7.4/controller/upload_file.html>)

3. Le titre, la date et les informations sont **obligatoires**, le nombre de places doit être **supérieur à 0**. À l'ajout, l'événement a le statut « **Non validé** ».

4. Sur la page d'accueil, rajouter une **couleur différente** sur chaque événement suivant sa catégorie.

5. Sur la page d'accueil, les catégories sont **cliquables** : un clic sur une catégorie n'affiche que les événements de cette catégorie (ex. : `/?categorie=concert`), « Toutes » réaffiche la liste complète. La catégorie sélectionnée est mise en évidence (classe `active`).

6. Chaque titre est un lien vers la page de détail de l'événement (`/evenement/{id}`), avec une **page 404** si l'événement n'existe pas.

7. Ajouter la **création de compte** et la **connexion** : tout le monde peut créer un événement, seuls les connectés s'inscrivent.

   Une fois connecté, les boutons « Connexion » et « Créer un compte » du menu sont remplacés par un bouton **Se déconnecter**.

8. Sur la page de détail, un bouton « **S'inscrire** » permet de participer à l'événement. Le nombre de places disponibles diminue et l'utilisateur est ajouté à la liste des inscrits.

9. Un utilisateur inscrit peut **se désinscrire** d'un événement.

10. Afficher le **nombre de places restantes** et un badge « **Complet** » quand il n'y en a plus, l'inscription est alors bloquée.

11. Seuls les admins (`ROLE_ADMIN`) accèdent à la page `/validate` : liste des événements « Non validé » avec un bouton **Valider**.

    Le lien « Validation » du menu n'est **affiché qu'aux admins**.

---

## Astuces

- Commandes utiles : `make:entity`, `make:form`, `make:user`, `make:security:form-login`, `make:registration-form`.
- Les commandes `make:security:form-login` et `make:registration-form` génèrent `SecurityController` et `RegistrationController`, qui **existent déjà** : supprimez le contrôleur fourni avant de lancer la commande, puis reprenez le HTML du template fourni.
- Pour donner le rôle admin à un utilisateur :

  ```bash
  php bin/console dbal:run-sql "UPDATE user SET roles = '[\"ROLE_ADMIN\"]' WHERE email = 'admin@test.fr'"
  ```

- Lister les routes : `php bin/console debug:router`

---

## `bin/console` manquant

Les fichiers `bin/console` et `bin/phpunit` ont été ajoutés au dépôt le **15/10/2026**. Si votre fork date d'avant, ils vous manquent, et `composer install` ne les recrée pas. Récupérez-les avec **l'une** des méthodes suivantes.

**Méthode 1 : synchroniser votre fork**

Sur la page GitHub de votre fork, cliquez sur **Sync fork** puis **Update branch**, puis récupérez les changements en local :

```bash
git pull
```

**Méthode 2 : réinstaller la recette Symfony** (après `composer install`)

```bash
composer recipes:install symfony/console --force
```

**Méthode 3 : créer le fichier à la main**

Créez le fichier `bin/console` avec ce contenu :

```php
#!/usr/bin/env php
<?php

use App\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

if (!is_dir(dirname(__DIR__).'/vendor')) {
    throw new LogicException('Dependencies are missing. Try running "composer install".');
}

if (!is_file(dirname(__DIR__).'/vendor/autoload_runtime.php')) {
    throw new LogicException('Symfony Runtime is missing. Try running "composer require symfony/runtime".');
}

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    $kernel = new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);

    return new Application($kernel);
};
```

Lancez ensuite les commandes avec `php bin/console ...`. Avec les méthodes 2 et 3, `bin/phpunit` reste absent : utilisez `vendor/bin/phpunit` à la place.
