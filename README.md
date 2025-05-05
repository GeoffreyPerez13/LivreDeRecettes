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
- git init
- git add .
- git commit -m ""
- git branch -M main
- git push -u origin main
- (git remote add origin https://github.com/GeoffreyPerez13/LivreDeRecettes.git)
- (git push -u origin master / git push -u origin main)

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


