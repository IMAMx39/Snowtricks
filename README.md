# Projet SnowTricks Symfony OpenClassrooms

Projet 6 de la formation **PHP/Symfony** d'OpenClassrooms : Développez de A à Z le site communautaire SnowTricks !

Ce projet a été développé avec PHP **8.2.7** et Symfony **6.3.3**

## Installer le projet localement
Pour installer le projet sur votre machine, suivez ces étapes :
- Installez un environnement PHP & MySQL *(par exemple via [XAMPP](https://www.apachefriends.org/))*
- Installez [Composer](https://getcomposer.org/download/)

###  Base de données et jeu de démonstration
Créez la base de données, lancez la migration et injectez les données de démonstration :
>php bin/console doctrine:database:create

>php bin/console doctrine:migrations:migrate

>php bin/console doctrine:fixtures:load