# LivreDeRecettes
# Prérequis :
-	Installer MySQL
-	Installer PHP
-	Installer wamp

# Créer le projet Symfony :
-	composer create-project symfony/skeleton:"7.0.*" TutoSymfony
-	composer require webapp dans le dossier du projet

# Démarrer le serveur :
-	php -S localhost:8000 -t public

# Création d’un controlleur :
-	php bin/console make:controller

# Adminer :
-	Télécharger adminer et le faire glisser dans le dossier public de l’application
-	Le renommer en adminer.php
-	Vérifier que pdo_mysql est bien installé dans le fichier php.ini du projet :
o	php -m pour voir si il est bien installé
o	php –ini pour vérifier quel fichier php.ini est chargé et est à vérifier

# Créer une entité :
-	php bin/console make:entity
-	php bin/console make:migration
-	php bin/console doctrine:migrations:migrate

# Créer un formulaire :
-	php bin/console make:form
-	Dans config/packages/twig.yaml :
  o	Ajout de form_themes: ['bootstrap_5_layout.html.twig'] pour modifier le visuel des formulaires.
-	Form_row() pour afficher plusieurs champs les uns à côté des autres.
-	Form_rest() pour afficher le reste du formulaire.

# Créer des contraintes personnalisées :
-	php bin/console make:validator

# Pusher sur le repository :
- git pull origin main
- git init
- git add .
- git commit -m ""
- git push origin main

# Formulaire de contact :
-	mettre une file synchrone dans messenger.yaml
-	création d’un faux serveur mail avec mailpit
-	mailpit-windows-amd64.zip à télécharger pour windows
-	décompresser le dossier et mettre le fichier mailpit.exe dans le dossier bin
-	executer ce fichier avec .\mailpit.exe
-	dans le fichier .env, décommenter et mettre MAILER_DSN=smtp://localhost:1025
-	php bin/console make: form ContactType à placer dans un dossier créé dans le dossier src

# ORM :
-	php bin/console make:entity NomDeL’Entité
-	fieldtype = relation -> relier à la bonne entité

# Créer un utilisateur :
-	php bin/console make:user

# Créer système d’authentification :
-	php bin/console make:auth ou make:security (plus récent)

# Créer formulaire d’inscription :
-	php bin/console make:registration-form

# Intégration CSS et JS :
-	php bin/console asset-map:compile
-	Supprimer dossier assets créé
-	Symfony : serve
-	php bin/console importmap:require canvas-confetti (exemple)

# Fixtures :
-	composer require --dev orm-fixtures
-	php bin/console doctrine:fixture:load
-	php bin/console make:fixtures
-	composer --dev require fakerphp/faker
-	composer require --dev jzonta/faker-restaurant
-	php bin/console doctrine:fixtures:load -n



