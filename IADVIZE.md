
Test Développeur PHP
====================
Ce test a été développé avec Symfony2
L'effort s'est principalement porté sur la commande qui récupère les VDM sur le site :
> - on peut configurer l'URL du site et le nombre de VDM récupéré (fichier config.yml dans src/BlogBundle/Ressources/config/)
> - l'invite de commande de la commande VDM:load demande si on veut effacer la table des posts VDM
> - le traitement est décomposé et on peut suivre la lecture des posts et leur sauvegarde en BDD


Environnement
------------
Les pré-requis sont les suivants : 
> - php version >= 5.4
>  -  modules : INTL, DomDocument
> - apache server >=2.4
> - serveur MySql >= 5.5.4
> - composer et PHPUnit installés

l'environnement utilisé pour la réalisation de ce test est : 
> - Ubuntu 14.04.1 LTS
> - php version : 5.5.9
> - mysql version : 	5.5.40-0ubuntu0.14.04.1
> - apache version : 2.4.7
> - PHPUnit 3.7.28
> - Composer version 1.0-dev
Pour installer correctement l'application, placer les sources 

Récupérer le dépôt et les dépendances du projet
---------------------------------------------------------
    $ git clone https://github.com/tonio-44/iadvize.git
	$ cd iadvize
	$ composer install
	
 Composer installera les **Vendors** nécessaires au fonctionnement de ce projet
Répondre aux questions posées par le script d'installation :

* database_driver (pdo_mysql): 
* database_host (127.0.0.1): 
* database_port (null): 
* database_name (symfony): 
* database_user (root): **utilisateur mysql **
* database_password (null): **mon_mot_de_passe**
* mailer_transport (smtp): 
* mailer_host (127.0.0.1): 
* mailer_user (null): 
* mailer_password (null): 
* locale (en): **fr**
* secret (ThisTokenIsNotSoSecretChangeIt): 
* debug_toolbar (true): 
* debug_redirects (false): 
* use_assetic_controller (true): 

Installer l'application
--------------------------
1. Avant d'exécuter Symfony2, utilisez la commande ci-dessous pour vérifier si votre système remplit tous les prérequis.

		
2. Accéder au site :
		**`http://iadvize.ltd/config.php`**
et modifier la configuration si besoin

3. Créer la base de données avec la console de Symfony2
 `$ php ./app/console doctrine:database:create`
  `Created database for connection named `symfony_ltd`
4. Créer la table des posts VDM
`$ php ./app/console doctrine:schema:update --force`
`Database schema updated successfully! "1" queries were executed`

Lecture des Posts sur le site VDM
----------------------------------------
Lancer la commande avec le gestionnaire de commande symfony

    $ php ./app/console VDM:load

 - Le gestionnaire demande si l'on souhaite effacer la table des Posts
   existants. Taper 'y' pour l'effacer.
 - Consulter la base de données pour
   vérifier la présence des posts collectés par la commande
Accéder à l'API
---------------
Accéder à l'API et effectuer quelques requêtes

   `http://iadvize.ltd/app_dev.php/api/posts`
   ` http://iadvize.ltd/app_dev.php/api/posts?author=Anonyme`
    `http://iadvize.ltd/app_dev.php/api/posts?from=2014-11-20&to=2014-11-28`
    
 Tests unitaires
-----------
Lancer les tests unitaires 

   `$ phpunit -c app/`