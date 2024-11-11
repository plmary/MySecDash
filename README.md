
# MySecDash : My Security Dashboard

![Logo MySecDash](/Images/Logo-MySecDash.png)

Préambule : Désolé par avance, mais ce projet sera en "français". Pour autant, les écrans seront multilingues (Français et Anglais pour commencer).

Le projet "MySecDash" permet de gérer les différents projets d'un RSSI ou d'un RPCA.
Il y aura donc plusieurs Univers de disponibles :
- **L'Univers de la Continuité :**
    - *MyContinuity* : pour le moment, nous nous sommes concentrés sur la gestion des BIAs est prise en compte.
- **L'Univers de la Gestion des Cartographies des Risques IT :**
    - *MyRisk* : gestion de la Cartographie des Risques IT (conforme ISO 27005 et utilisant les bases de connaissances EBIOS).
- **L'Univers de la Gestion de la Conformité :**
    - *MyCompliance* : gestion du suivi de la mise en oeuvre des Référentiels.
- **L'Univers des Tableaux de Bord :**
    - *MySecDash* : réunis tous les indicateurs produits par les autres Univers et le suivi de Toutes les Actions liées à la Sécurité.

## Dépendances du projet

- JQuery 3.7.1
- Bootstrap 5.3.2
- bootstrap-select 1.13.14
- bootstrap-icons 1.11.2
- Summernote 0.9.0
- PHPWord 1.3.0
- PHP 8.2.4
- PostgreSQL 16

## Pré-requis

On suppose que vous avez été capable d'installer Apache, PHP, et PostgreSQL.
Dans le cas contraire, reportez vous sur des guides qui expliquent comment les installer.

## Installation rapide de MySecDash

1. Récupérer l'intégralité des sources sur *GitHub* et les installer dans le répertoire cible de votre *Apache2*.
2. Dans le répertoire `Installation`, il faut exécuter le script `php -f MySecDash-Controle_Installation.php`. Ce script contrôle les répertoires et crée les liens symboliques nécessaires.
3. **Attention :** contrôlez bien les droits sur les répertoires et fichiers.
4. A partir du répertoire `Installation`, allez dans le sous-répertoire `SQL`.
5. Ensuite, il faut exécuter le client PostgreSQL `psql`. A partir de ce client, vous pourrez exécuter les différents scripts SQL ci-après :
    1. `postgres=# \i MySecDash-0-Database-User.sql` : ce script créé l'utilisateur qui accèdera à la base **mysecdash**
    2. `postgres=# \i MySecDash-1-Database-Creation.sql`: ce script créé la base de donnée **mysecdash**
        - ou `postgres=# \i MySecDash-1-Database-Creation.sql.linux`: si vous êtes sur un environnement Linux
    3. `postgres=# \c mysecdash` : **IMPORTANT**, il est impératif de se connecter à base **mysecdash**
    4. `postgres=# \i MySecDash-2-All-Tables-Constraints.sql`: ce script créé toutes les tables et contraintes
    5. `postgres=# \i MySecDash-3-Primary-Data.sql`: ce script créé les données de base nécessaires au bon fonctionnement de l'outil **MySecDash"**
    6. `postgres=# \i MySecDash-4-Primary-Labels.sql`: ce script créé les libellés de base nécessaires au bon fonctionnement de l'outil **MySecDash"**
6. Au niveau d'Apache, il est conseillé d'utiliser un `VHOST` spécifique. Il faudra également installer un certificat, car **MySecDash** force les communications en `HTPPS`

## En route

Voilà, normalement, après tout cela, vous devriez être en mesure de pouvoir vous connecter à l'outil.

Par défaut, le nom d'utilisateur est "**root**" et le mot de passe est "**Welcome !**". Vous devez, bien sûr, changer ce mot de passe dès que possible.
